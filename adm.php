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
	
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script>
	$(function(){ 
		
		$('#abas-adm').tab();
		
	});	
</script>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- Conteúdo Principal: Início -->
	<h2 class="page-header">Administrativo</h2>
	
	<div>
		<ul class="nav nav-tabs" id="abas-adm" data-tabs="tabs">
			<li class="active"><a href="#aba-relatorios" data-toggle="tab">Relatórios</a></li>
			<li><a href="#aba-logs" data-toggle="tab">Logs</a></li>
			<li><a href="#aba-grupos" data-toggle="tab">Grupos</a></li>
			<li><a href="#aba-avisos" data-toggle="tab">Avisos</a></li>
		</ul>
		
		<div id="my-tab-content" class="tab-content">
			<div class="tab-pane active" id="aba-relatorios">
				 Relatórios 
			</div>
			
			<div class="tab-pane" id="aba-logs">
				 Logs 
			</div>
			
			<div class="tab-pane" id="aba-grupos">
				 Grupos 
			</div>
			
			<div class="tab-pane" id="aba-avisos">
				 Avisos 
			</div>
		</div>
	</div>
	
	<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
</html>


