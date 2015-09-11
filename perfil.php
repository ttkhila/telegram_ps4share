<?php
	session_start();
	if(!isset($_SESSION['login']))
		header('Location: aviso.php?a=2');
	include_once 'classes/usuarios.class.php';
	include 'classes/recomendacoes.class.php';
	include 'funcoes.php';
	
	$u = new usuarios();
	$u->carregaDados($_SESSION['ID']);
	$r = new recomendacoes();
	$recomendacoes = $r->getMinhasRecomendacoes($_SESSION['ID']);
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script>
	$(function(){ 
	});	
</script>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- Conteúdo Principal: Início -->
	<h2 class="page-header">Perfil do Usuário</h2>
	
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading"><span class="glyphicon glyphicon-user"></span> Dados cadastrais</div>
				<div class="panel-body fixed-panel">
					<ul class="list-group">
						<li class="list-group-item list-group-item-info">
							<div class="row">
								<div class="col-sm-offset-1 col-sm-3">Nome:</div>
								<div class="col-sm-8">
									<input class="input-sm" type="text" name="txtNome" id="txtId" value="<?php echo stripslashes(utf8_decode($u->getNome())); ?>" />
									<button class="btn btn-xs btn-primary">editar</button>
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
								<div class="col-sm-offset-1 col-sm-3">ID Telegram:</div>
								<div class="col-sm-8"><label>@<?php echo stripslashes(utf8_decode($u->getTelegramId())); ?></label></div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading"><span class="glyphicon glyphicon-stats"></span> Estatísticas</div>
				<div class="panel-body fixed-panel">nqne hfuwehfuih ewufihweuifhweuifhui</div>
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

