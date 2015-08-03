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
		$('#abas-jogos').tab();
				
		$('#abas-jogos a').click(function(e){
			var aba = $(this).attr('id');
			if(aba == "aba-alterar"){ //reseta a aba alterar
				$("#jogo-nome-altera_autocomplete").val("");
				$("#frm-altera-jogos").hide();
				$("#aba-altera-jogos form")[0].reset();
;			}
		});
		/* MODOS DE ATIVAÇÃO
		$('#myTab a[href="#profile"]').tab('show'); // Select tab by name
		$('#myTab a:first').tab('show'); // Select first tab
		$('#myTab a:last').tab('show'); // Select last tab
		$('#myTab li:eq(2) a').tab('show'); // Select third tab (0-indexed)
		*/	
	});	
</script>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- Conteúdo Principal: Início -->
	<a href="#" id="foco"></a>
	 <h1 class="page-header">Gerenciar Jogos</h1>
	<div id="div-conteudo-abas-jogos">
		<ul class="nav nav-tabs" id="abas-jogos" data-tabs="tabs">
		  <li class="active"><a href="#aba-cadastro-jogos" data-toggle="tab" id="aba-incluir">Incluir</a></li>
		  <li><a href="#aba-altera-jogos" data-toggle="tab" id="aba-alterar">Alterar</a></li>
		</ul>

		<div class="list-group">
			<label for="" class="col-sm-1 control-label"></label>
			<label id="sp-erro-msg" class="col-sm-5"></label><!-- mensagem de erro -->
		</div>
		<div class="list-group">
			<label for="" class="col-sm-1 control-label"></label>
			<label id="sp-sucesso-msg" class="col-sm-5"></label><!-- mensagem de sucesso -->
		</div><br /><br />
		
		<div id="my-tab-content" class="tab-content">
			<!-- Cadastro - Início -->
			<div class="tab-pane active" id="aba-cadastro-jogos">
				<form id="frm-cadastra-jogos" name="frm-cadastra-jogos" class="form-horizontal" method="post">
					<div class="form-group">
						<label for="nome-jogo" class="col-sm-1 control-label">Nome:</label>
						<div class="col-sm-4">
							<input type="text" id="nome-jogo" name="nome-jogo" class="form-control" maxlength="100" />
						</div>
					</div>
					<div class="form-group">
						<label for="plataforma-altera" class="col-sm-1 control-label">Plataforma:</label>
						<div class="col-sm-4">
							<select class="form-control" id="plataforma" name="plataforma">
								<?php
								while($p = $plat->fetch_object()){
									echo "<option value='".$p->id."'>".stripslashes(utf8_decode($p->nome))."</option>";
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-1 control-label"></label>
						<div class="btn-group col-sm-4">
							<button class="btn btn-success">Confirma</button>
						</div>
					</div>
				</form>
			</div>
			<!-- Cadastro - Fim -->
			
			<!-- Alteração - Início -->
			<div id="aba-altera-jogos" class="tab-pane">
				<div class="form-group-sm" style="margin-bottom:40px;">
					<label class="col-sm-1 control-label">Busca:</label>
					<input class="form-control" type="text" name="jogo-nome-altera" 
						id="jogo-nome-altera_autocomplete" autocomplete="off" style="width:250px;" placeholder="Digite parte do nome do jogo..." />
				</div>

				<form id="frm-altera-jogos" name="frm-altera-jogos" class="form-horizontal" method="post" style="display:none;">
					<input type="hidden" name="jogo-nome-altera_id" id="jogo-nome-altera_id" />
					<div class="form-group">
						<label for="nome-jogo-altera" class="col-sm-1 control-label">Nome:</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" id="nome-jogo-altera" name="nome-jogo-altera" maxlength="100" />
						</div>
					</div>
					
					<div class="form-group">
						<label for="plataforma-altera" class="col-sm-1 control-label">Plataforma:</label>
						<div class="col-sm-4">
							<select class="form-control" id="plataforma-altera" name="plataforma-altera">
								<?php
								$plat->data_seek(0);
								while($p = $plat->fetch_object()){
									echo "<option value='".$p->id."'>".stripslashes(utf8_decode($p->nome))."</option>";
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-1 control-label"></label>
						<div class="col-sm-4">
							<span class="control-label" id="sp-ativo-altera"></span>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-1 control-label"></label>
						<div class="btn-group col-sm-4">
							<button class="btn btn-success">Alterar</button>
						</div>
					</div>
					
				</form>

			</div>
			<!-- Alteração - Fim -->
		</div>
	</div>
	
	<br /><br /><br />
	<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
</html>
