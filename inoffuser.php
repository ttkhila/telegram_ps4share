<?php
	session_start();
	if(!isset($_SESSION['login']))
		header('Location: aviso.php?a=2');
	else {
		include_once 'classes/usuarios.class.php';
		$u = new usuarios();
		$adm = $u->is_adm($_SESSION['ID']);
		if(!$adm) header('Location: index.php');
	}
	//include_once 'classes/recomendacoes.class.php';
	//include_once 'classes/grupos_acesso.class.php';
	//include_once 'classes/compartilhamentos.class.php';
	include 'funcoes.php';
	
	$indPend = $u->getIndicadosPendentes();
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script type="text/javascript" src="js/lib/jquery.mask.min.js"/></script>
<script>
	$(function(){ 
		$("form").submit(function(e){
			e.preventDefault(); //previne o evento 'normal'

			$(this)[0].submit();
		});
		
		$("#btn-id-email").click(function(e){
			e.preventDefault(); //previne o evento 'normal'
				
			var login = $("#txtLogin").val();
			var initLogin = login.substr(0, 3).toLowerCase();
			
			var tel = $("#txtTelefone").val();
			var finTel = tel.substr(-3, 3);
			
			$("#txtIdEmail").val(initLogin+""+finTel);
			//alert(initLogin+""+finTel);
		});
	});	
</script>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- Conteúdo Principal: Início -->
	<a href="#" id="foco"></a>
	 <h1 class="page-header">Inclui usuários da planilha</h1>
	<form role="form" method="post" action="processa_inoffuser.php">
		<div id="form-group">
			<label>Nome</label>
			<input class="form-control" type="text" id="txtNome" name="txtNome" />
		</div>	
		
		<div id="form-group">
			<label>ID</label>
			<input class="form-control" type="text" id="txtLogin" name="txtLogin" />
		</div>
		
		<div id="form-group">
			<label>Celular</label>
			<input class="form-control" type="tel" id="txtTelefone" name="txtTelefone" />
			<script type="text/javascript">$("#txtTelefone").mask("(00) 0000-00009");</script>
		</div>		
		
		<div id="form-group">
			<label>E-mail</label>
			<input class="form-control" type="email" id="txtEmail" name="txtEmail" />
		</div>	
		
		<div id="form-group">                      			
			<label>Indicado Por:</label>
			<input type="hidden" name="original1_id" id="original1_id" />
			<input type="text" name="original1" class="form-control" id="original1_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
		</div>
		
		<div id="form-group">
			<label>ID E-mail</label>
			<input class="form-control" type="text" id="txtIdEmail" name="txtIdEmail" />
			<button type="button" class="btn btn-success" id="btn-id-email">Preencher</button>
		</div><br />
		<button type="submit" class="btn btn-primary">Enviar</button>
	</form>
	
	<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
</html>
