<?php
	include 'funcoes.php';
	$id = $_GET["id"];
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
	<h1 class="page-header">Primeiro Acesso</h1>
	
	<form class="form-inline" action="processa-nova-senha.php" method="post">
		<input type="hidden" id="hidId" name="hidId" value="<?php echo $id; ?>" />
		<div class="form-group col-md-12">
			<label for="passEmail" class="control-label col-sm-3">Senha recebida no e-mail:</label>
			<input type="password" class="form-control col-sm-9" name="passEmail" id="passEmail" maxlength="10" required />
		</div><br /><br />
		<div class="form-group col-md-12">
			<label for="passNova" class="control-label  col-sm-3">Nova Senha:</label>
			<input type="password" class="form-control  col-sm-8" name="passNova" id="passNova" maxlength="10" pattern="(^[\w-!#@+]{6,10})$" required="" />
			<div class="col-sm-1">
				<img src='img/help.png' width='16' height='16' data-toggle="tooltip" data-placement="right" title="Sua senha deve ter entre 6 e 10 caracteres, podendo conter letras, números e os seguintes caracteres especiais: (_ - ! # @ +)." />
			</div>
		</div><br /><br />
		<div class="form-group col-md-12">
			<label for="rePassNova" class="control-label  col-sm-3">Repita a Nova Senha:</label>
			<input type="password" class="form-control  col-sm-9" name="rePassNova" id="rePassNova" maxlength="10" pattern="(^[\w-!#@+]{6,10})$" required="" />
		</div><br /><br />
		<div class="form-group col-md-12">
			<div class="col-sm-3">&nbsp;</div>
			<button class="btn btn-primary">Ok</button>
		</div>
	</form><br /><br />
	
	
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
</html>
