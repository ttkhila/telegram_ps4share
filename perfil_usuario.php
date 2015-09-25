
<?php
	session_start();
	if(!isset($_SESSION['login'])) header('Location: aviso.php?a=2');
	if(isset($_GET['user']) && !empty($_GET['user'])) $user = $_GET['user']; else die("URL Inv&aacute;lida!");
	
	include_once 'classes/usuarios.class.php';
	include_once 'classes/recomendacoes.class.php';
	include_once 'classes/compartilhamentos.class.php';
	include 'funcoes.php';

	$u = new usuarios();
	$c = new compartilhamentos();
	$r = new recomendacoes();
	
	//dados cadastrais
	$u->carregaDados($user);
	$nome = stripslashes(utf8_decode($u->getNome()));
	$telegramID = $u->getTelegramId();
	$login = stripslashes(utf8_decode($u->getLogin()));
	$email = $u->getIdEmail();
	
	//Recomendações
	$recomendacoes = $r->getMinhasRecomendacoes($user);
	
	//estatísticas - usuário
	$gt = $c->gruposTotaisUsuario($user);
	$gt3 = $c->gruposCriadosUsuario($user);
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script type="text/javascript" src="js/lib/jquery.mask.min.js"/></script>
<script>
	$(function(){ 
	});	
</script>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- Conteúdo Principal: Início -->
	<h2 class="page-header">Perfil do Usuário - <?php echo stripslashes(utf8_decode($u->getLogin())); ?></h2>
	
	<div class="row">
		<div class="panel panel-primary" id="div-edita-perfil">
			<div class="panel-heading"><span class="glyphicon glyphicon-user"></span> Dados de contato</div>
			<div class="panel-body">
				
				<ul class="list-group col-sm-6">
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-5">Nome:</div>
							<div class="col-sm-6">
								<label><?php echo $nome; ?></label>
							</div>
						</div>
					</li>
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-5">
								ID Telegram:
								<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="bottom" data-html="true" 
									title="Identificação única do usuário no Telegram.<br />
										Precisa ser cadastrada no app e pode ser <br />usada para fazer contato com o mesmo."></span>
							</div>	
							<div class="col-sm-6">
								<label><?php if(!empty($telegramID)) echo "@".stripslashes(utf8_decode($telegramID)); else echo "Não Cadastrado"; ?></label>
							</div>
						</div>
					</li>
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-5">Grupos Criados pelo usuário:</div>
							<div class="col-sm-6"><label><?php echo $gt3->qtd; ?></label></div>
						</div>
					</li>
				</ul>
				
				<ul class="list-group col-sm-6">
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-5">ID:</div>
							<div class="col-sm-6"><label><?php echo $login; ?></label></div>
						</div>
					</li>
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-5">
								E-mail ID:
								<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="bottom" data-html="true" 
									title="Identificação única de usuário<br />para criação de e-mails de conta<br />de compartilhamento.<br />
										Não pode ser alterada."></span>
							</div>
							<div class="col-sm-6"><label><?php echo $email; ?></label></div>
						</div>
					</li>
					<li class="list-group-item list-group-item-info">
						<div class="row">
							<div class="col-sm-offset-1 col-sm-5">Quantidade de vagas a venda:</div>
							<div class="col-sm-6">
								<label><?php echo $gt->qtdVenda; ?></label>&nbsp;
									<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" data-html="true" 
										title='Aviso: Esse número não compreende<br />as vagas "Em Aberto" colocadas a <br />venda pelo usuário.<br />'></span>
							</div>
						</div>
					</li>
				</ul>
				
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading"><span class="glyphicon glyphicon-thumbs-up"></span> Recomendações recebidas por <?php echo $login; ?></div>
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

