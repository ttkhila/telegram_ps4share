<?php
	session_start();
	if(!isset($_SESSION['login']))
		header('Location: aviso.php?a=2');
		
	include 'funcoes.php';
	include_once 'classes/jogos.class.php';

	$j = new jogos();
	$plat = $j->getPlataformas();
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
	<a href="#" id="foco"></a>
	<h2>Jogos</h2>
	
	<div id="div-conteudo-abas-jogos">
		<div class="aba-jogos"> <span>Cadastro</span><span>Alteração</span></div>
		<div class="sp-erro-msg" style="display:none;"></div><!-- mensagem de erro -->
		<!-- Cadastro - Início -->
		<div id="div-cadastra-jogos" style="display:none;">
			<form id="frm-cadastra-jogos" name="frm-cadastra-jogos" method="post">
				<span>Nome:</span><span><input type="text" id="nome-jogo" name="nome-jogo" maxlength="100" /></span><br />
				<span>Plataforma:</span>
				<span>
					<select id="plataforma" name="plataforma">
						<?php
						while($p = $plat->fetch_object()){
							echo "<option value='".$p->id."'>".stripslashes(utf8_decode($p->nome))."</option>";
						}
						?>
					</select>
				</span><br />
				<span>&nbsp;</span><span><button>Confirma</button></span>
			</form>
		</div>
		<!-- Cadastro - Fim -->
		
		<!-- Alteração - Início -->
		<div id="div-altera-jogos" style="display:block;">
			<span>Busca:</span><input type="text" name="jogo-nome-altera" id="jogo-nome-altera_autocomplete" autocomplete="off" style="width:250px;" placeholder="Digite parte do nome do jogo..." />
			<form id="frm-altera-jogos" name="frm-altera-jogos" method="post" style="display:none;margin-top:20px;">
				<input type="hidden" name="jogo-nome-altera_id" id="jogo-nome-altera_id" />
				<span>Nome:</span><span><input type="text" id="nome-jogo-altera" name="nome-jogo-altera" maxlength="100" /></span><br />
				<span>Plataforma:</span>
				<span>
					<select id="plataforma-altera" name="plataforma-altera">
						<?php
						$plat->data_seek(0);
						while($p = $plat->fetch_object()){
							echo "<option value='".$p->id."'>".stripslashes(utf8_decode($p->nome))."</option>";
						}
						?>
					</select>
				</span><br />
				<span>&nbsp;</span><span id="sp-ativo-altera"></span><br /><br />
				<span>&nbsp;</span><span><button>Alterar</button></span>
			</form>
		</div>
		<!-- Alteração - Fim -->
		
		<!-- Exclusão - Início -->
		<div id="div-exclui-jogos" style="display:none;"></div>
		<!-- Exclusão - Fim -->
	</div>
	
	
	<br /><br /><br />
	<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
	
</html>
