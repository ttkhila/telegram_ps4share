<?php
	session_start();
	if(!isset($_SESSION['login']))
		header('Location: aviso.php?a=2');
	include_once 'classes/usuarios.class.php';
	include_once 'classes/recomendacoes.class.php';
	include_once 'classes/grupos_acesso.class.php';
	include_once 'classes/compartilhamentos.class.php';
	include 'funcoes.php';
	
	$u = new usuarios();
	$ga = new grupos_acesso();
	$c = new compartilhamentos();
	$r = new recomendacoes();
	
	//dados cadastrais
	$u->carregaDados($_SESSION['ID']);
	$ga->carregaDados($u->getGrupoAcessoId());
	$nome = stripslashes(utf8_decode($u->getNome()));
	$telegramID = $u->getTelegramId();
	$login = stripslashes(utf8_decode($u->getLogin()));
	$emailID = $u->getIdEmail();
	$email = $u->getEmail();
	$tel = $u->getTelefone();
	$nomeGrupoAcesso = stripslashes(utf8_decode($ga->getNome()));
	$usuarioDesde = $u->getPrimeiroAcessoData();

	//estatísticas - usuário
	if(is_null($usuarioDesde)) $usuarioDesde = "N/A";
	else $usuarioDesde = date( 'd/m/Y', strtotime($usuarioDesde) );
	$gt = $c->gruposTotaisUsuario($_SESSION['ID']);
	$gtVaga = $c->gruposTotaisUsuarioPorVaga($_SESSION['ID']);
	$gt2 = $c->gruposAtivos($_SESSION['ID']);
	$gt3 = $c->gruposCriadosUsuario($_SESSION['ID']);
	$gt2Vaga = $c->gruposAtivosPorVaga($_SESSION['ID']);
	$ma = $c->montanteArrecadado($_SESSION['ID']);
	$gtValorTotal = $gt->valorTotal;
	$maValorTotal = $ma->valorTotal;
	
	//estatisticas - global
	$gtg = $c->montanteArrecadadoGlobal();
	$gtg2 = $c->gruposTotaisGlobal();
	$gtg3 = $c->totalRepassesGlobal();
	$gtgVaga = $c->totalRepassesGlobalPorVaga();
	$moeda = $c->moedaPreferida();
	$jogo = $c->jogoPreferido();
	
	$gtgValorTotal = $gtg->valorTotal;
	$moedaNome = stripslashes(utf8_decode($moeda->nome));
	$moedaPais = $moeda->pais;
	$moedaQtd = $moeda->qtd;
	
	$jogoNome = stripslashes(utf8_decode($jogo->nomeJogo));
	$jogoPlataforma = $jogo->plataforma;
	$jogoQtd = $jogo->qtd;
	
	//Recomendações recebidas
	$recomendacoes = $r->getMinhasRecomendacoes($_SESSION['ID']);
	
	//Recomendações efetuadas
	$recomendacoesE = $r->getMinhasRecomendacoesEfetuadas($_SESSION['ID']);
	
	//Indicados Pendentes
	$ind = $u->getIndicadosPendentesPorIndicador($_SESSION['ID']);
	
	//Indicações Negadas
	$indNeg = $u->getIndicacoesNegadasPorIndicador($_SESSION['ID']);
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<style>
#heading1 { cursor: pointer; }
#heading2 {cursor: pointer; }
</style>
<script type="text/javascript" src="js/lib/jquery.mask.min.js"/></script>
<script>
	$(function(){ 
		$('#abas-perfil').tab();
		$("[name='edita-perfil']").click(function(e){
			e.preventDefault(); //previne o evento 'normal'
			$campo = $(this).attr("id").split("_")[1];
			$("#fixo_"+$campo).hide();
			$("#edita_"+$campo).show();
			//alert($campo);
		});
		
		$("[name='btn-esconde-edita']").click(function(e){
			e.preventDefault(); //previne o evento 'normal'
			$campo = $(this).attr("id").split("_")[1];
			$("#edita_"+$campo).hide();
			$("#fixo_"+$campo).show();
		});
		
		
		function toggleChevron(e) {
		    $(e.target)
			 .prev('.panel-heading')
			 .find('.accordion-toggle')
			 .toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
		}
		$('#grupo-recomenda').on('hidden.bs.collapse', toggleChevron);
		$('#grupo-recomenda').on('shown.bs.collapse', toggleChevron);
		
		
	});	
</script>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- Conteúdo Principal: Início -->
	<h2 class="page-header">Perfil do Usuário - <?php echo $_SESSION['login']; ?></h2>

		<ul class="nav nav-tabs" id="abas-perfil" data-tabs="tabs">
			<li class="active"><a href="#aba-dados_cadastrais" data-toggle="tab"><span class="glyphicon glyphicon-user"></span> Dados Cadastrais</a></li>
			<li><a href="#aba-estatisticas" data-toggle="tab"><span class="glyphicon glyphicon-stats"></span> Estatísticas</a></li>
			<li><a href="#aba-recomendacoes" data-toggle="tab"><span class="glyphicon glyphicon-thumbs-up"></span> Recomendações</a></li>
			<li><a href="#aba-indicacoes" data-toggle="tab"><span class="glyphicon glyphicon-hand-right"></span> Indicações</a></li>
			<li><a href="#aba-preferencias" data-toggle="tab"><span class="glyphicon glyphicon-cog"></span> Preferências</a></li>
		</ul>
		
		<div id="my-tab-content" class="tab-content">
			
			<div class="tab-pane active" id="aba-dados_cadastrais"><!-- ABA DADOS CADASTRAIS -->
				<ul class="list-group" style="margin-top:5px;">	
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-3">Nome:</div>
							<div class="col-sm-8">
								<div id="fixo_nome" style="display:block">
									<label><?php echo $nome; ?></label>
									<a href="#" name="edita-perfil" id="link_nome">[editar]</a>
								</div>
								<div id="edita_nome" style="display:none">
									<input class="input-sm" type="text" name="txt_nome" id="txt_nome" value="<?php echo $nome; ?>" />
									<button class="btn btn-xs btn-success glyphicon glyphicon-saved" name="btn-edita-perfil" id="btn-edita_nome"> Salvar</button>
									<button class="btn btn-xs btn-danger glyphicon glyphicon-eye-close" name="btn-esconde-edita" id="btn-esconde_nome" title="Cancela Edição"></button>
									<p class="bg-danger" style="display:none;"></p>
								</div>
							</div>
						</div>
					</li>
					
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-3">ID:</div>
							<div class="col-sm-8"><label><?php echo $login; ?></label></div>
						</div>
					</li>
					
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-3">
								Telegram ID:
								<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="bottom" data-html="true" 
									title="Identificação única do usuário no Telegram.<br />
										Precisa ser cadastrada no app.<br />
										Aceita letras maiúsculas, minúsculas,<br />
										números e sublinhado(_)."></span>
							</div>	
							<div class="col-sm-8">
								<div id="fixo_telegram" style="display:block">
									<label><?php if(!empty($telegramID)) echo "@".$telegramID; else echo "Não Cadastrado"; ?></label>
									<a href="#" name="edita-perfil" id="link_telegram">[editar]</a>
								</div>
								<div id="edita_telegram" style="display:none">
									<div class="input-group">
										<span class="input-group-addon input-sm" id="addon1">@</span>
										<input class="input-sm" type="text" name="txt_telegram" id="txt_telegram" value="<?php echo $telegramID; ?>" maxlength="30" aria-describedby="addon1"  />
										&nbsp;<button class="btn btn-xs btn-success glyphicon glyphicon-saved" name="btn-edita-perfil" id="btn-edita_telegram"> Salvar</button>
										&nbsp;<button class="btn btn-xs btn-danger glyphicon glyphicon-eye-close" name="btn-esconde-edita" id="btn-esconde_telegram" title="Cancela Edição"></button>
									</div>
									<p class="bg-danger" style="display:none;"></p>
								</div>
							</div>
						</div>
					</li>
					
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-3">E-mail pessoal:</div>	
							<div class="col-sm-8">
								<div id="fixo_email" style="display:block">
									<label><?php echo $email; ?></label>
									<a href="#" name="edita-perfil" id="link_email">[editar]</a>
								</div>
								<div id="edita_email" style="display:none">
									<input class="input-sm" type="text" name="txt_email" id="txt_email" value="<?php echo $email; ?>" />
									<button class="btn btn-xs btn-success glyphicon glyphicon-saved" name="btn-edita-perfil" id="btn-edita_email"> Salvar</button>
									<button class="btn btn-xs btn-danger glyphicon glyphicon-eye-close" name="btn-esconde-edita" id="btn-esconde_email" title="Cancela Edição"></button>
									<p class="bg-danger" style="display:none;"></p>
								</div>
							</div>
						</div>
					</li>
					
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-3">Celular:</div>	
							<div class="col-sm-8">
								<div id="fixo_telefone" style="display:block">
									<label id="lbl_tel"><?php echo $tel; ?></label>
									<a href="#" name="edita-perfil" id="link_telefone">[editar]</a>
									<script type="text/javascript">$("#lbl_tel").mask("(00) 0000-00009");</script>
								</div>
								<div id="edita_telefone" style="display:none">
									<input class="input-sm" type="tel" name="txt_telefone" id="txt_telefone" value="<?php echo $tel; ?>" pattern="\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}" />
									<button class="btn btn-xs btn-success glyphicon glyphicon-saved" name="btn-edita-perfil" id="btn-edita_telefone"> Salvar</button>
									<button class="btn btn-xs btn-danger glyphicon glyphicon-eye-close" name="btn-esconde-edita" id="btn-esconde_telefone" title="Cancela Edição"></button>
									<p class="bg-danger" style="display:none;"></p>
									<script type="text/javascript">$("#txt_telefone").mask("(00) 0000-00009");</script>
								</div>
							</div>
						</div>
					</li>
					
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-3">
								E-mail ID:
								<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="bottom" data-html="true" 
									title="Identificação única de usuário<br />para criação de e-mails de conta<br />de compartilhamento.<br />
										Não pode ser alterada."></span>
							</div>
							<div class="col-sm-8"><label><?php echo $emailID; ?></label></div>
						</div>
					</li>
					
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-3">Grupo (sistema):</div>
							<div class="col-sm-8"><label><?php echo $nomeGrupoAcesso; ?></label></div>
						</div>
					</li>
					
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-3">
								Senha do site:
								
							</div>	
							<div class="col-sm-8">
								<div id="fixo_senha" style="display:block">
									<label><a href="#" name="edita-perfil" id="link_senha">[mudar a senha]</a></label>
								</div>
								<div id="edita_senha" style="display:none">
									<input class="input-sm" type="password" name="txt_senha" id="txt_senha" maxlength="10" pattern="(^[\w-!#@+]{6,10})$" required="" placeholder="Digite a nova senha"  />&nbsp;
									<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="bottom" data-html="true" 
									title="Sua senha deve ter entre 6 e 10 caracteres,<br />podendo conter letras, números e os seguintes<br /> caracteres especiais: (_ - ! # @ +)."></span><br />
									<input class="input-sm" type="password" name="txt_senha2" id="txt_senha2" maxlength="10" pattern="(^[\w-!#@+]{6,10})$" required="" placeholder="Re-digite a nova senha"  />
									&nbsp;<button class="btn btn-xs btn-success glyphicon glyphicon-saved" name="btn-edita-perfil" id="btn-edita_senha"> Salvar</button>
									&nbsp;<button class="btn btn-xs btn-danger glyphicon glyphicon-eye-close" name="btn-esconde-edita" id="btn-esconde_senha" title="Cancela Edição"></button>
									<p class="bg-danger" style="display:none;"></p>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</div><!-- aba-dados_cadastrais -->
			
			<div class="tab-pane" id="aba-estatisticas"><!-- ABA ESTATÍSTICAS -->
				<div class="panel panel-primary" style="margin-top:5px;"><!-- ESTATÍSTICAS USUARIO -->
					<div class="panel-heading"><span class="glyphicon glyphicon-user"></span> Estatísticas do Usuário</div>
					<div class="panel-body">
						<ul class="list-group">
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Usuário Desde:</span>
									<span class="col-sm-7"><label><?php echo $usuarioDesde; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Grupos Ativos que você faz parte:</span>
									<span class="col-sm-7">
										<label><?php echo $gt2->qtd; ?></label>&nbsp;
										<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" data-html="true" 
											title="Aviso: Um usuário pode ser dono de<br /> mais de uma vaga dentro de um mesmo <br />grupo, por isso a soma dos valores abaixo<br /> pode ser maior que o valor mostrado aqui."></span>
									</span>
								</div>
							</li>
							<li class="list-group-item list-group-item-text">
								<div class="row">
									<span class="col-sm-offset-2 col-sm-3">Original 1:</span>
									<span class="col-sm-7"><label><?php echo $gt2Vaga[1]; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-text">
								<div class="row">
									<span class="col-sm-offset-2 col-sm-3">Original 2:</span>
									<span class="col-sm-7"><label><?php echo $gt2Vaga[2]; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-text">
								<div class="row">
									<span class="col-sm-offset-2 col-sm-3">Fantasma:</span>
									<span class="col-sm-7"><label><?php echo $gt2Vaga[3]; ?></label></span>
								</div>
							</li>
							
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Total de grupos em que você está ou já esteve:</span>
									<span class="col-sm-7"><label><?php echo $gt->qtd; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-text">
								<div class="row">
									<span class="col-sm-offset-2 col-sm-3">Original 1:</span>
									<span class="col-sm-7"><label><?php echo $gtVaga[1]; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-text">
								<div class="row">
									<span class="col-sm-offset-2 col-sm-3">Original 2:</span>
									<span class="col-sm-7"><label><?php echo $gtVaga[2]; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-text">
								<div class="row">
									<span class="col-sm-offset-2 col-sm-3">Fantasma:</span>
									<span class="col-sm-7"><label><?php echo $gtVaga[3]; ?></label></span>
								</div>
							</li>
							
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Grupos Criados por você:</span>
									<span class="col-sm-7"><label><?php echo $gt3->qtd; ?></label></span>
								</div>
							</li>

							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Montante gasto em jogos:</span>
									<span class="col-sm-7"><label>R$ <?php echo number_format($gtValorTotal, 2, ",", "."); ?></label></span>
								</div>
							</li>
						
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Montante arrecadado em jogos:</span>
									<span class="col-sm-7"><label>R$ <?php echo number_format($maValorTotal, 2, ",", "."); ?></label></span>
								</div>
							</li>
							
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Vagas suas a venda no momento:</span>
									<span class="col-sm-7">
										<label><?php echo $gt->qtdVenda; ?>&nbsp;
										<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" data-html="true" 
											title='Aviso: Esse número não compreende<br />as vagas "Em Aberto" colocadas a <br />venda pelo usuário.<br />'></span>
									</label></span>
								</div>
							</li>
						</ul>
					</div> <!-- panel-body USUARIO -->
				</div> <!-- panel panel-primary - USUARIO -->
					
				<div class="panel panel-primary"> <!-- ESTATÍSTICAS GLOBAL -->
					<div class="panel-heading"><span class="glyphicon glyphicon-globe"></span> Estatísticas Globais</div>
					<div class="panel-body">	
						<ul class="list-group">
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Grupos totais já criados:</span>
									<span class="col-sm-7"><label><?php echo $gtg2->qtd; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Moeda mais usada em criações de grupo:</span>
									<span class="col-sm-7">
										<label><?php echo $moedaNome." (".$moedaPais.") - ".$moedaQtd." grupos"; ?></label>
									</span>
								</div>
							</li>
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Montante negociado:</span>
									<span class="col-sm-7"><label>R$ <?php echo number_format($gtgValorTotal, 2, ",", "."); ?></label></span>
								</div>
							</li>
							
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Total de repasses de vagas:</span>
									<span class="col-sm-7"><label><?php echo $gtg3->qtd; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-text">
								<div class="row">
									<span class="col-sm-offset-2 col-sm-3">Repasses de Original 1:</span>
									<span class="col-sm-7"><label><?php echo $gtgVaga[1]; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-text">
								<div class="row">
									<span class="col-sm-offset-2 col-sm-3">Repasses de Original 2:</span>
									<span class="col-sm-7"><label><?php echo $gtgVaga[2]; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-text">
								<div class="row">
									<span class="col-sm-offset-2 col-sm-3">Repasses de Fantasma:</span>
									<span class="col-sm-7"><label><?php echo $gtgVaga[3]; ?></label></span>
								</div>
							</li>
							
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Vagas a venda atualmente:</span>
									<span class="col-sm-7"><label><?php echo $gtg->qtdVenda; ?></label></span>
								</div>
							</li>
							<li class="list-group-item list-group-item-warning">
								<div class="row">
									<span class="col-sm-offset-1 col-sm-4">Jogo mais compartilhado:</span>
									<span class="col-sm-7">
										<label><?php echo $jogoNome." (".$jogoPlataforma.") - ".$jogoQtd." grupos"; ?></label>
									</span>
								</div>
							</li>
						</ul>
					</div> <!-- panel-body GLOBAL -->
				</div> <!-- panel panel-primary GLOBAL -->	
			</div><!-- aba-estatisticas -->

			<div class="tab-pane" id="aba-recomendacoes"><!-- ABA RECOMENDAÇÕES  -->
				<div class="panel-group" id="grupo-recomenda" role="tablist" aria-multiselectable="true" style="margin-top:5px;">
					<div class="panel panel-primary">
						<div class="panel-heading" role="tab" id="heading1" data-toggle="collapse" data-parent="#grupo-recomenda" data-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
							<h4 class="panel-title">
								<a role="button">
									<span class="glyphicon glyphicon-star"></span> 
									Recomendações Recebidas por você&nbsp;
									<span class="glyphicon glyphicon-chevron-down accordion-toggle"></span> 
								</a>
							</h4>
						</div>
						
						<div id="collapse1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading1">
							<div class="panel-body">
							<?php
								if($recomendacoes->num_rows == 0) $rec = "<div class='col-md-12'><label>Não há recomendações recebidas até o momento.</label></div>";
								else {
									$rec = "<ul class='list-group'>";
									while($dados = $recomendacoes->fetch_object()){
										$rec .= "
											<li class='list-group-item list-group-item-warning'>
												<span class='glyphicon glyphicon-user'></span> ".stripslashes(utf8_decode($dados->login))."<small> em ".$dados->data."</small>
												<br /><span class='glyphicon glyphicon-comment'></span> ".stripslashes(utf8_decode($dados->texto))."
											</li><br />
											";
									}
									$rec .= "</ul>";
								}
								echo $rec;
							?>
							</div>
						</div>
					</div>

					<div class="panel panel-success">
						<div class="panel-heading" role="tab" id="heading2" data-toggle="collapse" data-parent="#grupo-recomenda" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
							<h4 class="panel-title">
								<a role="button">
									<span class="glyphicon glyphicon-star-empty"></span> 
									Recomendações Efetuadas por você&nbsp;
									<span class="glyphicon glyphicon-chevron-down accordion-toggle"></span> 
								</a>
							</h4>
						</div>
						<div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading2">
							<div class="panel-body">
							<?php
								if($recomendacoesE->num_rows == 0) $rec = "<div class='col-md-12'><label>Não há recomendações efetuadas por você até o momento.</label></div>";
								else {
									$rec = "<ul class='list-group'>";
									while($dados = $recomendacoesE->fetch_object()){
										$rec .= "
											<li class='list-group-item list-group-item-warning'>
												<span class='glyphicon glyphicon-user'></span> ".stripslashes(utf8_decode($dados->login))."<small> em ".$dados->data."</small>
												<br /><span class='glyphicon glyphicon-comment'></span> ".stripslashes(utf8_decode($dados->texto))."
											</li><br />
											";
									}
									$rec .= "</ul>";
								}
								echo $rec;
							?>
							</div>
						</div>
					</div>
				</div><!-- ID: grupo_recomenda -->
			</div><!-- aba-recomendacoes -->
			
			<div class="tab-pane" id="aba-indicacoes"><!-- ABA INDICAÇÕES  -->
			
				<div class="panel-group" style="margin-top:5px;">
					<div class="panel panel-primary">
						<div class="panel-heading"><span class="glyphicon glyphicon-list-alt"></span> Formulário de Indicação ao Grupo de Partilhas Telegram - Dados do Indicado</div>
						<div class="panel-body">
							<form role="form">
								<div class="form-group">
									<label for="nome">Nome <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="nome" required maxlength="100" />
								</div>
								<div class="form-group">
									<label for="email">E-mail <span class="text-danger">*</span></label>
									<input type="email" class="form-control" id="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" />
								</div>
								<div class="form-group">
									<label for="telefone">Celular <span class="text-danger">*</span></label>
									<input type="tel" class="form-control" id="telefone" required pattern="\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}" />
									<script type="text/javascript">$("#telefone").mask("(00) 0000-00009");</script>
								</div>
								<div class="form-group"><span class="text-danger">*</span> Campos obrigatórios</div>
								<p class="bg-danger" id="sp-erro-msg-modal" style="display:none;"></p>
								<p class="bg-success" id="sp-sucesso-msg-modal" style="display:none;"></p>
								<button type="submit" class="btn btn-success">Enviar</button>
							</form>
						</div>
					</div>
					<br />
					<div class="panel panel-primary">
						<div class="panel-heading"><span class="glyphicon glyphicon-hand-right"></span> Minhas indicações</div>
						<div class="panel-body">
							<div class="panel panel-warning">
								<div class="panel-heading"><span class="glyphicon glyphicon-time"></span> Pendentes de confirmação</div>
								<div class="panel-body">
								<?php
									if(!$ind){ $saida = "<div class='col-md-12'><label>Não há indicações suas pendentes de confirmação.</label></div>"; }
									else {
										$saida = "<ul class='list-group'>";
										while($dados = $ind->fetch_object()){
										$saida .= "
											<li class='list-group-item list-group-item-default'>
												<span class='glyphicon glyphicon-circle-arrow-right'></span> ".stripslashes(utf8_decode($dados->nome))."<small> - Celular: ".$dados->telefone."</small>
											</li>
											";
										}
										$saida .= "</ul>";
									}
									echo $saida;
								?>
								</div>
							</div>
							
							<div class="panel panel-success">
								<div class="panel-heading"><span class="glyphicon glyphicon-ok-circle"></span> Confirmadas</div>
								<div class="panel-body">
									Confirmadas aqui
								</div>
							</div>
							<div class="panel panel-danger">
								<div class="panel-heading"><span class="glyphicon glyphicon-ban-circle"></span> Negadas</div>
								<div class="panel-body">
									<?php
									if(!$indNeg){ $saida = "<div class='col-md-12'><label>Não há indicações suas negadas.</label></div>"; }
									else {
										$saida = "<ul class='list-group'>";
										while($dados = $indNeg->fetch_object()){
										$saida .= "
											<li class='list-group-item list-group-item-text'>
												<span class='glyphicon glyphicon-thumbs-down'></span> ".stripslashes(utf8_decode($dados->nome))."<br />
												&nbsp;&nbsp;<span class='glyphicon glyphicon-arrow-right'></span><small> Motivo: ".stripslashes(utf8_decode($dados->motivo))."</small>
											</li>
											";
										}
										$saida .= "</ul>";
									}
									echo $saida;
								?>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div><!-- aba-indicacoes -->
			
			<div class="tab-pane" id="aba-preferencias"><!-- ABA PREFERÊNCIAS  -->
				 Preferências
			</div><!-- aba-preferencias -->
			
		</div>
		
		
		
		
		
		
		
		
	
		
		
		

		
	

	<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
</html>

