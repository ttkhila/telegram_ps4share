<?php
	session_start();
	if(isset($_SESSION['login']))
		header('Location: grupos.php');
	
	include 'funcoes.php';
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script>
	$(function(){ 
		$("#a-recupera-senha").click(function(e){
			e.preventDefault();
			$("#frm-recupera-senha").show();
		});
	});
</script>
</head>
<body>
	<?php //$menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
		<!-- Conteúdo Principal: Início --> 
	<div class="container">
		<form class="form-signin" id="frmLogin" name="frmLogin" method="post">
			<h2 class="form-signin-heading">Login</h2>
			<p class="bg-danger" id="sp-erro-msg" style="display:none;"></p><!-- mensagem de erro -->
			<label for="inputEmail" class="sr-only">PSN-ID</label>
			<input type="text" name="login" id="login" maxlength="16" class="form-control" placeholder="Insira sua ID" required autofocus>			
			<label for="inputPassword" class="sr-only">Senha</label>
			<input type="password" name="senha" id="senha" maxlength="10" class="form-control" placeholder="Insira sua senha" required>			
			<button class="btn btn-lg btn-primary btn-block" type="submit">Confirmar</button>
		</form>
		<br />
		<div class="form-group">
			<a role="button" id="a-recupera-senha">Esqueceu a senha?</a>
		</div>
		<form role="form" id="frm-recupera-senha" style="display:none;">
			<div class="form-group">
				<label>Informe a ID cadastrada(PSN/Live)</label>
				<input type="text" class="form-control" id="loginForRecoveryPass" required="" pattern="(^[\w-]{3,16})$" maxlength="16" />	
			</div>
			<div class="form-group">
				<p class="bg-danger" id="sp-erro-msg2" style="display:none;"></p>
				<p class="bg-success" id="sp-sucesso-msg" style="display:none;"></p>
				<button role="button" class="btn btn-success">Enviar</button>
			</div>
		</form>
	</div>
		<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); //echo $rodape; //insere rodapé ?>
</body>
