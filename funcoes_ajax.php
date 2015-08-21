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
    } else { //LOGIN OK! Carregar os dados 
        
        session_start();
        $_SESSION['login'] = stripslashes(utf8_decode($resp->login)); //PSN ID
        $_SESSION['ID'] = $resp->id; //Usuário ID
        $result = array(1);//sucesso
        
        // --- LOG -> Início ---
        /*
		$log = carregaClasse('Log');
		$dt = $log->dateTimeOnline(); //date e hora no momento atual
		$usuLogin = $_SESSION['login']; $usuID = $_SESSION['ID'];
		$acao = stripslashes(utf8_decode($usuLogin))." se logou!";
		$log->insereLog(array($usuID, $usuLogin, $dt, addslashes(utf8_encode($acao))));
		*/
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
	/*
	$log = carregaClasse('Log');
	$dt = $log->dateTimeOnline(); //date e hora no momento atual
	$acao = stripslashes(utf8_decode($usuLogin))." se deslogou!";
	$log->insereLog(array($usuID, $usuLogin, $dt, addslashes(utf8_encode($acao))));
	*/
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
		if($parte[0] == 'nome') $v->set($parte[0], $parte[1])->is_required()->min_length(3, true); //NOME
		
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
		$consolidados = array("nome", "email", "original1_id", "original2_id", "original3_id", "moeda_id", "fechado");
		foreach($dados as $value){
			$parte = explode("%=%", $value);
			if(in_array($parte[0], $consolidados)) { //está entre os dados que coincidem nome do campo no form com nome do campo no BD
				array_push($campos, $parte[0]);
				array_push($valores, "'".addslashes(utf8_encode($parte[1]))."'");
			} else { //dados restantes
				$outrosDados[$parte[0]] = $parte[1];
			}
		}
		
		array_push($campos, "criador_id");
		array_push($valores, $selfID);
		$idGrupo = $c->insereGrupo($campos, $valores);
		$soma = $c->gravaVagas($idGrupo, $orig[1], $orig[2], $orig[3], $outrosDados); //retorna a soma dos valores lançados
		
		$retorno2 = $j->gravaJogosCompartilhados($idGrupo, $outrosDados);
		
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
		//echo json_encode(array($retorno2));	exit;
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
function mostraGrupo(){
	$idGrupo = $_POST['id'];
	$selfID = $_POST['selfid'];
	$c = carregaClasse('Compartilhamento');
	$j = carregaClasse('Jogo');
	$u = carregaClasse('Usuario'); 
	$c->carregaDados($idGrupo);
	$saida = "";
	$simboloMoeda = $c->recupera_dados_moedas($c->getMoedaId())->simbolo;
	$nomeMoeda = stripslashes(utf8_decode($c->recupera_dados_moedas($c->getMoedaId())->nome));
	if($c->getFechado() == 1) $fechado = "Sim"; else $fechado = "Não";

	if($c->getOrig1() == 0){ $orig1 = "Vaga em aberto"; $orig1Nome = "Vaga em aberto"; $orig1ID = 0; $valor1 = "N/D"; }
	else { 
		$u->carregaDados($c->getOrig1()); 
		$c->carregaDadosHistoricos($idGrupo, 1);
		$orig1 = stripslashes(utf8_decode($u->getLogin())); 
		$orig1Nome = stripslashes(utf8_decode($u->getNome()));
		$orig1ID = $u->getId();
		$valorPago = $c->getValorPago();
		$valor1 = (!empty($valorPago)) ? $simboloMoeda." ".number_format($valorPago, 2, ',', '.') : "N/D";
	}
	
	if($c->getOrig2() == 0){ $orig2 = "Vaga em aberto"; $orig2Nome = "Vaga em aberto"; $orig2ID = 0;  $valor2 = "N/D";} 
	else { 
		$u->carregaDados($c->getOrig2()); 
		$c->carregaDadosHistoricos($idGrupo, "2");
		$orig2 = stripslashes(utf8_decode($u->getLogin()));
		$orig2Nome = stripslashes(utf8_decode($u->getNome()));
		$orig2ID = $u->getId();
		$valorPago = $c->getValorPago();
		$valor2 = (!empty($valorPago)) ? $simboloMoeda." ".number_format($valorPago, 2, ',', '.'): "N/D";
	}
	
	if($c->getOrig3() == 0){ $orig3 = "Vaga em aberto"; $orig3Nome = "Vaga em aberto"; $orig3ID = 0; $valor3 = "N/D"; }
	else { 
		$u->carregaDados($c->getOrig3()); 
		$c->carregaDadosHistoricos($idGrupo, "3");
		$orig3 = stripslashes(utf8_decode($u->getLogin()));
		$orig3Nome = stripslashes(utf8_decode($u->getNome())); 
		$orig3ID = $u->getId();
		$valorPago = $c->getValorPago(); 
		$valor3 = (!empty($valorPago)) ? $simboloMoeda." ".number_format($valorPago, 2, ',', '.'): "N/D";
	}

	//identado por HTML
	$saida .= "<div class='list-group-item-heading col-md-8'>";
		$saida .= "<div class='list-group-item active'>Vagas/Valores originais* ($nomeMoeda) ";
		if($c->getFechado() == 0 && $selfID == $c->getCriadorId()){ //insere botão Fechar Grupo
			$saida .= "<div class='badge'><a role='button' id='grupo_$idGrupo' name='btn-fechar-grupo' data-toggle='modal' data-target='#fecha-grupo'>Fechar Grupo</a> ";
			$saida .= "<img src='img/help.png' width='16' height='16' data-toggle='tooltip' data-placement='right' 
										title='Informa que o grupo já possui suas vagas preenchidas, com os respectivos valores dessas vagas e a conta se encontra devidamente criada na PSN/Live.' /></div>";
		}
		$saida .= "</div>";
		

		//Original 1, 2 e fantasma
		$saida .= "<div class='list-group-item-info' style='padding:10px 0px;'>";
			$saida .= "<div class='list-group-item-text'><label class='col-sm-2'>Original 1: </label><label class='col-sm-4' style='font-weight:normal;' title='$orig1Nome'>$orig1 
				<div name='input-valor' id='input-valor_".$idGrupo."_1' class='form-group div-input-valor'>Valor em reais (opcional):<span aria-label='Close' class='close' name='sp-close-input-valor'>&times;</span>
				<input class='input-xs' type='text' id='txt-valor-venda_".$idGrupo."_1' /><button class='btn btn-xs btn-primary' rel='1' id='btn-grupo_".$idGrupo."_1'>Confirma</button></div> 
				%%opcoes1%%</label>
				<label class='col-sm-3'>Valor pago: </label><label class='col-sm-3' style='font-weight:normal;'>$valor1</label></div>";
			$saida .= "<div class='list-group-item-text'><label class='col-sm-2'>Original 2: </label><label class='col-sm-4' style='font-weight:normal;' title='$orig2Nome'>$orig2 
				<div name='input-valor' id='input-valor_".$idGrupo."_2' class='form-group div-input-valor'>Valor em reais (opcional):<span aria-label='Close' class='close' name='sp-close-input-valor'>&times;</span>
				<input class='input-xs' type='text' id='txt-valor-venda_".$idGrupo."_2' /><button class='btn btn-xs btn-primary' rel='2' id='btn-grupo_".$idGrupo."_2'>Confirma</button></div> 
				%%opcoes2%%</label>
				<label class='col-sm-3'>Valor pago: </label><label class='col-sm-3' style='font-weight:normal;'>$valor2</label></div>";
			$saida .= "<div class='list-group-item-text'><label class='col-sm-2'>Fantasma: </label><label class='col-sm-4' style='font-weight:normal;' title='$orig3Nome'>$orig3 
				<div name='input-valor' id='input-valor_".$idGrupo."_3' class='form-group div-input-valor'>Valor em reais (opcional):<span aria-label='Close' class='close' name='sp-close-input-valor'>&times;</span>
				<input class='input-xs' type='text' id='txt-valor-venda_".$idGrupo."_3' /><button class='btn btn-xs btn-primary' rel='3' id='btn-grupo_".$idGrupo."_3'>Confirma</button></div> 
				%%opcoes3%%</label>
				<label class='col-sm-3'>Valor pago: </label><label class='col-sm-3' style='font-weight:normal;'>$valor3</label></div>";
		$saida .= "<br /><br /><br /></div>";
	
		if($c->getFechado() == 1){
			$saida .= "<div class='list-group-item-success'>";
				$saida .= "<label class='col-sm-6'><a href='#' name='historico-grupo' data-toggle='modal' data-target='#historico' id='historico_".$c->getId()."'>Ver Histórico</a></label>";
				$saida .= "<label class='col-sm-3'>Valor Total: </label><label class='col-sm-3' style='font-weight:normal;'>".$simboloMoeda." ".number_format($c->getValor(), 2, ',', '.')."</label>";	
			if($c->getMoedaId() != 1){ //moeda estrangeira - mostrar conversão
				$saida .= "<label class='col-sm-6'></label><label class='col-sm-3'>Convertido(R$): </label>
					<label class='col-sm-3' style='font-weight:normal;'>R$ ".number_format($c->getValorConvertido(), 2, ',', '.')."</label>";
				$saida .= "<label class='col-sm-6'></label><label class='col-sm-3'>Fator Conversão: </label>
					<label class='col-sm-3' style='font-weight:normal;'>".$simboloMoeda." 1,00 = R$ ".str_replace(".", ",", number_format($c->getFatorConversao(), 2))."</label>";
			}
			$saida .= "</div>";
		}
		$saida .= "<div class='panel-footer'>*Valores originais referentes a compra da conta sem levar em consideração os repasses da mesma.</div>";
	$saida .= "<br /></div>";
		
	//recupera os jogos da conta
	$jogos = $j->getJogosGrupo($idGrupo);
	$saida .= "<div class='list-group-item-heading col-md-4'>";
		$saida .= "<div class='list-group-item active'>Jogos:</div>";
		$saida .= "<div class='list-group-item list-group-item-info'>";
			while($d = $jogos->fetch_object()){
				$saida .= "<span>- ".stripslashes(utf8_decode($d->jogo))." (".$d->nome_abrev.")</span><br />";	
			}
		$saida .= "</div>";
	$saida .= "</div>";
	
	//Opções de repasse e disponibilizar vaga
	// ORIGINAL 1
	if($orig1ID == $selfID && $c->getFechado() == 1) $opcoes1 = "<img name='img-repasse' data-id='1' data-toggle='modal' data-target='#repasse' id='img-repasse_$idGrupo' rel='1' title='Informar vaga repassada' src='img/cash.gif' />
		&nbsp;&nbsp;<img name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_1'  title='Colocar vaga a venda' src='img/checkout.png' />"; // grupo fechado. OS donos das vagas podem repassa-la ou coloca-la a venda
	else if($selfID == $c->getCriadorId() && $c->getFechado() == 0 && $c->getOrig1() == 0) $opcoes1 = "<img name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' data-target='#repasse' id='img-repasse_$idGrupo' rel='1' title='Informar vaga repassada' src='img/cash.gif' />
		&nbsp;&nbsp;<img name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_1'  title='Colocar vaga a venda' src='img/checkout.png' />"; //grupo aberto. O criador tem o direito de colocar uma vaga que estiver sem dono, a venda
	else if($orig1ID == $selfID  && $c->getFechado() == 0 && $selfID != $c->getCriadorId()) $opcoes1 = "<img name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' data-target='#repasse' id='img-repasse_$idGrupo' rel='1' title='Informar vaga repassada' src='img/cash.gif' />
		&nbsp;&nbsp;<img name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_1'  title='Colocar vaga a venda' src='img/checkout.png' />";//grupo aberto. O usuário pode desistir da sua vaga e a passar pra outro. O criador não pode fazer isso
	else $opcoes1 = "";
	// ORIGINAL 2
	if($orig2ID == $selfID && $c->getFechado() == 1) $opcoes2 = "<img name='img-repasse' data-id='1' data-toggle='modal' data-target='#repasse' id='img-repasse_$idGrupo' rel='2' title='Informar vaga repassada' src='img/cash.gif' />
		&nbsp;&nbsp;<img name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_2'  title='Colocar vaga a venda' src='img/checkout.png' />"; 
	else if($selfID == $c->getCriadorId() && $c->getFechado() == 0 && $c->getOrig2() == 0) $opcoes2 = "<img name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' data-target='#repasse' id='img-repasse_$idGrupo' rel='2' title='Informar vaga repassada' src='img/cash.gif' />
		&nbsp;&nbsp;<img name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_2'  title='Colocar vaga a venda' src='img/checkout.png' />"; 
	else if($orig2ID == $selfID  && $c->getFechado() == 0 && $selfID != $c->getCriadorId()) $opcoes2 = "<img name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' data-target='#repasse' id='img-repasse_$idGrupo' rel='2' title='Informar vaga repassada' src='img/cash.gif' />
		&nbsp;&nbsp;<img name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_2'  title='Colocar vaga a venda' src='img/checkout.png' />"; 
	else $opcoes2 = "";
	// FANTASMA
	if($orig3ID == $selfID && $c->getFechado() == 1) $opcoes3 = "<img name='img-repasse' data-id='1' data-toggle='modal' data-target='#repasse' id='img-repasse_$idGrupo' rel='3' title='Informar vaga repassada' src='img/cash.gif' />
		&nbsp;&nbsp;<img name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_3'  title='Colocar vaga a venda' src='img/checkout.png' />"; 
	else if($selfID == $c->getCriadorId() && $c->getFechado() == 0 && $c->getOrig3() == 0) $opcoes3 = "<img name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' data-target='#repasse' id='img-repasse_$idGrupo' rel='3' title='Informar vaga repassada' src='img/cash.gif' />
		&nbsp;&nbsp;<img name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_3'  title='Colocar vaga a venda' src='img/checkout.png' />"; 
	else if($orig3ID == $selfID  && $c->getFechado() == 0 && $selfID != $c->getCriadorId()) $opcoes3 = "<img name='img-repasse' data-id='$nomeMoeda' data-toggle='modal' data-target='#repasse' id='img-repasse_$idGrupo' rel='3' title='Informar vaga repassada' src='img/cash.gif' />
		&nbsp;&nbsp;<img name='img-disponibiliza' id='img-disponibiliza_".$idGrupo."_3'  title='Colocar vaga a venda' src='img/checkout.png' />"; 
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
	$saida .= "<tr><th colspan=4 style='background-color:#28720F; color:#fff'>Grupo: ".stripslashes(utf8_decode($c->getNome()))."</th></tr>";
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
		else $saida .= "<td title='".stripslashes(utf8_decode($d->nome))."'>".stripslashes(utf8_decode($d->login))."</td>";
		$cont ++;
	}
	$saida .= "</tr>";
	
	if($dadosHist->num_rows > 0){ //a conta já foi repassada ao menos uma vez depois da criação
		while($d = $dadosHist->fetch_object()){ //dados do histórico da conta já repassada
			$phpdate = strtotime($d->data_venda);
			$data_venda = date( 'd-m-Y', $phpdate );
			$saida .= "<tr><td>$data_venda</td>";
			if($d->vaga == '1') { //Original 1
				$saida .= "<td title='".stripslashes(utf8_decode($d->nome_comprador))."'>".stripslashes(utf8_decode($d->login_comprador))."</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
			} else if($d->vaga == '2') { //Original 1
				$saida .= "<td>&nbsp;</td><td title='".stripslashes(utf8_decode($d->nome_comprador))."'>".stripslashes(utf8_decode($d->login_comprador))."</td><td>&nbsp;</td></tr>";
			} else if($d->vaga == '3') { //Original 1
				$saida .= "<td>&nbsp;</td><td>&nbsp;</td><td title='".stripslashes(utf8_decode($d->nome_comprador))."'>".stripslashes(utf8_decode($d->login_comprador))."</td></tr>";
			}	
		}
	}
	$saida .= "</tbody>";
	$saida .= "</table>";
	
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
		echo json_encode(1);
	}else{
		 $erros = $v->get_errors();
		 echo json_encode($erros);
	}
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
	//echo json_encode($dados);exit;

	$nomeConta = stripslashes(utf8_decode($c->getNome()));
	$moeda = $c->getMoedaId();
	//echo json_encode($nomeConta);exit;

	if($dados["email"] != "" && !empty($dados["email"])) $email = $dados["email"];

	if($dados["orig1"] > 0){
		$c->carregaDadosHistoricos($idGrupo, 1);
		$u->carregaDados($dados["orig1"]);
		$valorPago = $c->getValorPago();
		if(empty($valorPago)) $valor1 = "";
		else $valor1 = number_format($valorPago, 2, ',', '.');
		$login1 = stripslashes(utf8_decode($u->getLogin()));
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
		$login2 = stripslashes(utf8_decode($u->getLogin()));
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
		$login3 = stripslashes(utf8_decode($u->getLogin()));
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

	$saida = "
	<input type='hidden' id='id-grupo-fechamento' value='$idGrupo' />
	<h5 class='label-personalizada'>Para incluir ou alterar usuários nas vagas, feche essa tela e use a anterior.</h5>
	<div class='form-group'>
		<label class='control-label'>Nome da conta:</label>
		<input type='text' class='form-control' name='nome-fechamento' id='nome-fechamento' maxlength='50' required='' placeholder='Nome da conta' value='$nomeConta' />
	</div>
	<div class='form-group'>
		<label class='control-label'>E-mail da conta:</label>";
		if(!isset($email))	$saida .= "<input type='email' class='form-control' name='email-fechamento' id='email-fechamento' 
				placeholder='E-mail da conta' required='' />";
		else $saida .= "<label cl	ass='label-info'>$email</label>";
		
	$saida .= "
	</div>
	<div class='form-group'>
		<label class='control-label'>Moeda de Compra</label>
		<select class='form-control' id='moedas-fechamento' name='moedas-fechamento'>";                            
			while($m = $moedas->fetch_object()){
				if($m->id === $moeda) $saida .= "<option value='".$m->id."' selected='selected'>".stripslashes(utf8_decode($m->nome))." (".$m->pais.")</option>";
				else $saida .= "<option value='".$m->id."'>".stripslashes(utf8_decode($m->nome))." (".$m->pais.")</option>";
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
	<div class='checkbox-inline'>
		<label>Alterou a senha? <input type='checkbox' name='alterou_senha-fechamento' id='alterou_senha-fechamento' /></label>
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
	//echo json_encode($moeda_id);exit;

	$campos_conta = array("nome", "email", "moeda_id");
	$campos_historico = array("valor1", "valor2", "valor3", "senha_alterada");
	$campos_conta_result = array();
	$campos_historico_result = array();

	$c = carregaClasse('Compartilhamento');
	$j = carregaClasse('Jogo');
	
	// 'monta' um array ($campos_conta_result) com campos%=%valores
	// 'monta' um array ($campos_historico_result) com campos%=%valores
	foreach ($campos as $key => $value) {
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
	echo json_encode(1);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function executaFiltro(){
	$dados = $_POST['dados'];
	$tipoValor = $_POST['tipoValor'];
	//echo json_encode($dados["fechado"]); exit;
	$c = carregaClasse("Compartilhamento");
	$dados = array_filter($dados); //elimina arrays vazios ou nulos
	$ret = $c->buscaVagasClassificados($dados, $tipoValor);
	//echo json_encode($ret); exit;
	$tela = montaResultadoBuscaClassificados($ret);
	echo json_encode($tela);
	exit;
}
//----------------------------------------------------------------------------------------------------------------------------
function montaResultadoBuscaClassificados($dados){
	$saida = "";
	while($d = $dados->fetch_object()){
		if($d->fechado == 1) $stt = "Fechado"; else $stt = "Aberto";
		if($d->original1_id == 0) $login1 = "Vaga aberta"; else $login1 = stripslashes(utf8_decode($d->login1));
		if($d->original2_id == 0) $login2 = "Vaga aberta"; else $login2 = stripslashes(utf8_decode($d->login2));
		if($d->original3_id == 0) $login3 = "Vaga aberta"; else $login3 = stripslashes(utf8_decode($d->login3));
		$saida .= "
			<tr>
				<td>".stripslashes(utf8_decode($d->nomeJogo))."</td>
				<td>
					<span>Original 1: $login1</span>
					<span>Original 2: $login2</span>
					<span>Fantasma: $login3</span>
				</td>
				<td>".number_format($d->valor_venda, 2, ',', '.')."</td>
				<td>".stripslashes(utf8_decode($d->loginCriador))."</td>
				<td>".$d->dataCompra."</td>
				<td>$stt</td>
			</tr>";
	}
	return $saida;
}
//----------------------------------------------------------------------------------------------------------------------------
/*
SELECT c.id as idGrupo, c.original1_id, c.original2_id, c.original3_id, c.data_compra, c.fechado, c.criador_id, 
				h.comprador_id, h.vaga, h.data_venda, h.valor_venda, j.id as idJogo, j.nome as nomeJogo, u1.login, u2.login, u3.login, u4.login as loginCriador 
				FROM compartilhamentos c, historicos h, jogos_compartilhados jc, jogos j, usuarios u1, usuarios u2, usuarios u3 
				WHERE h.comprador_id = 3 AND jc.jogo_id = 5 AND h.vaga = '1' AND  (jc.compartilhamento_id = c.id) AND (h.compartilhamento_id = c.id) AND (jc.jogo_id = j.id) AND (h.a_venda = 1) 
				AND (u1.id = c.original1_id)  AND (u2.id = c.original2_id) AND (u3.id = c.original3_id)
<th>Jogo(s)</th>
<th>Proprietários das vagas</th>
<th>Preço da vaga</th>
<th>Criador</th>
<th>Data criação</th>
<th>Status</th>
*/
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
		case 'Mensagem':
			require_once 'classes/mensagens.class.php';
			$inst = new mensagens();
			break;
		case 'Campeonato':
			require_once 'classes/campeonatos.class.php';
			$inst = new campeonatos();
			break;
		case 'Usuario':
			require_once './classes/usuarios.class.php';
			$inst = new usuarios();
			break;
		case 'Log':
			require_once 'classes/logs.class.php';
			$inst = new logs();
			break;
		case 'Disponibilidade':
			require_once 'classes/disponibilidades.class.php';
			$inst = new disponibilidades();
			break;
		default:
			
		break;
	}
	return $inst;
}
//----------------------------------------------------------------------------------------------------------------------------

?>
