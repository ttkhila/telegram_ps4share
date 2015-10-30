<?php

?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
	<script type="text/javascript" src="js/lib/jquery.mask.min.js"/></script>
	<script>
		$(function(){ 
		});	
	</script>
	</head>
	<body>
		<?php //$menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
		<!-- Conteúdo Principal: Início -->
		<div class="container">
			<div class="row">
				<h1 class="page-header">Cadastro</h1>
				<div class="panel panel-primary">
					<div class="panel-heading">Preencha todos os campos abaixo</div>
					<div class="panel-body">
						<form id="frm-cadastro" role="form">
							<div class="form-group">
								<label for="nome">Nome</label>
								<input type="text" class="form-control" name="nome" id="nome" maxlength="60" required="" />
							</div>
							<div class="form-group">
								<label for="login">ID (PSN)</label>
								<input type="text" class="form-control" name="login" id="login" maxlength="16" placeholder="Digite sua ID PSN/Live" pattern="(^[\w-]{3,16})$" required="" />
							</div>
							<div class="form-group">
								<label for="email">E-mail</label><br />
								<span>E-mail preenchido automaticamente aqui</span>
							</div>
							<div class="form-group">
								<label for="telefone">Celular</label>
								<input class="form-control" type="tel" name="telefone" id="telefone" value="" pattern="\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}" required="" />
								<script type="text/javascript">$("#telefone").mask("(00) 0000-00009");</script>
							</div>
							<div class="form-group">
								<label for="senha">ID (PSN)</label>
								<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" data-html="true" 
									title="Sua senha deve ter entre 6 e 10 caracteres,<br />podendo conter letras, números e os seguintes<br /> caracteres especiais: (_ - ! # @ +)."></span>
								<input type="password" class="form-control" name="senha" id="senha" maxlength="10" pattern="(^[\w-!#@+]{6,10})$" required="" placeholder="Digite a senha " />
								<input type="password" class="form-control" name="senha2" id="senha2" maxlength="10" pattern="(^[\w-!#@+]{6,10})$" required="" placeholder="Re-digite a senha" />
							</div>
							<p class="bg-danger" id="sp-erro-msg" style="display:none;"></p>
							<div class="form-group">
								<button class="btn btn-primary" type="submit">Enviar</button>
							</div>
						</form>
					</div>
				</div> 
			</div><!-- row -->
		</div><!-- container -->
	</body>
</html>
