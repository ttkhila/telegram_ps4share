<?php
	session_start();
	include 'funcoes.php';
	$cod = $_GET['a'];
	
	switch($cod){
		case 1:
			$aviso = "Já existe cadastro efetuado.<br />Encerre a sessão atual para realizar um novo cadastro.";
			break;
		case 2:
			$aviso = "Você precisa estar logado para visualizar esta seção.<br />Faça <a href='login.php'>login</a> ou efetue <a href='#'>cadastro</a>.";
			break;
		case 3:
			$aviso = "Você não tem permissão para acessar essa área.<br />Contate o administrador do sistema.";
			break;
		default:
			$aviso = "Erro desconhecido.";
			break;
	}
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
		<!-- Conteúdo Principal: Início --> 
		
		<div class="div-aviso"><?php echo $aviso; ?></div>
	
		<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
	
</html>
