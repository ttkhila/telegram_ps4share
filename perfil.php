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
	
	//Recomendações
	$recomendacoes = $r->getMinhasRecomendacoes($_SESSION['ID']);
	
	//estatísticas - usuário
	$gt = $c->gruposTotaisUsuario($_SESSION['ID']);
	$gtVaga = $c->gruposTotaisUsuarioPorVaga($_SESSION['ID']);
	$gt2 = $c->gruposAtivos($_SESSION['ID']);
	$gt3 = $c->gruposCriadosUsuario($_SESSION['ID']);
	$gt2Vaga = $c->gruposAtivosPorVaga($_SESSION['ID']);
	$ma = $c->montanteArrecadado($_SESSION['ID']);
	
	//estatisticas - global
	$gtg = $c->montanteArrecadadoGlobal();
	$gtg2 = $c->gruposTotaisGlobal();
	$gtg3 = $c->totalRepassesGlobal();
	$gtgVaga = $c->totalRepassesGlobalPorVaga();
	$moeda = $c->moedaPreferida();
	$jogo = $c->jogoPreferido();
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script type="text/javascript" src="js/lib/jquery.mask.min.js"/></script>
<script>
	$(function(){ 
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
		
	});	
</script>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- Conteúdo Principal: Início -->
	<h2 class="page-header">Perfil do Usuário - <?php echo $_SESSION['login']; ?></h2>
	
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary" id="div-edita-perfil">
				<div class="panel-heading"><span class="glyphicon glyphicon-user"></span> Dados cadastrais</div>
				<div class="panel-body fixed-panel">
					<ul class="list-group">
						
						<li class="list-group-item list-group-item-info">
							<div class="row">
								<div class="col-sm-offset-1 col-sm-3">Nome:</div>
								<div class="col-sm-8">
									<div id="fixo_nome" style="display:block">
										<label><?php echo stripslashes(utf8_decode($u->getNome())); ?></label>
										<a href="#" name="edita-perfil" id="link_nome">[editar]</a>
									</div>
									<div id="edita_nome" style="display:none">
										<input class="input-sm" type="text" name="txt_nome" id="txt_nome" value="<?php echo stripslashes(utf8_decode($u->getNome())); ?>" />
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
								<div class="col-sm-8"><label><?php echo stripslashes(utf8_decode($u->getLogin())); ?></label></div>
							</div>
						</li>
						
						<li class="list-group-item list-group-item-info">
							<div class="row">
								<div class="col-sm-offset-1 col-sm-3">
									ID Telegram:
									<img src='img/help.png' width='16' height='16' data-toggle="tooltip" data-placement="bottom" data-html="true" 
										title="Identificação única do usuário no Telegram.<br />
											Precisa ser cadastrada no app.<br />
											Aceita letras maiúsculas, minúsculas, números e sublinhado(_)." />
								</div>	
								<div class="col-sm-8">
									<div id="fixo_telegram" style="display:block">
										<label><?php if(!empty($u->getTelegramId())) echo "@".stripslashes(utf8_decode($u->getTelegramId())); else echo "Não Cadastrado"; ?></label>
										<a href="#" name="edita-perfil" id="link_telegram">[editar]</a>
									</div>
									<div id="edita_telegram" style="display:none">
										<div class="input-group">
											<span class="input-group-addon input-sm" id="addon1">@</span>
											<input class="input-sm" type="text" name="txt_telegram" id="txt_telegram" value="<?php echo stripslashes(utf8_decode($u->getTelegramId())); ?>" maxlength="30" aria-describedby="addon1"  />
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
										<label><?php echo stripslashes(utf8_decode($u->getEmail())); ?></label>
										<a href="#" name="edita-perfil" id="link_email">[editar]</a>
									</div>
									<div id="edita_email" style="display:none">
										<input class="input-sm" type="text" name="txt_email" id="txt_email" value="<?php echo stripslashes(utf8_decode($u->getEmail())); ?>" />
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
										<label id="lbl_tel"><?php echo $u->getTelefone(); ?></label>
										<a href="#" name="edita-perfil" id="link_telefone">[editar]</a>
										<script type="text/javascript">$("#lbl_tel").mask("(00) 0000-00009");</script>
									</div>
									<div id="edita_telefone" style="display:none">
										<input class="input-sm" type="tel" name="txt_telefone" id="txt_telefone" value="<?php echo $u->getTelefone(); ?>" pattern="\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}" />
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
									<img src='img/help.png' width='16' height='16' data-toggle="tooltip" data-placement="bottom" data-html="true" 
										title="Identificação única de usuário<br />para criação de e-mails de conta<br />de compartilhamento.<br />
											Não pode ser alterada." />
								</div>
								<div class="col-sm-8"><label><?php echo $u->getIdEmail(); ?></label></div>
							</div>
						</li>
						
						<li class="list-group-item list-group-item-info">
							<div class="row">
								<div class="col-sm-offset-1 col-sm-3">Grupo (sistema):</div>
								<div class="col-sm-8"><label><?php echo stripslashes(utf8_decode($ga->getNome())); ?></label></div>
							</div>
						</li>

					</ul>
				</div>
			</div>
		</div>
		
		<!-- ESTATÍSTICAS -->
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading"><span class="glyphicon glyphicon-stats"></span> Estatísticas</div>
				<div class="panel-body fixed-panel">
				
					<div class="panel panel-info"> 
						<div class="panel-heading">Usuário</div>
						<div class="panel-body">
							<ul class="list-group">
								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Grupos totais em que está ou já esteve:</span>
										<span class="col-sm-4"><label><?php echo $gt->qtd; ?></label></span>
									</div>
								</li>
								<li class="list-group-item list-group-item-text">
									<div class="row">
										<span class="col-sm-offset-2 col-sm-6">Original 1:</span>
										<span class="col-sm-4"><label><?php echo $gtVaga[1]; ?></label></span>
									</div>
								</li>
								<li class="list-group-item list-group-item-text">
									<div class="row">
										<span class="col-sm-offset-2 col-sm-6">Original 2:</span>
										<span class="col-sm-4"><label><?php echo $gtVaga[2]; ?></label></span>
									</div>
								</li>
								<li class="list-group-item list-group-item-text">
									<div class="row">
										<span class="col-sm-offset-2 col-sm-6">Fantasma:</span>
										<span class="col-sm-4"><label><?php echo $gtVaga[3]; ?></label></span>
									</div>
								</li>

								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Grupos Ativos em que faz parte:</span>
										<span class="col-sm-4">
											<label><?php echo $gt2->qtd; ?></label>&nbsp;
											<img src='img/help.png' width='16' height='16' data-toggle="tooltip" data-placement="right" data-html="true" 
												title="Aviso: Um usuário pode ser dono de<br /> mais de uma vaga dentro de um mesmo <br />grupo, por isso a soma dos valores abaixo<br /> pode ser maior que o valor mostrado aqui." />
										</span>
									</div>
								</li>
								<li class="list-group-item list-group-item-text">
									<div class="row">
										<span class="col-sm-offset-2 col-sm-6">Original 1:</span>
										<span class="col-sm-4"><label><?php echo $gt2Vaga[1]; ?></label></span>
									</div>
								</li>
								<li class="list-group-item list-group-item-text">
									<div class="row">
										<span class="col-sm-offset-2 col-sm-6">Original 2:</span>
										<span class="col-sm-4"><label><?php echo $gt2Vaga[2]; ?></label></span>
									</div>
								</li>
								<li class="list-group-item list-group-item-text">
									<div class="row">
										<span class="col-sm-offset-2 col-sm-6">Fantasma:</span>
										<span class="col-sm-4"><label><?php echo $gt2Vaga[3]; ?></label></span>
									</div>
								</li>
								
								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Grupos Criados por você:</span>
										<span class="col-sm-4"><label><?php echo $gt3->qtd; ?></label></span>
									</div>
								</li>

								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Montante gasto em jogos:</span>
										<span class="col-sm-4"><label>R$ <?php echo number_format($gt->valorTotal, 2, ",", "."); ?></label></span>
									</div>
								</li>
							
								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Montante arrecadado em jogos:</span>
										<span class="col-sm-4"><label>R$ <?php echo number_format($ma->valorTotal, 2, ",", "."); ?></label></span>
									</div>
								</li>
								
								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Vagas suas a venda no momento:</span>
										<span class="col-sm-4"><label><?php echo $gt->qtdVenda; ?></label></span>
									</div>
								</li>
							</ul>
							
						</div>
					</div><!-- Panel-info - Usuário -->
					
					<div class="panel panel-info">
						<div class="panel-heading">Global</div>
						<div class="panel-body">
							
							<ul class="list-group">
								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Grupos totais já criados:</span>
										<span class="col-sm-4"><label><?php echo $gtg2->qtd; ?></label></span>
									</div>
								</li>
								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Montante negociado:</span>
										<span class="col-sm-4"><label>R$ <?php echo number_format($gtg->valorTotal, 2, ",", "."); ?></label></span>
									</div>
								</li>
								
								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Total de repasses de vagas:</span>
										<span class="col-sm-4"><label><?php echo $gtg3->qtd; ?></label></span>
									</div>
								</li>
								<li class="list-group-item list-group-item-text">
									<div class="row">
										<span class="col-sm-offset-2 col-sm-6">Repasses de Original 1:</span>
										<span class="col-sm-4"><label><?php echo $gtgVaga[1]; ?></label></span>
									</div>
								</li>
								<li class="list-group-item list-group-item-text">
									<div class="row">
										<span class="col-sm-offset-2 col-sm-6">Repasses de Original 2:</span>
										<span class="col-sm-4"><label><?php echo $gtgVaga[2]; ?></label></span>
									</div>
								</li>
								<li class="list-group-item list-group-item-text">
									<div class="row">
										<span class="col-sm-offset-2 col-sm-6">Repasses de Fantasma:</span>
										<span class="col-sm-4"><label><?php echo $gtgVaga[3]; ?></label></span>
									</div>
								</li>
								
								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Vagas a venda atualmente:</span>
										<span class="col-sm-4"><label><?php echo $gtg->qtdVenda; ?></label></span>
									</div>
								</li>
								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-7">Moeda mais usada em criações de grupo:</span>
										<span class="col-sm-4">
											<label><?php echo stripslashes(utf8_decode($moeda->nome))." (".$moeda->pais.") - ".$moeda->qtd." grupos"; ?></label>
										</span>
									</div>
								</li>
								<li class="list-group-item list-group-item-warning">
									<div class="row">
										<span class="col-sm-offset-1 col-sm-11">Jogo mais compartilhado:</span>
										<span class="col-sm-offset-1 col-sm-11">
											<label><?php echo stripslashes(utf8_decode($jogo->nomeJogo))." (".$jogo->plataforma.") - ".$jogo->qtd." grupos"; ?></label>
										</span>
									</div>
								</li>
							</ul>
							
						</div>
					</div><!-- Panel-info - Global -->
					
					
					
				</div>
			</div>
		</div>
		
	</div>
		
	<div class="row">
		<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading"><span class="glyphicon glyphicon-thumbs-up"></span> Recomendações</div>
			<div class="panel-body">
			<?php
				if($recomendacoes->num_rows == 0) $rec = "<div class='col-md-12'><label>Não há recomendações recebidas até o momento.</label></div>";
				else {
					$rec = "<ul class='list-group'>";
					while($dados = $recomendacoes->fetch_object()){
						$rec .= "
							<li class='list-group-item list-group-item-warning'>
								<span class='glyphicon glyphicon-user'></span> ".stripslashes(utf8_decode($dados->login))."<small> em ".$dados->data."</small>
								<br /><label>- ".stripslashes(utf8_decode($dados->texto))."</label>
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
	</div>

		

	
	<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
</html>

