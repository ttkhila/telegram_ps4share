<?php
header('Content-Type: text/html; charset=UTF-8');
//Esse arquivo � respons�vel por carregar as fun��es usadas com ajax
//Lembrar sempre de acrescentar o comando EXIT ao final da fun��o

$fx = $_POST['funcao'];
call_user_func($fx); //chama a função passada como parametro
//----------------------------------------------------------------------------------------------------------------------------
function realizaLogin(){  
    $form = $_POST['dados'];
    $u = carregaClasse('Usuario');
    //$form = explode("&", $form);
    $dados = array();
	
    foreach($form as $valor){
        $valor = explode("=", $valor);
        if(trim($valor[1]) == ''){
            $result = array(0, "Preencha os campos!");
            echo json_encode($result);
            exit;
        } 
        $dados[$valor[0]] = $valor[1];
    }
    
    $resp = $u->validaLogin($dados);
    
    if(is_null($resp)){
        $result = array(0, "Usuário/Senha Inválidos");
    } else { //LOGIN OK! Checar inatividade e banimento
	
	//checar user banido
	$banido = $u->is_banido($resp->id);
	if ($banido == 1){
		echo json_encode(array(0, "Usuário Banido! Contate a administração."));
		exit;
	}
    
	//checar user inativo
	$ativo = $u->is_ativo($resp->id);
	if ($ativo == 0){
		echo json_encode(array(0, "Usuário Inativo! Contate a administração."));
		exit;
	}
     
        $primeiro_acesso = $resp->primeiro_acesso;
        if ($primeiro_acesso == 1){
		echo json_encode(array(2, $resp->id));
		exit;
	}	
        
        session_start();
        $_SESSION['login'] = stripslashes($resp->login); //PSN ID
        $_SESSION['ID'] = $resp->id; //Usuário ID
        $result = array(1);//sucesso

       // --- LOG -> Início ---
	$log = carregaClasse('Log');
	$dt = $log->dateTimeOnline(); //date e hora no momento atual
	$usuLogin = $_SESSION['login']; $usuID = $_SESSION['ID'];
	$acao = stripslashes($usuLogin)." se logou!";
	$log->insereLog(array($usuID, $usuLogin, $dt, addslashes($acao)));
	// --- LOG -> Fim ---
    }
    echo json_encode($result); 
    exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function realizaLogout(){
	session_start();
	$usuLogin = $_SESSION['login']; $usuID = $_SESSION['ID'];
	unset($_SESSION['login']);
	unset($_SESSION['ID']);
	session_destroy();
	
	// --- LOG -> Início ---
	$log = carregaClasse('Log');
	$dt = $log->dateTimeOnline(); //date e hora no momento atual
	$acao = stripslashes($usuLogin)." se deslogou!";
	$log->insereLog(array($usuID, $usuLogin, $dt, addslashes($acao)));
	// --- LOG -> Fim ---
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function novoGrupo(){
	$dados = $_POST['dados'];
	$fechado = $_POST['fechado'];
	$selfID = $_POST['id']; 
	$moeda = $_POST['moeda'];
	$c = carregaClasse('Compartilhamento');
	$v = carregaClasse('Validacao');
	$j = carregaClasse('Jogo');
	//echo json_encode($dados);exit;
	$cont = 0;
	foreach($dados as $value){
		$parte = explode("%=%", $value);
		//if($parte[0] == 'nome') $v->set($parte[0], $parte[1])->is_required()->min_length(3, true); //NOME
		
		if(strstr($parte[0], "jogo") && strstr($parte[0], "id")){
			if($parte[0] == "jogo1_id")	 $v->set("Jogo 1", $parte[1])->is_required(); //se for jogo1, requerido
		}

		if($parte[0] == 'original1_id' || $parte[0] == 'original2_id' || $parte[0] == 'original3_id'){
			if($parte[1] == $selfID) $cont++;
			if ($parte[0] == "original1_id") $orig[1] = !empty($parte[1]) ? $parte[1] : 0; //ID original 1
			if ($parte[0] == "original2_id") $orig[2] = !empty($parte[1]) ? $parte[1] : 0; //ID original 2
			if ($parte[0] == "original3_id") $orig[3] = !empty($parte[1]) ? $parte[1] : 0; //ID original 3 (fantasma)
		}
		
		if(strstr($parte[0], "valor") && $parte[1] != "") $v->set($parte[0], str_replace(",", ".", $parte[1]))->is_float(); //VALOR
		
		//verifica duplicidade de e-mail, que significaria uma conta duplicada
		if($parte[0] == 'email' && trim($parte[1]) != ""){
			$dup = $c->checaDuplicidadeGrupo(trim($parte[1]));
			if(!$dup) $v->set('Duplicidade', '')->set_error("O e-mail informado já está em uso em outro grupo. Esse grupo já existe!");
		}
			
		if($fechado == 1){
			if($parte[0] == 'email') $v->set($parte[0], $parte[1])->is_required()->is_email(); //E-MAIL
			//checa se os valores foram preenchidos para sa vagas informadas, quando o grupo estiver fechado
			if($parte[0] == 'valor1' || $parte[0] == 'valor2' || $parte[0] == 'valor3'){ 
				$valor = substr($parte[0], -1); //armazena o numeral correspondente a vaga (1,2,3)
				if($orig[$valor] > 0) $v->set($parte[0], $parte[1])->is_required(); //VALOR
			}
		}		
	}
	
	if($cont == 0) $v->set('ID', '')->set_error("É necessário informar seu próprio ID numa das vagas do grupo.");

	if($v->validate()){
		$campos = array(); 
		$valores = array(); 
		$outrosDados = array();
		//$consolidados = array("nome", "email", "original1_id", "original2_id", "original3_id", "moeda_id", "fechado");
		$consolidados = array("email", "original1_id", "original2_id", "original3_id", "moeda_id", "fechado");
		foreach($dados as $value){
			$parte = explode("%=%", $value);
			if(in_array($parte[0], $consolidados)) { //está entre os dados que coincidem nome do campo no form com nome do campo no BD
				array_push($campos, $parte[0]);
				array_push($valores, "'".addslashes($parte[1])."'");
			} else { //dados restantes
				$outrosDados[$parte[0]] = $parte[1];
			}
		}

		array_push($campos, "criador_id");
		array_push($valores, $selfID);
		$idGrupo = $c->insereGrupo($campos, $valores);
		$soma = $c->gravaVagas($idGrupo, $orig[1], $orig[2], $orig[3], $outrosDados); //retorna a soma dos valores lançados
		
		$jogos = $j->gravaJogosCompartilhados($idGrupo, $outrosDados); //retorna um array com os NOMES dos jogos
		$u = carregaClasse("Usuario");
		$u->carregaDados($selfID);
		$nomeGrupo = $u->getIdEmail().": ";

		//monta Nome do Grupo
		foreach($jogos as $jogo){
			$nomeGrupo .= str_replace("'", " ", $jogo)." + ";
		}
		$nomeGrupo = substr_replace($nomeGrupo, "", -3);
		$tam = strlen($nomeGrupo);
		//echo json_encode($nomeGrupo);exit;
		if($tam > 96) $nomeGrupo = substr_replace($nomeGrupo, "", (96-$tam))."...";

		$c->gravaNomeGrupo($idGrupo, $nomeGrupo); //grava nome grupo
		
		if($fechado == 1){
			require_once 'funcoes.php';
			$data = date('Y-m-d');
			$moeda = between("(", ")", $moeda);
			
			$fator = 3.14; //provisório //Não está funcionando para ambiente externo dentro do banco
			//$fator = $c->converteMoeda($moeda);
			
			if ($moeda != "BRL") $valor_convertido = $soma * $fator;
			else $valor_convertido = $soma;
			$valor_convertido = str_replace(",", "", number_format($valor_convertido, 2));
			$c->gravaDadosAdicionais($idGrupo, $soma, $valor_convertido, $fator, $data);
		}
		
		//grava aviso
		$a = carregaClasse("Aviso");
		$u = carregaClasse("Usuario");
		$c->carregaDados($idGrupo);
		$nomeGrupo = stripslashes($c->getNome());
		$u->carregaDados($selfID);
		$criadorNome = stripslashes($u->getLogin());
		for($i=1; $i<=3; $i++){
			if($orig[$i] > 0 && $orig[$i] != $selfID){
				$u->carregaDados($orig[$i]);
				if($i == 3) $vagaNome = "Fantasma"; else $vagaNome = "Original ".$i;
				$texto = "O usuário <b>$criadorNome</b> criou um novo grupo <b>'$nomeGrupo'</b> e incluiu você na vaga de $vagaNome em ".date('d-m-Y').".";
				$texto = addslashes($texto);
				$a->insereAviso($orig[$i], $texto);
			}
		}
		
		// --- LOG -> Início ---
		$log = carregaClasse('Log');
		$dt = $log->dateTimeOnline(); //date e hora no momento atual
		$acao = $criadorNome." criou um novo grupo (ID: $idGrupo / NOME: $nomeGrupo)";
		$log->insereLog(array($selfID, $criadorNome, $dt, addslashes($acao)));
		// --- LOG -> Fim ---

		 echo json_encode(1);
	}else{
		 $erros = $v->get_errors();
		 echo json_encode($erros);
		// foreach ($erros as $erro){ //Percorre todos os erros
			// foreach ($erro as $err){ //Percorre cada erro do campo especifico
				// echo '<p>' . $err . '</p>';
			// }
		// }
	}
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function montaPadraoEmail(){
	$idUsuario = $_POST['id'];
	$u = carregaClasse("Usuario");
	$u->carregaDados($idUsuario);
	$idEmail = $u->getIdEmail();
	if ($idEmail == "" || empty($idEmail)){
		echo json_encode(array(1, "O usuário não possui uma ID de email cadastrada. Informar a administração"));
		exit;
	}
	$emailPadrao = "tlcw.".$idEmail."_";
	echo json_encode(array(0, $emailPadrao));
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function mostraGrupo(){
	$idGrupo = $_POST['id'];
	
	$selfID = $_POST['selfid'];
	$c = carregaClasse('Compartilhamento');
	$c2 = carregaClasse('Compartilhamento');
	$j = carregaClasse('Jogo');
	$u = carregaClasse('Usuario'); 
	$c->carregaDados($idGrupo);
	$saida = "";
	$simboloMoeda = $c->recupera_dados_moedas($c->getMoedaId())->simbolo;
	
	$nomeMoeda = stripslashes($c->recupera_dados_moedas($c->getMoedaId())->nome);
	
	if($c->getFechado() == 1) $fechado = "Sim"; else $fechado = "Não";

	if($c->getOrig1() == 0){ 
		$orig1 = "Vaga em aberto"; $orig1Nome = "Vaga em aberto"; $orig1ID = 0; $valor1 = "N/D"; $link1 = "<a name='link_vazio' href='#'>";
		$c2->carregaUltimoHistorico($idGrupo, 1);
		$aVenda1 = $c2->getAVenda();
		$classeVenda1 = ($aVenda1 == 0) ? "glyphicon glyphicon-shopping-cart btn btn-primary btn-xs" : 
			"glyphicon glyphicon-shopping-cart glyph_click btn btn-default btn-xs";
		$titleVenda1 = ($aVenda1 == 0) ? "Colocar vaga a venda" : 
			"Item já colocado a venda. Refazer irá alterar seu preço.";
	} else { 
		$u->carregaDados($c->getOrig1()); 
		$c->carregaDadosHistoricos($idGrupo, 1);
		$c2->carregaUltimoHistorico($idGrupo, 1);
		$orig1 = stripslashes($u->getLogin()); 
		$orig1Nome = stripslashes($u->getNome());
		$orig1ID = $u->getId();
		$link1 = "<a href='perfil_usuario.php?user=$orig1ID' target='_blank'>";
		$valorPago = $c->getValorPago();
		$valor1 = (!empty($valorPago)) ? $simboloMoeda." ".number_format($valorPago, 2, ',', '.') : "N/D";
		$aVenda1 = $c2->getAVenda();
		$classeVenda1 = ($aVenda1 == 0) ? "glyphicon glyphicon-shopping-cart btn btn-primary btn-xs" : 
			"glyphicon glyphicon-shopping-cart glyph_click btn btn-default btn-xs";
		$titleVenda1 = ($aVenda1 == 0) ? "Colocar vaga a venda" : 
			"Item já colocado a venda. Refazer irá alterar seu preço.";
	}
	
	if($c->getOrig2() == 0){ 
		$orig2 = "Vaga em aberto"; $orig2Nome = "Vaga em aberto"; $orig2ID = 0;  $valor2 = "N/D"; $link2 = "<a name='link_vazio' href='#'>";
		$c2->carregaUltimoHistorico($idGrupo, '2');
		$aVenda2 = $c2->getAVenda();
		$classeVenda2 = ($aVenda2 == 0) ? "glyphicon glyphicon-shopping-cart btn btn-primary btn-xs" : 
			"glyphicon glyphicon-shopping-cart glyph_click btn btn-default btn-xs";
		$titleVenda2 = ($aVenda2 == 0) ? "Colocar vaga a venda" : 
			"Item já colocado a venda. Refazer irá alterar seu preço.";
	} else { 
		$u->carregaDados($c->getOrig2()); 
		$c->carregaDadosHistoricos($idGrupo, "2");
		$c2->carregaUltimoHistorico($idGrupo, '2');
		$orig2 = stripslashes($u->getLogin());
		$orig2Nome = stripslashes($u->getNome());
		$orig2ID = $u->getId();
		$link2 = "<a href='perfil_usuario.php?user=$orig2ID' target='_blank'>";
		$valorPago = $c->getValorPago();
		$valor2 = (!empty($valorPago)) ? $simboloMoeda." ".number_format($valorPago, 2, ',', '.'): "N/D";
		$aVenda2 = $c2->getAVenda();
		$classeVenda2 = ($aVenda2 == 0) ? "glyphicon glyphicon-shopping-cart btn btn-primary btn-xs" : 
			"glyphicon glyphicon-shopping-cart glyph_click btn btn-default btn-xs";
		$titleVenda2 = ($aVenda2 == 0) ? "Colocar vaga a venda" : 
			"Item já colocado a venda. Refazer irá alterar seu preço.";
	}
	
	if($c->getOrig3() == 0){ 
		$orig3 = "Vaga em aberto"; $orig3Nome = "Vaga em aberto"; $orig3ID = 0; $valor3 = "N/D";  $link3 = "<a name='link_vazio' href='#'>";
		$c2->carregaUltimoHistorico($idGrupo, '3');
		$aVenda3 = $c2->getAVenda();
		$classeVenda3 = ($aVenda3 == 0) ? "glyphicon glyphicon-shopping-cart btn btn-primary btn-xs" : 
			"glyphicon glyphicon-shopping-cart glyph_click btn btn-default btn-xs";
		$titleVenda3 = ($aVenda3 == 0) ? "Colocar vaga a venda" : 
			"Item já colocado a venda. Refazer irá alterar seu preço.";
	} else { 
		$u->carregaDados($c->getOrig3()); 
		$c->carregaDadosHistoricos($idGrupo, "3");
		$c2->carregaUltimoHistorico($idGrupo, '3');
		$orig3 = stripslashes($u->getLogin());
		$orig3Nome = stripslashes($u->getNome()); 
		$orig3ID = $u->getId();
		$link3 = "<a href='perfil_usuario.php?user=$orig3ID' target='_blank'>";
		$valorPago = $c->getValorPago(); 
		$valor3 = (!empty($valorPago)) ? $simboloMoeda." ".number_format($valorPago, 2, ',', '.'): "N/D";
		$aVenda3 = $c2->getAVenda();
		$classeVenda3 = ($aVenda3 == 0) ? "glyphicon glyphicon-shopping-cart btn btn-primary btn-xs" : 
			"glyphicon glyphicon-shopping-cart glyph_click btn btn-default btn-xs";
		$titleVenda3 = ($aVenda3 == 0) ? "Colocar vaga a venda" : 
			"Item já colocado a venda. Refazer irá alterar seu preço.";
	}

	//identado por HTML
	$saida .= "
		<div class='panel-group col-md-8'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>Vagas/Valores originais* ($nomeMoeda) ";
					if($c->getFechado() == 0 && $selfID == $c->getCriadorId()){ //insere botão Fechar Grupo
						$saida .= "
							<div class='badge'>
								<a role='button' id='grupo_$idGrupo' name='btn-fechar-grupo' data-toggle='modal' data-target='#fecha-grupo'>Fechar Grupo</a>
								<span class='glyphicon glyphicon-info-sign' data-toggle='tooltip' data-placement='right' data-html='true' 
									title='Informa que o grupo já possui suas vagas preenchidas, com os respectivos valores dessas vagas e a conta se encontra devidamente criada na PSN/Live.'></span>
							</div>";
					}
					$saida .= "
				</div>";
		$saida .= "
				<div class='panel panel-info'>	
					<div class='panel-heading'>
						<div class='row'>
							<label class='col-sm-2'>Original 1: </label>
							<label class='col-sm-4' title='Clique para ver o perfil de $orig1Nome' style='font-weight:normal;'>$link1$orig1</a> %%opcoes1%% 
								<div name='input-valor' id='input-valor_".$idGrupo."_1' class='form-group div-input-valor'>Valor em reais (opcional):
									<button type='button' aria-label='Close' class='close' name='sp-close-input-valor' data-dismiss='div-input-valor'>
										<span aria-hidden='true'>&times;</span>
									</button>
									<input class='input-xs' type='text' id='txt-valor-venda_".$idGrupo."_1' maxlength='10' />
									<button name='btn-grupo' class='btn btn-xs btn-primary' rel='1' id='btn-grupo_".$idGrupo."_1'>Confirma</button>
								</div>
							</label>
							<label class='col-sm-3'>Valor pago: </label>
							<label class='col-sm-3' style='font-weight:normal;'>$valor1</label>
						</div>
						<div class='row'>
							<label class='col-sm-2'>Original 2: </label>
							<label class='col-sm-4' style='font-weight:normal;' title='Clique para ver o perfil de $orig2Nome'>$link2$orig2</a> %%opcoes2%%
								<div name='input-valor' id='input-valor_".$idGrupo."_2' class='form-group div-input-valor'>Valor em reais (opcional):
									<button type='button' aria-label='Close' class='close' name='sp-close-input-valor' data-dismiss='div-input-valor'>
										<span aria-hidden='true'>&times;</span>
									</button>
									<input class='input-xs' type='text' id='txt-valor-venda_".$idGrupo."_2' maxlength='10' />
									<button name='btn-grupo' class='btn btn-xs btn-primary' rel='2' id='btn-grupo_".$idGrupo."_2'>Confirma</button>
								</div> 
							</label>
							<label class='col-sm-3'>Valor pago: </label>
							<label class='col-sm-3' style='font-weight:normal;'>$valor2</label>
						</div>
						<div class='row'>
							<label class='col-sm-2'>Fantasma: </label>
							<label class='col-sm-4' style='font-weight:normal;' title='Clique para ver o perfil de $orig3Nome'>$link3$orig3</a> %%opcoes3%%
								<div name='input-valor' id='input-valor_".$idGrupo."_3' class='form-group div-input-valor'>Valor em reais (opcional):
									<button type='button' aria-label='Close' class='close' name='sp-close-input-valor' data-dismiss='div-input-valor'>
										<span aria-hidden='true'>&times;</span>
									</button>
									<input class='input-xs' type='text' id='txt-valor-venda_".$idGrupo."_3' maxlength='10' />
									<button name='btn-grupo' class='btn btn-xs btn-primary' rel='3' id='btn-grupo_".$idGrupo."_3'>Confirma</button>
								</div> 
							</label>
							<label class='col-sm-3'>Valor pago: </label>
							<label class='col-sm-3' style='font-weight:normal;'>$valor3</label>
						</div>
					</div>
				</div>
			</div>"; //close panel panel-primary"; 
		if($c->getFechado() == 1){
			$saida .= "
			<div class='panel panel-primary'>
				<div class='panel-heading'>
					<div class='row'>
						<label class='col-sm-offset-1 col-sm-3'>E-mail da conta:</label>
						<label class='col-sm-8'>".stripslashes($c->getEmail())."</label>
					</div>
				</div>
			</div>
			<div class='panel panel-default'>
				<div class='panel-heading'>
					<div class='row'>
						<label class='col-sm-6'>
							<a href='#' name='historico-grupo' data-toggle='modal' data-target='#historico' id='historico_".$c->getId()."'>Ver Histórico</a>
						</label>
						<label class='col-sm-3'>Valor Total: </label>
						<label class='col-sm-3' style='font-weight:normal;'>".$simboloMoeda." ".number_format($c->getValor(), 2, ',', '.')."</label>
					</div>";	

			if($c->getMoedaId() != 1){ //moeda estrangeira - mostrar conversão
				$saida .= "
					<div class='row'>
						<label class='col-sm-6'></label>
						<label class='col-sm-3'>Convertido(R$): </label>
						<label class='col-sm-3' style='font-weight:normal;'>R$ ".number_format($c->getValorConvertido(), 2, ',', '.')."</label>
					</div>
					<div class='row'>
						<label class='col-sm-6'></label><label class='col-sm-3'>Fator Conversão: </label>
						<label class='col-sm-3' style='font-weight:normal;'>".$simboloMoeda." 1,00 = R$ ".str_replace(".", ",", number_format($c->getFatorConversao(), 2))."</label>
					</div>";
			}
			$saida .= "
				</div>";
			}
			$saida .= "
				<div class='panel-body'>*Valores originais referentes a criação da conta sem levar em consideração os repasses da mesma.</div>
			</div>
		</div>"; //close panel-group col-md-8
		
	//recupera os jogos da conta
	$jogos = $j->getJogosGrupo($idGrupo);
	$saida .= "
		<div class='panel-group col-md-4'>
			<div class='panel panel-primary'>
				<div class='panel-heading'>Jogo(s) nesta conta:</div>
					<div class='panel-body'>";
						while($d = $jogos->fetch_object()){
							$saida .= "<span>- ".stripslashes($d->jogo)." (".$d->nome_abrev.")</span><br />";	
						}
						$saida .= "
					</div>
				</div>
			</div>
		</div>";
	
	//Opções de repasse e disponibilizar vaga
	// ORIGINAL 1   
	if($orig1ID == $selfID && $c->getFechado() == 1) 
		$opcoes1 = "&nbsp;<button class='glyphicon glyphicon-transfer glyph_click btn btn-primary btn-xs' name='img-repasse' data-id='1' data-toggle='modal' 
			data-target='#repasse' id='img-repasse_$idGrupo' rel='1' title='Informar vaga repassada'></button>&nbsp;&nbsp;
		<button class='$classeVenda1' name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_1'  
			title='$titleVenda1'></button>"; // grupo fechado. OS donos das vagas podem repassa-la ou coloca-la a venda
	else if($selfID == $c->getCriadorId() && $c->getOrig1() == 0) 
		$opcoes1 = "&nbsp;<button class='glyphicon glyphicon-transfer glyph_click btn btn-primary btn-xs' name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' 
			data-target='#repasse' id='img-repasse_$idGrupo' rel='1' title='Informar vaga repassada'></button>&nbsp;&nbsp;
		<button class='$classeVenda1' name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_1'  
			title='$titleVenda1'></button>"; //grupo aberto. O criador tem o direito de colocar uma vaga que estiver sem dono, a venda
	else if($orig1ID == $selfID  && $c->getFechado() == 0 && $selfID != $c->getCriadorId()) 
		$opcoes1 = "&nbsp;<button class='glyphicon glyphicon-transfer glyph_click btn btn-primary btn-xs' name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' 
			data-target='#repasse' id='img-repasse_$idGrupo' rel='1' title='Informar vaga repassada'></button>&nbsp;&nbsp;
		<button class='$classeVenda1' name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_1'  
			title='$titleVenda1'></button>"; //grupo aberto. O usuário pode desistir da sua vaga e a passar pra outro. O criador não pode fazer isso	
	else if($selfID == $c->getCriadorId() && $c->getFechado() == 0 && $c->getOrig1() != 0 && $c->getOrig1() != $selfID) 
		$opcoes1 = "&nbsp;<button class='glyphicon glyphicon-trash glyph_click btn btn-primary btn-xs' name='img-excluir' id='exclui-vaga_".$idGrupo."_".$c->getOrig1()."_1' rel='1' 
			title='Excluir usuário desta vaga'></button>"; //grupo aberto. O criador tem o direito de excluir um usuário e retornar a vaga para aberta
	else $opcoes1 = ""; 

	// ORIGINAL 2
	if($orig2ID == $selfID && $c->getFechado() == 1) 
		$opcoes2 = "&nbsp;<button class='glyphicon glyphicon-transfer glyph_click btn btn-primary btn-xs' name='img-repasse' data-id='1' data-toggle='modal' 
			data-target='#repasse' id='img-repasse_$idGrupo' rel='2' title='Informar vaga repassada'></button>&nbsp;&nbsp;
		<button class='$classeVenda2' name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_2'  
			title='$titleVenda2'></button>";  // grupo fechado. OS donos das vagas podem repassa-la ou coloca-la a venda
	else if(($selfID == $c->getCriadorId() || $c->getOrig1() == $selfID) && $c->getOrig2() == 0) 
		$opcoes2 = "&nbsp;<button class='glyphicon glyphicon-transfer glyph_click btn btn-primary btn-xs' name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' 
			data-target='#repasse' id='img-repasse_$idGrupo' rel='2' title='Informar vaga repassada'></button>&nbsp;&nbsp;
		<button class='$classeVenda2' name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_2'  
			title='$titleVenda2'></button>"; //grupo aberto. O criador tem o direito de colocar uma vaga que estiver sem dono, a venda
	else if($orig2ID == $selfID  && $c->getFechado() == 0 && $selfID != $c->getCriadorId()) 
		$opcoes2 = "&nbsp;<button class='glyphicon glyphicon-transfer glyph_click btn btn-primary btn-xs' name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' 
			data-target='#repasse' id='img-repasse_$idGrupo' rel='2' title='Informar vaga repassada'></button>&nbsp;&nbsp;
		<button class='$classeVenda2' name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_2'  
			title='$titleVenda2'></button>"; //grupo aberto. O usuário pode desistir da sua vaga e a passar pra outro. O criador não pode fazer isso	
	else if($selfID == $c->getCriadorId() && $c->getFechado() == 0 && $c->getOrig2() != 0 && $c->getOrig2() != $selfID) 
		$opcoes2 = "&nbsp;<button class='glyphicon glyphicon-trash glyph_click btn btn-primary btn-xs' name='img-excluir' id='exclui-vaga_".$idGrupo."_".$c->getOrig2()."_2' rel='2' 
			title='Excluir usuário desta vaga'></button>"; //grupo aberto. O criador tem o direito de excluir um usuário e retornar a vaga para aberta
	else $opcoes2 = "";
	
	// FANTASMA
	if($orig3ID == $selfID && $c->getFechado() == 1) 
		//$opcoes3 = "&nbsp;<button class='glyphicon glyphicon-transfer glyph_click btn btn-primary btn-xs' name='img-repasse' data-id='1' data-toggle='modal' 
			//data-target='#repasse' id='img-repasse_$idGrupo' rel='3' title='Informar vaga repassada'></button>&nbsp;&nbsp;
		$opcoes3 = "<button class='$classeVenda3' name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_3'  
			title='$titleVenda3'></button>"; //grupo fechado. OS donos das vagas podem repassa-la ou coloca-la a venda
	else if(($selfID == $c->getCriadorId() || $c->getOrig1() == $selfID) && $c->getOrig3() == 0 && $c->getFechado() == 0) 
		$opcoes3 = "&nbsp;<button class='glyphicon glyphicon-transfer glyph_click btn btn-primary btn-xs' name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' 
			data-target='#repasse' id='img-repasse_$idGrupo' rel='3' title='Informar vaga repassada'></button>&nbsp;&nbsp;
		<button class='$classeVenda3' name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_3'  
			title='$titleVenda3'></button>"; //grupo aberto. O criador tem o direito de colocar uma vaga que estiver sem dono, a venda	
	else if ($c->getFechado() == 1 && $c->getOrig3() == 0 && $c->getOrig1() == $selfID)
		$opcoes3 = "&nbsp;<button class='glyphicon glyphicon-transfer glyph_click btn btn-primary btn-xs' name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' 
			data-target='#repasse' id='img-repasse_$idGrupo' rel='3' title='Informar vaga repassada'></button>&nbsp;&nbsp;
		<button class='$classeVenda3' name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_3'  
			title='$titleVenda3'></button>"; //Grupo fechado, Fantasma em aberto. Só Original 1 pode vender o fantasma. SITUAÇÃO EXCEPCIONAL DO FANTASMA		
	else if($orig3ID == $selfID  && $c->getFechado() == 0 && $selfID != $c->getCriadorId()) 
		$opcoes3 = "&nbsp;<button class='glyphicon glyphicon-transfer glyph_click btn btn-primary btn-xs' name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' 
			data-target='#repasse' id='img-repasse_$idGrupo' rel='3' title='Informar vaga repassada'></button>&nbsp;&nbsp;
		<button class='$classeVenda3' name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_3'  
			title='$titleVenda3'></button>"; //grupo aberto. O usuário pode desistir da sua vaga e a passar pra outro. O criador não pode fazer isso	
	else if($selfID == $c->getCriadorId() && $c->getFechado() == 0 && $c->getOrig3() != 0 && $c->getOrig3() != $selfID) 
		$opcoes3 = "&nbsp;<button class='glyphicon glyphicon-trash glyph_click btn btn-primary btn-xs' name='img-excluir' id='exclui-vaga_".$idGrupo."_".$c->getOrig3()."_3' rel='3' 
			title='Excluir usuário desta vaga'></button>"; //grupo aberto. O criador tem o direito de excluir um usuário e retornar a vaga para aberta
	else $opcoes3 = "";
	$saida = str_replace("%%opcoes1%%", $opcoes1, $saida);	
	$saida = str_replace("%%opcoes2%%", $opcoes2, $saida);
	$saida = str_replace("%%opcoes3%%", $opcoes3, $saida);
	
	echo json_encode($saida);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function mostraHistorico(){
	$idGrupo = $_POST['id'];
	$c = carregaClasse("Compartilhamento");
	$c->carregaDados($idGrupo);
	$dadosIniciais = $c->getDadosHistoricoInicial($idGrupo);
	$dadosHist = $c->getDadosHistorico($idGrupo);
	$saida = "";
	$saida .= "<table class='table'><thead>";
	$saida .= "<tr><th colspan=4 style='background-color:#28720F; color:#fff'>Grupo: ".stripslashes($c->getNome())."</th></tr>";
	$saida .= "<tr><th width='40%'>Linha do Tempo</th><th width='20%'>Original 1</th><th width='20%'>Original 2</th><th width='20%'>Fantasma</th></tr></thead>";
	$saida .= "<tbody><tr>";
	$cont = 0;
	while($d = $dadosIniciais->fetch_object()){ //dados da criação da conta
		if($cont == 0){
			$phpdate = strtotime($d->data_venda);
			$data_venda = date( 'd-m-Y', $phpdate );
			$saida .= "<td>$data_venda (criação da conta)</td>";
		}
		if($d->comprador_id == 0) $saida .= "<td>Vaga em aberto</td>"; //vaga não foi vendida no fechamento do grupo
		else $saida .= "<td title='".stripslashes($d->nome)."'>".stripslashes($d->login)."</td>";
		$cont ++;
	}
	$saida .= "</tr>";
	
	if($dadosHist->num_rows > 0){ //a conta já foi repassada ao menos uma vez depois da criação
		while($d = $dadosHist->fetch_object()){ //dados do histórico da conta já repassada
			if($d->senha_alterada == 1) $img = "<img src='img/senha_alterada.jpg' title='alterou senha' />"; else $img = "";
			$phpdate = strtotime($d->data_venda);
			$data_venda = date( 'd-m-Y', $phpdate );
			$saida .= "<tr><td>$data_venda</td>";
			if($d->vaga == '1') { //Original 1
				$saida .= "<td title='".stripslashes($d->nome_comprador)."'>".stripslashes($d->login_comprador)." $img</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
			} else if($d->vaga == '2') { //Original 2
				$saida .= "<td>&nbsp;</td><td title='".stripslashes($d->nome_comprador)."'>".stripslashes($d->login_comprador)." $img</td><td>&nbsp;</td></tr>";
			} else if($d->vaga == '3') { //Fantasma
				$saida .= "<td>&nbsp;</td><td>&nbsp;</td><td title='".stripslashes($d->nome_comprador)."'>".stripslashes($d->login_comprador)." $img</td></tr>";
			}	
		}
	}
	$saida .= "</tbody>";
	$saida .= "</table>";
	$saida .= "<div><img src='img/senha_alterada.jpg' /> = Informa que a senha foi alterada.</div>";
	
	echo json_encode($saida);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function gravaRepasse(){
	session_start();
	$idGrupo = $_POST['grupo'];
	$vaga = $_POST['vaga'];
	$compradorID = $_POST['comprador'];
	$valor = $_POST['valor'];
	$data_venda = $_POST['data_venda'];
	$vendedor = $_SESSION['ID'];
	$alterou_senha = $_POST['alterou_senha'];
	//echo json_encode("--".$idGrupo."--"); exit;
	$v = carregaClasse('Validacao');
	$c = carregaClasse('Compartilhamento');
	//echo json_encode("GRUPO ".$idGrupo." / vaga ".$vaga." / comprador ".$compradorID." / valor ".$valor." / Data ".$data_venda." / Vendedor ".$vendedor);
	$ret = $c->is_thisGroup($vendedor, $idGrupo, $vaga);
	if(!$ret){
		$v->set('Comprador', '')->set_error("Falha na autenticação do usuário.");
		 $erros = $v->get_errors();
		 echo json_encode($erros); 
		 exit;
	}
	$v->set("Comprador", $compradorID)->is_required(); //Comprador ID
	if($compradorID === $vendedor) $v->set('Comprador', '')->set_error("O comprador deve ser diferente do vendedor.");
	$v->set("Valor", str_replace(",", ".", $valor))->is_required()->is_float(); //VALOR
	$v->set("Data", $data_venda)->is_date(); // Data Venda
	//echo json_encode("dwqqwdqwdq"); exit;
	
	if($v->validate()){
		$ret = $c->gravaRepasse($idGrupo, $vendedor, $compradorID, $vaga, $valor, $data_venda, $alterou_senha);
		
		// Faz abertura de registro de recomendação
		$r = carregaClasse("Recomendacao");
		$c->carregaUltimoHistorico($idGrupo, $vaga);
		$historicoID = $c->getHistoricoId();
		$r->abreRecomendacao($historicoID, $compradorID, $vendedor);

		// *** GRAVA AVISO - INÍCIO *** //
		$a = carregaClasse("Aviso");
		$u = carregaClasse("Usuario");
		$c->carregaDados($idGrupo);
		$vendedorLogin = $_SESSION["login"];
		$nomeGrupo = stripslashes($c->getNome());
		$u->carregaDados($compradorID);
		$compradorLogin = $u->getLogin();
		if ($vaga == 1){ 
			$vagaNome = "Original 1";
			$orig2 = $c->getOrig2();
			if($orig2 == $vendedor) $outroOriginal = 0; //se vendedor e dono do Original 2 forem a mesma pessoa, não percisa avisar
			else $outroOriginal = $orig2; //avisa o Original 2 sobre o repasse
		}
		else if ($vaga == 2){ 
			$vagaNome = "Original 2"; 
			$orig1 = $c->getOrig1();
			if($orig1 == $vendedor) $outroOriginal = 0; //se vendedor e dono do Original 1 forem a mesma pessoa, não percisa avisar
			else $outroOriginal = $orig1; //avisa o Original 1 sobre o repasse
		}
		else {
			$vagaNome = "Fantasma";
			$orig1 = $c->getOrig1();
			$orig2 = $c->getOrig2();
			if($orig1 == $vendedor) $orig1 = 0;
			if($orig2 == $vendedor) $orig2 = 0;
			$outroOriginal = array($orig1, $orig2);
		}
		//echo json_encode($outroOriginal); exit;
		$texto = "O usuário <b>$vendedorLogin</b> repassou a vaga de $vagaNome da conta <b>'$nomeGrupo'</b> para você em ".date('d-m-Y', strtotime($data_venda)).".";
		$textoOutroOriginal = "O usuário <b>$vendedorLogin</b> repassou a vaga de $vagaNome da conta <b>'$nomeGrupo'</b>, da qual você faz parte, para <b>$compradorLogin</b> em ".date('d-m-Y', strtotime($data_venda)).".";
		$texto = addslashes($texto);
		$textoOutroOriginal = addslashes($textoOutroOriginal);
		$a->insereAviso($compradorID, $texto); //envia aviso ao destinatário da vaga
		$a->insereAviso($outroOriginal, $textoOutroOriginal); //envia aviso ao(s) outro(s) original(is)
		// *** GRAVA AVISO - FIM *** //
		
		// --- LOG -> Início ---
		$log = carregaClasse('Log');
		$dt = $log->dateTimeOnline(); //date e hora no momento atual
		$acao = $vendedorLogin." repassou (VAGA: '$vagaNome' / GRUPO: '$nomeGrupo' / PARA: $compradorLogin)";
		$log->insereLog(array($vendedor, $vendedorLogin, $dt, addslashes($acao)));
		// --- LOG -> Fim ---

		if($ret) echo json_encode(1);
		else echo json_encode($ret);
	}else{
		 $erros = $v->get_errors();
		 echo json_encode($erros);
	}
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function gravaDisponibilidadeVaga(){
	session_start();
	$idGrupo = $_POST['id'];	
	$valor = $_POST['valor'];
	$vaga = $_POST['vaga'];
	$usuarioID = $_SESSION['ID'];
	$c = carregaClasse('Compartilhamento');
	$v = carregaClasse('Validacao');
	//echo json_encode($vaga); exit;
	if(trim($valor) != "") $v->set("valor", str_replace(",", ".", $valor))->is_float(); //VALOR
	
	if($v->validate()){
		$c->gravaDisponibilidadeVaga($idGrupo, $vaga, $valor, $usuarioID);
		
		$c->carregaDados($idGrupo);
		// --- LOG -> Início ---
		$log = carregaClasse('Log');
		$dt = $log->dateTimeOnline(); //date e hora no momento atual
		$usuLogin = $_SESSION['login'];
		$vagaNome = $c->getNomeVaga($vaga, 1);
		$nomeGrupo = stripslashes($c->getNome());
		$acao = $usuLogin." disponibilizou para venda (VAGA: '$vagaNome' / GRUPO: '$nomeGrupo')";
		$log->insereLog(array($usuarioID, $usuLogin, $dt, addslashes($acao)));
		// --- LOG -> Fim ---

		echo json_encode(1);
	}else{
		 $erros = $v->get_errors();
		 echo json_encode($erros);
	}
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function mostraGrupoAntigo(){
	$idHist = $_POST['id'];
	$selfID = $_POST['selfid'];
	$c = carregaClasse('Compartilhamento');
	$j = carregaClasse('Jogo');
	$u = carregaClasse('Usuario'); 
	$c->carregaDadosHistoricos($idHist);
	//VAGA
	$numVaga = $c->getVaga();
	$nomeVaga = $c->getNomeVaga($numVaga, 1);
	//DATA VENDA
	$data_venda = $c->getDataVenda();
	$phpdate = strtotime($data_venda);
	$data_venda = date( 'd/m/Y', $phpdate );
	//VALOR PAGO
	$valor_pago = $c->getValorPago();
	$valor_pago = number_format($valor_pago, 2, ',', '.');
	//Comprador
	$comprador_id = $c->getCompradorId();
	$u->carregaDados($comprador_id);
	$compradorLogin = stripslashes($u->getLogin());
	
	//Dados do Grupo
	$idGrupo = $c->getCompartilhamentoId();
	$c->carregaDados($idGrupo);
	$orig1 = $c->getOrig1();
	$orig2 = $c->getOrig2();
	$orig3 = $c->getOrig3();
	
	//Original 1
	if ($orig1 == 0){ $orig1Login = "Vaga em aberto"; $link1 = "<a name='link_vazio' href='#'>"; }
	else{
		$u->carregaDados($orig1);
		$orig1Login = stripslashes($u->getLogin());
		$link1= "<a href='perfil_usuario.php?user=$orig1' target='_blank'>";
	}
	//Original 2
	if ($orig2 == 0){ $orig2Login = "Vaga em aberto"; $link2 = "<a name='link_vazio' href='#'>"; }
	else{
		$u->carregaDados($orig2);
		$orig2Login = stripslashes($u->getLogin());
		$link2= "<a href='perfil_usuario.php?user=$orig2' target='_blank'>";
	}
	//Fantasma
	if ($orig3 == 0){ $orig3Login = "Vaga em aberto"; $link3 = "<a name='link_vazio' href='#'>"; }
	else{
		$u->carregaDados($orig3);
		$orig3Login = stripslashes($u->getLogin());
		$link3= "<a href='perfil_usuario.php?user=$orig3' target='_blank'>";
	}
	
	//JOGOS
	$jogos = $j->getJogosGrupo($idGrupo);
	
	$saida = "";

	$saida .= "
		<div class='panel-group'>
			<div class='col-md-8'>
				<div class='panel panel-primary'>
					<div class='panel-heading'>Proprietários atuais das vagas deste grupo</div>
					<div class='panel-body'>
						<div class='row'><label class='col-sm-2 col-sm-offset-1'>Original 1:</label><label title='Clique para ver o perfil de $orig1Login' class='col-sm-3'>$link1$orig1Login</a></label></div>
						<div class='row'><label class='col-sm-2 col-sm-offset-1'>Original 2:</label><label title='Clique para ver o perfil de $orig2Login' class='col-sm-3'>$link2$orig2Login</a></label></div>
						<div class='row'><label class='col-sm-2 col-sm-offset-1'>Fantasma:</label><label title='Clique para ver o perfil de $orig3Login' class='col-sm-3'>$link3$orig3Login</a></label></div>
					</div>
				</div>
				<div class='panel panel-danger'>
					<div class='panel-heading'>
						Você foi proprietário da vaga de <b>$nomeVaga</b> e repassou para<b>
						<a href='perfil_usuario.php?user=$comprador_id' target='_blank' title='Clique para ver o perfil de $compradorLogin'>$compradorLogin</a>
						</b> em $data_venda por R$ $valor_pago
					</div>
				</div>
			</div>
			
			<div class='col-md-4'>
				<div class='panel panel-primary'>
					<div class='panel-heading'>Jogo(s) nesta conta:</div>
					<div class='panel-body'>";
						while($d = $jogos->fetch_object()){
							$saida .= "<span>- ".stripslashes($d->jogo)." (".$d->nome_abrev.")</span><br />";	
						}
						$saida .= "
					</div>
				</div>
			</div>
		</div>
	";
	echo json_encode($saida);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function alteraValorVendaVaga(){
	session_start();
	$idHist = $_POST['id'];	
	$valor = $_POST['valor'];
	$logado = $_SESSION['ID'];
	$c = carregaClasse('Compartilhamento');
	$v = carregaClasse('Validacao');
	
	//autenticação anti-fraude
	$fraude = $c->is_thisHistory($idHist, $logado);
	if(!$fraude){ echo json_encode(array("valor" => array("Erro na autenticação do usuário."))); exit; }
	
	if(trim($valor) != "") $v->set("valor", str_replace(",", ".", $valor))->is_float(); //VALOR

	if($v->validate()){
		$c->gravaAlteracaoValorVenda($idHist, $valor);
		echo json_encode(1);
	}else{
		 $erros = $v->get_errors();
		 echo json_encode($erros);
	}
	exit; 
}
//----------------------------------------------------------------------------------------------------------------------------
	function excluiVenda(){
		session_start();
		$idHist = $_POST['id'];	
		$logado = $_SESSION['ID'];
		$c = carregaClasse('Compartilhamento');	
		
		//autenticação anti-fraude
		$fraude = $c->is_thisHistory($idHist, $logado);
		if(!$fraude){ echo json_encode("Erro na autenticação do usuário."); exit; }
		
		$c->excluiVenda($idHist);
		
		// --- LOG -> Início ---
		$log = carregaClasse('Log');
		$dt = $log->dateTimeOnline(); //date e hora no momento atual
		$usuLogin = $_SESSION['login'];
		$c->carregaDadosHistoricos($idHist);
		$idGrupo = $c->getCompartilhamentoId();
		$c->carregaDados($idGrupo);
		$vaga = $c->getVaga();
		$vagaNome = $c->getNomeVaga($vaga, 1);
		$nomeGrupo = stripslashes($c->getNome());
		$acao = $usuLogin." excluiu venda sem repasse (VAGA: '$vagaNome' / GRUPO: '$nomeGrupo')";
		$log->insereLog(array($logado, $usuLogin, $dt, addslashes($acao)));
		// --- LOG -> Fim ---
		
		echo json_encode(1);
		exit;
	}
//----------------------------------------------------------------------------------------------------------------------------
function excluiUsuarioVaga(){
	$idGrupo = $_POST['grupo'];	
	$idUsuario = $_POST['user'];
	$vaga = $_POST['vaga'];
	$c = carregaClasse('Compartilhamento');
	
	$ret = $c->excluiUsuarioVaga($idGrupo, $vaga, $idUsuario);
	
	//grava aviso
	$a = carregaClasse("Aviso");
	$u = carregaClasse("Usuario");
	$c->carregaDados($idGrupo);
	$criadorID = $c->getCriadorId();
	$nomeGrupo = stripslashes($c->getNome());
	$u->carregaDados($criadorID);
	$criadorNome = stripslashes($u->getLogin());
	if ($vaga == 1) $vagaNome = "Original 1";
	else if ($vaga == 2) $vagaNome = "Original 2";
	else $vagaNome = "Fantasma";
	$texto = "Você foi retirado da vaga de $vagaNome do grupo aberto <b>'$nomeGrupo'</b> por <b>$criadorNome</b> em ".date('d-m-Y').".";
	$texto = addslashes($texto);
	$a->insereAviso($idUsuario, $texto);
	
	$u2 = carregaClasse("Usuario");
	$u2->carregaDados($idUsuario);
	$loginExcluido = stripslashes($u2->getLogin());
	// --- LOG -> Início ---
	$log = carregaClasse('Log');
	$dt = $log->dateTimeOnline(); //date e hora no momento atual
	$acao = $criadorNome." excluiu usuario de grupo aberto (VAGA: '$vagaNome' / GRUPO: '$nomeGrupo' / EXCLUÍDO: $loginExcluido)";
	$log->insereLog(array($criadorID, $criadorNome, $dt, addslashes($acao)));
	// --- LOG -> Fim ---

	echo json_encode($ret);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function gravaJogo(){
	$dados = $_POST['dados'];
	$j = carregaClasse('Jogo');
	$dado = array(); //Ordem dos valores (NOME, PLATAFORMA)
	foreach($dados as $valor){
		$valor = explode("=", $valor);
		if (trim($valor[1]) == ""){ echo json_encode(array(1, "Preencha os campos")); exit; }
		array_push($dado, rawurldecode($valor[1]));
	}
	//echo json_encode($dado);
	$j->gravaJogo($dado);
	echo json_encode(array(0, "Jogo Cadastrado"));
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function alteraJogo(){
	$dados = $_POST['dados'];
	$j = carregaClasse('Jogo');
	$dado = array(); //Ordem dos valores (ID, NOME, PLATAFORMA)
	foreach($dados as $valor){
		$valor = explode("=", $valor);
		if (trim($valor[1]) == ""){ echo json_encode(array(1, "Preencha os campos")); exit; }
		array_push($dado, rawurldecode($valor[1]));
	}
	//echo json_encode($dado);exit;
	$j->alteraJogo($dado);
	echo json_encode(array(0, "Jogo Alterado"));
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function ativaInativaJogo(){
	$id = $_POST['id'];
	$flag = $_POST['flag'];
	$j = carregaClasse('Jogo');
	$j->carregaDados($id);
	echo json_encode($j->ativo_inativo_alterna($flag));
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function mostraFechamentoGrupo(){
	$idGrupo = $_POST['id'];
	$c = carregaClasse("Compartilhamento");
	$j = carregaClasse('Jogo');
	$u = carregaClasse("Usuario");

	$c->carregaDados($idGrupo);
	$dados = $c->getDadosFechamento();

	$nomeConta = stripslashes($c->getNome());
	$moeda = $c->getMoedaId();

	if($dados["email"] != "" && !empty($dados["email"])) $email = $dados["email"];

	if($dados["orig1"] > 0){
		$c->carregaDadosHistoricos($idGrupo, 1);
		$u->carregaDados($dados["orig1"]);
		$valorPago = $c->getValorPago();
		if(empty($valorPago)) $valor1 = "";
		else $valor1 = number_format($valorPago, 2, ',', '.');
		$login1 = stripslashes($u->getLogin());
		$id1 = $dados["orig1"];
		$disable1 = "";
	} else {
		$valor1 = "";
		$login1 = "Vaga em Aberto";
		$id1 = 0;
		$disable1 = "disabled";
	}

	if($dados["orig2"] > 0){
		$c->carregaDadosHistoricos($idGrupo, 2);
		$u->carregaDados($dados["orig2"]);
		$valorPago = $c->getValorPago();
		if(empty($valorPago)) $valor2 = "";
		else $valor2 = number_format($valorPago, 2, ',', '.');
		$login2 = stripslashes($u->getLogin());
		$id2 = $dados["orig2"];
		$disable2 = "";
	} else {
		$valor2 = "";
		$login2 = "Vaga em Aberto";
		$id2 = 0;
		$disable2 = "disabled";
	}

	if($dados["orig3"] > 0){
		$c->carregaDadosHistoricos($idGrupo, 3);
		$u->carregaDados($dados["orig3"]);
		$valorPago = $c->getValorPago();
		if(empty($valorPago)) $valor3 = "";
		else $valor3 = number_format($valorPago, 2, ',', '.');
		$login3 = stripslashes($u->getLogin());
		$id3 = $dados["orig3"];
		$disable3 = "";
	} else {
		$valor3 = "";
		$login3 = "Vaga em Aberto";
		$id3 = 0;
		$disable3 = "disabled";
	}

	$jogos = $j->getJogosGrupo($idGrupo);
	$moedas = $c->recupera_moedas();
//<input type='text' class='form-control' name='nome-fechamento' id='nome-fechamento' maxlength='50' required='' placeholder='Nome da conta' value='$nomeConta' />
	$saida = "
	<input type='hidden' id='id-grupo-fechamento' value='$idGrupo' />
	<h5 class='label-personalizada'>Para incluir ou alterar usuários nas vagas, feche essa tela e use a anterior.</h5>
	<div class='form-group'>
		<label class='control-label'>Nome da conta:</label>
		<label class='label-info'>$nomeConta</label>
	</div>
	<div class='form-group'>
		<label class='control-label'>E-mail da conta:</label>";
		if(!isset($email)){	$saida .= "<input type='email' class='form-control' name='email-fechamento' id='email-fechamento' 
				placeholder='E-mail da conta' required='' />
				<button class='glyphicon glyphicon-hand-up btn btn-xs btn-warning' id='btn-email-padrao' title='Preenche parte do e-mail padrão para criação do grupo'> Colocar E-mail no padrão</button>";
		} else $saida .= "<label class='label-info'>$email</label>";
		
	$saida .= "
	</div>
	<div class='form-group'>
		<label class='control-label'>Moeda de Compra</label>
		<select class='form-control' id='moedas-fechamento' name='moedas-fechamento'>";                            
			while($m = $moedas->fetch_object()){
				if($m->id === $moeda) $saida .= "<option value='".$m->id."' selected='selected'>".stripslashes($m->nome)." (".$m->pais.")</option>";
				else $saida .= "<option value='".$m->id."'>".stripslashes($m->nome)." (".$m->pais.")</option>";
			}
		$saida .= "
		</select>
	</div>
	<h4>JOGOS:</h4>";
	$cont=1;
	while($d = $jogos->fetch_object()){
		$saida .= "<label class='control-label'>Jogo $cont:</label>
			<label id='jogo-fechamento_".$d->idJogo."'>".$d->jogo." (".$d->nome_abrev.")</label><br />";
		$cont++;
	}
	$saida .= "<br />
	<h4>VAGAS:</h4>
	<div class='col-md-12'>
		<div class='form-group'>
			<label class='control-label col-md-3'>Original 1:</label>
			<input type='hidden' id='id1-fechamento' value='$id1' />
			<label class='control-label col-md-4'>$login1</label>
			<label class='control-label col-md-offset-1 col-xs-1'>Valor:</label>
			<div class='col-md-3'> 
				<input type='text' class='form-control' name='valor-fechamento-1' id='valor-fechamento-1' maxlength='10' value='$valor1' $disable1 />
			</div>
		</div>
	</div>
	<div class='col-md-12'>
		<div class='form-group'>
			<label class='control-label col-md-3'>Original 2:</label>
			<input type='hidden' id='id2-fechamento' value='$id2' />
			<label class='control-label col-md-4'>$login2</label>
			<label class='control-label col-md-offset-1 col-xs-1'>Valor:</label>
			<div class='col-md-3'> 
				<input type='text' class='form-control' name='valor-fechamento-2' id='valor-fechamento-2' maxlength='10' value='$valor2' $disable2 />
			</div>
		</div>
	</div>
	<div class='col-md-12'>
		<div class='form-group'>
			<label class='control-label col-md-3'>Fantasma:</label>
			<input type='hidden' id='id3-fechamento' value='$id3' />
			<label class='control-label col-md-4'>$login3</label>
			<label class='control-label col-md-offset-1 col-xs-1'>Valor:</label>
			<div class='col-md-3'> 
				<input type='text' class='form-control' name='valor-fechamento-3' id='valor-fechamento-3' maxlength='10' value='$valor3' $disable3 />
			</div>
		</div>
	</div>
	<div>
		<label class='control-label'>Alterou a senha? <input type='checkbox' name='alterou_senha-fechamento' id='alterou_senha-fechamento' /></label>
		<p class='bg-danger' id='sp-erro-msg-modal2' style='display:none;'></p>
	</div>
	<div class='modal-footer'>
		<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
		<button type='button' id='btn-confirma-fechamento' class='btn btn-primary'>Confirmar Fechamento</button>
	</div>";
	echo json_encode($saida);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function gravaFechamentoGrupo(){
	$campos = $_POST['campos'];
	$valores = $_POST['valores'];
	$idGrupo = $_POST['id'];
	$moeda_id = $_POST['moeda'];

	$campos_conta = array("email", "moeda_id");
	$campos_historico = array("valor1", "valor2", "valor3", "senha_alterada");
	$campos_conta_result = array();
	$campos_historico_result = array();

	$c = carregaClasse('Compartilhamento');
	$j = carregaClasse('Jogo');
	
	// 'monta' um array ($campos_conta_result) com campos%=%valores
	// 'monta' um array ($campos_historico_result) com campos%=%valores
	foreach ($campos as $key => $value) {
		//verifica duplicidade de e-mail, que significaria uma conta duplicada
		if($value == 'email' && trim($valores[$key]) != ""){
			$dup = $c->checaDuplicidadeGrupo(trim($valores[$key]));
			if(!$dup) {
				echo json_encode("O e-mail informado já está em uso em outro grupo. Esse grupo já existe!");
				exit;
			}
		}
		//Compartilhamento (conta)
		if(in_array($value, $campos_conta)){
			if(trim($valores[$key]) == "") {
				echo json_encode("Campos obrigatórios não preenchidos!");
				exit;
			}
			$value .= "%=%".$valores[$key];
			array_push($campos_conta_result, $value);
		}
		//Histórico
		if(in_array($value, $campos_historico)){
			if(trim($valores[$key]) == "") {
				echo json_encode("Campos obrigatórios não preenchidos!");
				exit;
			}
			$value .= "%=%".$valores[$key];
			array_push($campos_historico_result, $value);
		}
	}
	//grava histórico (alteração)
	$valorTotal = $c->gravaHistoricoFechamento($idGrupo, $campos_historico_result);
	$data = date('Y-m-d');

	//$fator = 3.14; //provisório //Não está funcionando para ambiente externo dentro do banco
	$moeda = $c->recupera_dados_moedas($moeda_id);
	if($moeda_id == 1) $fator = 1;//real
	else $fator = 3.14; //$fator = $c->converteMoeda($moeda->pais);

	$valor_convertido = $valorTotal * $fator;
	$valor_convertido = str_replace(",", "", number_format($valor_convertido, 2));

	$c->gravaDadosAdicionais($idGrupo, $valorTotal, $valor_convertido, $fator, $data);

	$c->gravaGrupoFechamento($idGrupo, $campos_conta_result);
	
	$u = carregaClasse("Usuario");
	// --- LOG -> Início ---
	$log = carregaClasse('Log');
	$dt = $log->dateTimeOnline(); //date e hora no momento atual
	$c->carregaDados($idGrupo);
	$criadorID = $c->getCriadorId();
	$nomeGrupo = stripslashes($c->getNome());
	$u->carregaDados($criadorID);
	$loginCriador = stripslashes($u->getLogin());
	$acao = $loginCriador." fechou um grupo aberto (GRUPO: '$nomeGrupo')";
	$log->insereLog(array($criadorID, $loginCriador, $dt, addslashes($acao)));
	// --- LOG -> Fim ---
	
	echo json_encode(1);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function mostraNegativaIndicacao(){
	$idIndicacao = $_POST['id'];
	$idIndicador = $_POST['indicador'];
	$u = carregaClasse("Usuario");
	$u->carregaDados($idIndicador);
	$indicacao = $u->getDadosIndicacao($idIndicacao);
	
	$login = stripslashes($u->getLogin());
	$tela = "
		<form role='form'>
			<input type='hidden' id='indicacao_id' value='$idIndicacao' />
			<div class='alert alert-warning'>
				Informe motivo da recusa da indicação.<br /><b>Aviso:</b> O indicado será informado por e-mail e o indicador por aviso dentro do sistema.
			</div>
			<div class='form-group'>
				<label>Indicado: </label>
				<label>".stripslashes($indicacao->nome)."</label>
			</div>
			<div class='form-group'>
				<label>Indicador: </label>
				<label>".$login."</label>
			</div>
			<div class='form-group'>
				<label>Motivo da recusa:</label>
				<textarea class='form-control' maxlength='250' id='txtTexto' autofocus required></textarea>
				<small>Máximo de 250 caracteres</small>
				<p class='bg-danger' id='sp-erro-msg-modal' style='display:none;'></p>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
				<button type='submit' id='btn-confirma-negativa-indicacao' class='btn btn-primary'>Confirmar</button>
			</div>
		</form>
	";
	echo json_encode($tela);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function gravaRecusaIndicacao(){
	session_start();
	$indicacaoID = $_POST['indicacao'];
	$texto = $_POST['texto'];
	
	if (trim($texto) == ""){ echo json_encode("Preencha o campo do motivo da recusa."); exit; }
	
	$texto = addslashes($texto);
	$u = carregaClasse("Usuario");
	$u->gravaRecusaIndicacao($indicacaoID, $texto);

	//grava aviso
	$indicacao = $u->getDadosIndicacao($indicacaoID); //dados da indicação
	$indicadoNome = stripslashes($indicacao->nome);
	$indicadoPor = $indicacao->indicado_por;
	$a = carregaClasse("Aviso");
	$texto = "A indicação do usuário <b>'$indicadoNome'</b> foi recusada pela administração do grupo em ".date('d-m-Y').". Consulte motivo em Meu Perfil->Indicações->Minhas Indicações->Negadas";
	$texto = addslashes($texto);
	$a->insereAviso($indicadoPor, $texto);
	
	$u->carregaDados($indicadoPor);
	// --- LOG -> Início ---
	$log = carregaClasse('Log');
	$dt = $log->dateTimeOnline(); //date e hora no momento atual
	$recusanteID = $_SESSION['ID'];
	$recusanteLogin = $_SESSION['login'];
	$indicadorLogin = stripslashes($u->getLogin());
	$acao = "Recusou uma indicação ao grupo (Recusado: '$indicadoNome' / Indicador: '$indicadorLogin')";
	$log->insereLog(array($recusanteID, $recusanteLogin, $dt, addslashes($acao)));
	// --- LOG -> Fim ---
	
	/*
	 * FALTA ENVIAR EMAIL AO INDICADO
	 * 
	 */

	echo json_encode(1);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function executaFiltro(){
	$dados = $_POST['dados'];
	$tipoValor = $_POST['tipoValor'];
	$c = carregaClasse("Compartilhamento");
	$dados= array_filter($dados, 'is_not_null'); //elimina nulls e vazios (""). Mantem 0 (zero)
	//$dados = array_filter($dados); //elimina arrays vazios ou nulos
	$ret = $c->buscaVagasClassificados($dados, $tipoValor);
	//echo json_encode($ret); exit;
	$tela = montaResultadoBuscaClassificados($ret);
	echo json_encode($tela);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function is_not_null($val){
	if ($val == "") $val = NULL;
	return !is_null($val);
}
//----------------------------------------------------------------------------------------------------------------------------
function montaResultadoBuscaClassificados($dados){
	$saida = "";
	if($dados->num_rows == 0) return "<tr><td colspan='6'><label class='text-warning'>Nenhum registro encontrado para os filtros informados!</label></td></tr>";
	$j = carregaClasse("Jogo");
	$u = carregaClasse("Usuario");
	while($d = $dados->fetch_object()){
		
		/*
		 *  Não mostrar vagas de usuaŕios inativos ou banidos
		 *  Arrumar solução para 'vagas em aberto' de usuarios com esses status - atualmente está mostrando
		if($d->vaga == "1" && ($d->ativo1 == 0 || $d->banido1 == 1)){ continue; 
		} else if ($d->vaga == "2" && ($d->ativo2 == 0 || $d->banido2 == 1)){ continue; 
		} else if ($d->vaga == "3" && ($d->ativo3 == 0 || $d->banido3 == 1)){ continue; }
		*/
		
		$jogos = $j->getJogosGrupo($d->idGrupo); //verifica se há mais de um jogo na conta
		$title = "";
		while($jogo = $jogos->fetch_object()){
			$nome = str_replace("'", " ", stripslashes($jogo->jogo));
			$nomeAbrev = $jogo->nome_abrev;
			$title .= "- $nome ($nomeAbrev)<br />";
		}
		
		$grupo = $d->idGrupo;
		if($d->fechado == 1) $stt = "Fechado"; else $stt = "Aberto";
		if($d->original1_id == 0) { 
			$login1 = "Vaga aberta"; $orig1 = $d->original2_id; $ativo1 = $d->ativo2; $banido1 = $d->banido2;
		} else { 
			if($d->banido1 == 1) $login1 = stripslashes($d->login1)." <small class='sm-ban'>(usuário banido)</small>"; 
			else $login1 = stripslashes($d->login1);
			$orig1 = $d->original1_id; $ativo1 = $d->ativo1; $banido1 = $d->banido1;
		}
		
		if($d->original2_id == 0){ 
			$login2 = "Vaga aberta"; $orig2 = $d->original1_id; $ativo2 = $d->ativo1; $banido2 = $d->banido1;
		} else { 
			if($d->banido2 == 1) $login2 = stripslashes($d->login2)." <small class='sm-ban'>(usuário banido)</small>";
			else $login2 = stripslashes($d->login2); 
			$orig2 = $d->original2_id; $ativo2 = $d->ativo2; $banido2 = $d->banido2;
		}
		
		if($d->original3_id == 0){ 
			$login3 = "Vaga aberta"; 
			if(!empty($d->original1_id)){
				$orig3 = $d->original1_id;
				$ativo3 = $d->ativo1; $banido3 = $d->banido1;
			} else {
				$orig3 = $d->original2_id; 
				$ativo3 = $d->ativo2; $banido3 = $d->banido2;
			}
			//$orig3 = (!empty($d->original1_id)) ? $d->original1_id : $d->original2_id; 
		} else { 
			if($d->banido3 == 1) $login3 = stripslashes($d->login3)." <small class='sm-ban'>(usuário banido)</small>";
			else $login3 = stripslashes($d->login3); 
			$orig3 = $d->original3_id; $ativo3 = $d->ativo3; $banido3 = $d->banido3;
		}
		
		//verifica usuarios inativos e/ou banidos
		if($d->vaga == "1" && ($ativo1 == 0 || $banido1 == 1)){ continue; 
		} else if ($d->vaga == "2" && ($ativo2 == 0 || $banido2 == 1)){ continue; 
		} else if ($d->vaga == "3" && ($ativo3 == 0 || $banido3 == 1)){ continue; }

		$saida .= "
			<tr>
				<td>$title</td>
				<td>";
					if($d->vaga == "1"){
						$popover = $u->mostraPerfilResumido($orig1, $grupo, 1); //retorna uma div oculta com o perfil do usuário no conteúdo
						$saida .= $popover."<label><a tabindex='0' data-id='".$grupo."_1' class='btn btn-xs btn-info' data-toggle='popover' title='Perfil resumido do dono da vaga'>Original 1: $login1</a></label><br />";
					}
					else
						$saida .= "<label class='text-muted small'>Original 1: $login1</label><br />";
					
					if($d->vaga == "2"){
						$popover = $u->mostraPerfilResumido($orig2, $grupo, 2); //retorna uma div oculta com o perfil do usuário no conteúdo
						$saida .= $popover."<label><a tabindex='0' data-id='".$grupo."_2' class='btn btn-xs btn-info' data-toggle='popover' title='Perfil resumido do dono da vaga'>Original 2: $login2</a></label><br />";
					}else	
						$saida .= "<label class='text-muted small'>Original 2: $login2</label><br />";
						
					if($d->vaga == "3"){
						$popover = $u->mostraPerfilResumido($orig3, $grupo, 3); //retorna uma div oculta com o perfil do usuário no conteúdo
						$saida .= $popover."<label><a tabindex='0' data-id='".$grupo."_3' class='btn btn-xs btn-info' data-toggle='popover' title='Perfil resumido do dono da vaga'>Fantasma: $login3</a></label><br />";
					}
					else
						$saida .= "<label class='text-muted small'>Fantasma: $login3</label>";
					
				$saida .= "
				</td>
				<td>R$ ".number_format($d->valor_venda, 2, ',', '.')."</td>
				<td>".stripslashes($d->loginCriador)."</td>
				<td>".$d->dataCompra."</td>
				<td>$stt</td>
			</tr>";
	}
	return $saida;
}
//----------------------------------------------------------------------------------------------------------------------------
function executaFiltroAdmGrupos(){
	$dados = $_POST['dados'];
	$c = carregaClasse("Compartilhamento");
	$dados= array_filter($dados, 'is_not_null'); //elimina nulls e vazios (""). Mantem 0 (zero)
	$ret = $c->buscaGruposAdm($dados);
	$tela = montaResultadoBuscaGruposAdm($ret);
	echo json_encode($tela);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function montaResultadoBuscaGruposAdm($dados){
	if($dados->num_rows == 0) return "<div class='col-md-12'><label>Nenhum registro encontrado para os filtros informados!</label></div>";
	$j = carregaClasse("Jogo");
	//$u = carregaClasse("Usuario");
	$saida = "";
	while($d = $dados->fetch_object()){
		
		$jogos = $j->getJogosGrupo($d->idGrupo); //verifica se há mais de um jogo na conta
		$title = "";
		while($jogo = $jogos->fetch_object()){
			$nome = str_replace("'", " ", stripslashes($jogo->jogo));
			$nomeAbrev = $jogo->nome_abrev;
			$title .= "
				<li class='list-group-item list-group-item-default'>
					- $nome ($nomeAbrev)
				</li>";
		}
		
		if ($d->original1_id == 0){
			$d->login1 = "Vaga em aberto";
		}
		if ($d->original2_id == 0){
			$d->login2 = "Vaga em aberto";
		}
		if ($d->original3_id == 0){
			$d->login3 = "Vaga em aberto";
		}
		$saida .= "
			<div class='panel panel-primary' id='panel-grupo_".$d->idGrupo."'>
				<div class='panel-heading'>
					".stripslashes($d->nomeGrupo)." (criador: ".stripslashes($d->loginCriador).")&nbsp;&nbsp;
					<div class='btn-group btn-group-xs' role='group'>
						<button type='button' class='btn btn-warning' id='inativa-grupo_".$d->idGrupo."' name='btn-inativar-grupo'>
							<span class='glyphicon glyphicon-eye-close'></span> Inativar Grupo
							<span class='glyphicon glyphicon-info-sign' data-toggle='tooltip' data-placement='bottom' data-html='true' 
								title='O grupo fica indisponível para os usuários, porém ainda faz parte do sistema e seu histórico não é apagado.'></span>
						</button>
						<button type='button' class='btn btn-danger' id='exclui-grupo_".$d->idGrupo."' name='btn-excluir-grupo'>
							<span class='glyphicon glyphicon-remove'></span> Excluir Grupo
							<span class='glyphicon glyphicon-info-sign' data-toggle='tooltip' data-placement='bottom' data-html='true' 
								title='Exclui o grupo e todo seu histórico, sem direito a reversão.'></span>
						</button>
					</div>
				</div>
				<div class='panel-body'>
					<div class='col-md-8'>
						<ul class='list-group'>
							<li class='list-group-item list-group-item-success'>Vagas:</li>
							<li class='list-group-item list-group-item-default'>
								<div class='row'>
									<label class='col-md-2'>Original 1:</label>
									<label class='col-md-10'>".stripslashes($d->login1)."</label>
								</div>
							</li>
							<li class='list-group-item list-group-item-default'>
								<div class='row'>
									<label class='col-md-2'>Original 2:</label>
									<label class='col-md-10'>".stripslashes($d->login2)."</label>
								</div>
							</li>
							<li class='list-group-item list-group-item-default'>
								<div class='row'>
									<label class='col-md-2'>Fantasma:</label>
									<label class='col-md-10'>".stripslashes($d->login3)."</label>
								</div>
							</li>
						</ul>
					</div>
					
					<div class='col-md-4'>
						<ul class='list-group'>
							<li class='list-group-item list-group-item-warning'>Jogo(s):</li>
							$title
						</ul>
					</div>					
				</div>
			</div><br />
		";
	}
	return $saida;
}
//----------------------------------------------------------------------------------------------------------------------------
function executaFiltroAdmLogs(){
	$dados = $_POST['dados'];
	$l = carregaClasse("Log");
	$dados= array_filter($dados, 'is_not_null'); //elimina nulls e vazios (""). Mantem 0 (zero)
	$ret = $l->buscaLogsAdm($dados); 
	$tela = montaResultadoBuscaLogsAdm($ret);
	echo json_encode($tela);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function montaResultadoBuscaLogsAdm($dados){
	if($dados->num_rows == 0) return "<div class='col-md-12'><label>Nenhum registro encontrado para os filtros informados!</label></div>";
	$saida = "";
	while($d = $dados->fetch_object()){
		$saida .= "
			<tr>
				<td>".substr($d->data_hora, 0, 25)."</td>
				<td>".stripslashes($d->usuario_login)."</td>
				<td>".stripslashes($d->acao)."</td>
			</tr>
		";
	}
	return $saida;
}
//----------------------------------------------------------------------------------------------------------------------------
function excluirGrupo(){
	session_start();
	$idGrupo = $_POST['idGrupo'];
	$c= carregaClasse("Compartilhamento");
	$r= carregaClasse("Recomendacao");
	$j = carregaClasse("Jogo");
	$hist = $c->getHistoricos($idGrupo); //retorna todos os históricos de um grupo
	
	//zera historico_id da tabela de recomendações
	while ($h = $hist->fetch_object()) $r->apagaOrigemRecomendacao($h->id);
	
	//apaga registros na tabela jogos_compartilhados
	$j->excluiJogosCompartilhados($idGrupo);
	
	//apaga registros da tabela historicos
	$c->excluiHistoricos($idGrupo);
	
	//apaga o registro do Grupo
	$c->carregaDados($idGrupo); //armazena dados do grupo antes de excluir para enviar aviso aos integrantes
	$c->excluiGrupo($idGrupo);
	
	//Envia avisos aos usuários do grupo	
	$orig1ID = $c->getOrig1();
	$orig2ID = $c->getOrig2();
	$orig3ID = $c->getOrig3();
	$nomeGrupo = stripslashes($c->getNome());
	$a = carregaClasse("Aviso");
	$texto = "O grupo <b>'$nomeGrupo'</b> da qual você faz parte, foi excluído do sistema pela administração em ".date('d-m-Y').". Em caso de dúvida, fazer contato com algum membro da administração.";
	$texto = addslashes($texto);
	if ($orig1ID != 0) $a->insereAviso($orig1ID, $texto);
	if ($orig2ID != 0) $a->insereAviso($orig2ID, $texto);
	if ($orig3ID != 0) $a->insereAviso($orig3ID, $texto);
	
	// --- LOG -> Início ---
	$idExecutante = $_SESSION['ID'];
	$loginExecutante = $_SESSION['login'];
	$log = carregaClasse('Log');
	$dt = $log->dateTimeOnline(); //date e hora no momento atual
	$acao = $loginExecutante." excluiu um grupo do sistema (GRUPO: $nomeGrupo).";
	$log->insereLog(array($idExecutante, $loginExecutante, $dt, addslashes($acao)));
	// --- LOG -> Fim ---

	echo json_encode(1);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function InativarGrupo(){
	session_start();
	$idGrupo = $_POST['idGrupo'];
	$c= carregaClasse("Compartilhamento");
	$c->inativaGrupo($idGrupo);
	
	//Envia avisos aos usuários do grupo	
	$c->carregaDados($idGrupo); 
	$orig1ID = $c->getOrig1();
	$orig2ID = $c->getOrig2();
	$orig3ID = $c->getOrig3();
	$nomeGrupo = stripslashes($c->getNome());
	$a = carregaClasse("Aviso");
	$texto = "O grupo <b>'$nomeGrupo'</b> da qual você faz parte, foi inativado do sistema pela administração em ".date('d-m-Y').". Em caso de dúvida, fazer contato com algum membro da administração.";
	$texto = addslashes($texto);
	if ($orig1ID != 0) $a->insereAviso($orig1ID, $texto);
	if ($orig2ID != 0) $a->insereAviso($orig2ID, $texto);
	if ($orig3ID != 0) $a->insereAviso($orig3ID, $texto);
	
	// --- LOG -> Início ---
	$idExecutante = $_SESSION['ID'];
	$loginExecutante = $_SESSION['login'];
	$log = carregaClasse('Log');
	$dt = $log->dateTimeOnline(); //date e hora no momento atual
	$acao = $loginExecutante." inativou um grupo do sistema (GRUPO: $nomeGrupo).";
	$log->insereLog(array($idExecutante, $loginExecutante, $dt, addslashes($acao)));
	// --- LOG -> Fim ---
	
	echo json_encode(1);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function mostraGruposInativos(){
	$c= carregaClasse("Compartilhamento");
	$ret = $c->getGruposInativos();
	
	$tela = montaResultadoGruposInativos($ret);
	echo json_encode($tela);
	exit;	
}
//----------------------------------------------------------------------------------------------------------------------------
function montaResultadoGruposInativos($dados){
	if($dados->num_rows == 0) return "<div class='col-md-12'><label>Nenhum há nenhum grupo inativo no momento!</label></div>";
	$saida = "";
	while($d = $dados->fetch_object()){
		if ($d->original1_id == 0){
			$d->login1 = "Vaga em aberto";
		}
		if ($d->original2_id == 0){
			$d->login2 = "Vaga em aberto";
		}
		if ($d->original3_id == 0){
			$d->login3 = "Vaga em aberto";
		}
		$saida .= "
			<div class='panel panel-primary' id='panel-grupo_".$d->idGrupo."'>
				<div class='panel-heading'>
					".stripslashes($d->nomeGrupo)." (criador: ".stripslashes($d->loginCriador).")&nbsp;&nbsp;
					<div class='btn-group btn-group-xs' role='group'>
						<button type='button' class='btn btn-success' id='ativa-grupo_".$d->idGrupo."' name='btn-ativar-grupo'>
							<span class='glyphicon glyphicon-eye-open'></span> Re-ativar Grupo
							<span class='glyphicon glyphicon-info-sign' data-toggle='tooltip' data-placement='bottom' data-html='true' 
								title='O grupo volta a ficar disponível para os usuários, inclusive novas negociações.'></span>
						</button>
					</div>
				</div>
				<div class='panel-body'>
					<div class='col-md-12'>
						<ul class='list-group'>
							<li class='list-group-item list-group-item-success'>Vagas:</li>
							<li class='list-group-item list-group-item-default'>
								<div class='row'>
									<label class='col-md-2'>Original 1:</label>
									<label class='col-md-10'>".stripslashes($d->login1)."</label>
								</div>
							</li>
							<li class='list-group-item list-group-item-default'>
								<div class='row'>
									<label class='col-md-2'>Original 2:</label>
									<label class='col-md-10'>".stripslashes($d->login2)."</label>
								</div>
							</li>
							<li class='list-group-item list-group-item-default'>
								<div class='row'>
									<label class='col-md-2'>Fantasma:</label>
									<label class='col-md-10'>".stripslashes($d->login3)."</label>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div><br />
		";
	}
	return $saida;
}
//----------------------------------------------------------------------------------------------------------------------------
function reativarGrupo(){
	session_start();
	$idGrupo = $_POST['idGrupo'];
	$c= carregaClasse("Compartilhamento");
	$c->reativaGrupo($idGrupo);
	
	//Envia avisos aos usuários do grupo	
	$c->carregaDados($idGrupo); 
	$orig1ID = $c->getOrig1();
	$orig2ID = $c->getOrig2();
	$orig3ID = $c->getOrig3();
	$nomeGrupo = stripslashes($c->getNome());
	$a = carregaClasse("Aviso");
	$texto = "O grupo <b>'$nomeGrupo'</b> da qual você faz parte, foi re-ativado no sistema pela administração em ".date('d-m-Y').". Em caso de dúvida, fazer contato com algum membro da administração.";
	$texto = addslashes($texto);
	if ($orig1ID != 0) $a->insereAviso($orig1ID, $texto);
	if ($orig2ID != 0) $a->insereAviso($orig2ID, $texto);
	if ($orig3ID != 0) $a->insereAviso($orig3ID, $texto);
	
	// --- LOG -> Início ---
	$idExecutante = $_SESSION['ID'];
	$loginExecutante = $_SESSION['login'];
	$log = carregaClasse('Log');
	$dt = $log->dateTimeOnline(); //date e hora no momento atual
	$acao = $loginExecutante." re-ativou um grupo do sistema (GRUPO: $nomeGrupo).";
	$log->insereLog(array($idExecutante, $loginExecutante, $dt, addslashes($acao)));
	// --- LOG -> Fim ---
	
	echo json_encode(1);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function marcaLidoAviso(){
	$idAviso = $_POST['aviso'];
	$a = carregaClasse("Aviso");
	$a->marcaLido($idAviso);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function removeAviso(){
	$idAviso = $_POST['aviso'];
	$a = carregaClasse("Aviso");
	$a->removeAviso($idAviso);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function gravaRecomendacao(){
	session_start();
	$comprador = $_SESSION['ID'];
	$recomendacaoID = $_POST['recomendacaoID'];
	$texto = $_POST['texto'];
	$r = carregaClasse("Recomendacao");
	$v = carregaClasse("Validacao");

	$ret = $r->is_this($recomendacaoID, $comprador);
	if(!$ret){
		$v->set('Registro', '')->set_error("Falha na autenticação.");
		 $erros = $v->get_errors();
		 echo json_encode($erros); 
		 exit;
	}

	$v->set("Comentário", trim($texto))->is_required(); 
	
	if($v->validate()){
		$dt = date('Y-m-d');
		$r->gravaRecomendacao($recomendacaoID, addslashes($texto), $dt);
		
		//Grava Aviso ao vendedor
		$a = carregaClasse("Aviso");
		$r->carregaDados($recomendacaoID);
		$vendedorID = $r->getVendedorId();
		$compradorLogin = $_SESSION['login'];
		$texto = "O usuário $compradorLogin registrou uma recomendação a você em ".date('d-m-Y').".";
		$texto = addslashes($texto);
		$a->insereAviso($vendedorID, $texto);

		echo json_encode(1);
	}else{
		 $erros = $v->get_errors();
		 echo json_encode($erros);
	}
	
	exit;	
}
//----------------------------------------------------------------------------------------------------------------------------
function cancelaRecomendacao(){
	$recomendacaoID = $_POST['recomendacao'];
	$r = carregaClasse("Recomendacao");
	$r->cancelaRecomendacao($recomendacaoID);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function alteraPerfil(){
	session_start();
	$tipo = $_POST['tp'];
	$valor = $_POST['vl'];
	$usuarioID = $_SESSION['ID'];
	$u = carregaClasse("Usuario");
	switch($tipo){
		case 'nome':
			if(trim($valor) == "") $erro = "Nome Inválido";
			else {
				$valor = addslashes($valor);
				$u->alteraCampoPerfil($tipo, $valor, $usuarioID);
			}
			break;
		case 'telegram':
			$tipo = "telegram_id";
			if(preg_match("/^[a-zA-Z0-9_]{5,30}$/", $valor) != 1) $erro = "Telegram ID inválido";
			else{
				$valor = addslashes($valor);
				$u->alteraCampoPerfil($tipo, $valor, $usuarioID);
			}
			break;
		case 'email':
			if(!preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/", $valor)) $erro = "E-mail inválido";
			else{
				$valor = addslashes($valor);
				$u->alteraCampoPerfil($tipo, $valor, $usuarioID);
			}
			break;
		case 'telefone':
			$u->alteraCampoPerfil($tipo, $valor, $usuarioID);
			break;
		case 'senha':
			$valor = md5($valor);
			$u->troca_senha_requisicao($usuarioID, $valor);
			break;
		default:
			echo json_encode("Erro desconhecido!");
			exit;
			break;
	}
	if(isset($erro)) echo json_encode($erro);
	else {
		// --- LOG -> Início ---
		$log = carregaClasse('Log');
		$dt = $log->dateTimeOnline(); //date e hora no momento atual
		$usuarioLogin = $_SESSION['login'];
		if ($tipo == 'senha') $valor = "*******";
		$acao = $usuarioLogin." alterou dados cadastrais (ITEM: '$tipo' / NOVO VALOR: $valor)";
		$log->insereLog(array($usuarioID, $usuarioLogin, $dt, addslashes($acao)));
		// --- LOG -> Fim ---
		 
		echo json_encode(1); 
	}
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function indicaUsuario(){
	session_start();
	$nome = $_POST['nome'];
	$email = $_POST['email'];
	$tel = $_POST['tel'];
	$indicador = $_SESSION['ID'];
	$u = carregaClasse("Usuario");
	$v = carregaClasse('Validacao');
	$v->set("Nome", $nome)->is_required();
	$v->set("E-mail", $email)->is_required()->is_email();
	$v->set("Celular", $tel)->is_required();
	
	//echo json_encode($dados);exit;
	if($v->validate()){
		$nome = addslashes($nome);
		$email = addslashes($email);
		$u->primeiro_registro_indicado($nome, $email, $tel, $indicador);
		
		//Grava Aviso aos ADMs que tem acesso a liberar indicados
		$ga = carregaClasse("Grupo Acesso");
		$grupos = $ga->getGruposPorAcesso("libera_indicados");
		if($grupos) {
			$a = carregaClasse("Aviso");
			$indicadorNome = $_SESSION['login'];
			$texto = "$indicadorNome fez uma indicação de usuário para o grupo de partilhas em ".date('d-m-Y').". Verifique a área administrativa para decidir liberar ou não essa indicação";
			$texto = addslashes($texto);
			foreach($grupos as $grupo){
				$usus = $u->getUsuariosPorGrupoAcesso($grupo);
				if ($usus){
					while ($dados = $usus->fetch_object()){
						$a->insereAviso($dados->id, $texto);
					}
				}
			}
		}

		echo json_encode("0");
	}else{
		 $erros = $v->get_errors();
		 echo json_encode($erros);
	}
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function salvaDadosCadastroAdm(){
	$tipo = $_POST['tipo'];
	$id = $_POST['id'];
	$valor = $_POST['val'];
	
	$u = carregaClasse("Usuario");
	$v = carregaClasse('Validacao');
	//echo json_encode($valor);exit;
	switch($tipo){
		case 'login':
			$v->set("Login", $valor)->is_required()->between_length(3,16)->is_alpha_num('_-');
			$valor = addslashes($valor);
			break;
		case 'nome':
			$v->set("Nome", $valor)->is_required()->between_length(3,60);
			$valor = addslashes($valor);
			break;
		case 'email':
			$v->set("E-mail", $valor)->is_required()->is_email()->between_length(8,100);
			break;
		case 'telefone':
			$v->set("Celular", $valor)->is_required()->is_phone();
			break;
		case 'id_email':
			$v->set("ID E-mail", $valor)->is_required()->min_length(6,true)->max_length(6,true)->is_alpha_num('_-');
			$valor = addslashes($valor);
			break;
	} 

	if($v->validate()){
		$u->alteraCampoPerfil($tipo, $valor, $id);
		echo json_encode("0");
	}else{
		 $erros = $v->get_errors();
		 echo json_encode($erros);
	}
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function onOffBoolean(){
	$id = $_POST['id'];
	$campo = $_POST['campo']; 
	$tabela = $_POST['tabela'];
	$novoValor = $_POST['valor'];
	//echo $tabela; exit;
	$t = carregaClasse($tabela);
	$t->on_off_boolean($id, $campo, $novoValor);

	exit;
}
//----------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------
// carrega classe solicitada e devolve uma instancia da mesma
function carregaClasse($secao){
	switch ($secao) {
		case 'Compartilhamento':
			require_once 'classes/compartilhamentos.class.php';
			$inst = new compartilhamentos();
			break;
		case 'Validacao':
			require_once 'classes/validacoes.class.php';
			$inst = new validacoes();
			break;
		case 'Jogo':
			require_once 'classes/jogos.class.php';
			$inst = new jogos();
			break;
		case 'Aviso':
			require_once 'classes/avisos.class.php';
			$inst = new avisos();
			break;
		case 'Recomendacao':
			require_once 'classes/recomendacoes.class.php';
			$inst = new recomendacoes();
			break;
		case 'Usuario':
			require_once 'classes/usuarios.class.php';
			$inst = new usuarios();
			break;
		case 'Log':
			require_once 'classes/logs.class.php';
			$inst = new logs();
			break;
		case 'Grupo Acesso':
			require_once 'classes/grupos_acesso.class.php';
			$inst = new grupos_acesso();
			break;
		default:
			
		break;
	}
	return $inst;
}
//----------------------------------------------------------------------------------------------------------------------------

?>
