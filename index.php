<?php
	session_start();
	//if(!isset($_SESSION['login']))
		//header('Location: aviso.php?a=2');
	include 'funcoes.php';
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script>
	$(function(){ 
		$(".deleterow").on("click", function(){
			var $killrow = $(this).parent('tr');
			var $aviso = parseInt($(this).parent('tr').attr('id').split("_")[1]); 
			$killrow.addClass("danger");
			var pars = { aviso: $aviso, funcao: 'removeAviso'};
			$.ajax({
				url: 'funcoes_ajax.php',
				type: 'POST',
				contentType: "application/x-www-form-urlencoded;charset=UFT-8",
				data: pars,
				beforeSend: function() { $("img.pull-right").fadeIn('fast'); },
				complete: function(){ $("img.pull-right").fadeOut('fast'); },
				success: function(data){ 
					$killrow.fadeOut(1000, function(){
						$(this).remove();
					});
				}	
			});	
		});
		
		$(".readrow").on("click", function(){
			if($(this).html() == "") return false;
			var $readrow = $(this).parent('tr').find('td:first img');
			var $this_tr = $(this).parent('tr');
			var $aviso = parseInt($(this).parent('tr').attr('id').split("_")[1]); 
			var pars = { aviso: $aviso, funcao: 'marcaLidoAviso'};
			$.ajax({
				url: 'funcoes_ajax.php',
				type: 'POST',
				contentType: "application/x-www-form-urlencoded;charset=UFT-8",
				data: pars,
				beforeSend: function() { $("img.pull-right").fadeIn('fast'); },
				complete: function(){ $("img.pull-right").fadeOut('fast'); },
				success: function(data){ 
					$readrow.fadeOut(1000,function(){
						$readrow.prop({'src': 'img/lida.png', 'title': 'Aviso lido'}).fadeIn(1000);
						$this_tr.find(".readrow").html("");
					});
				}	
			});
		});	
	});	
</script>
</head>
<body>
<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- aqui inicia o corpo -->
	<h2 class="page-header">Telegram Share - Painel do Usuário</h2>
	<?php
	if(!isset($_SESSION['login'])){
		echo "<div class='panel'>Faça <a href='login.php'>login</a> para visualizar sua área personalizada.</div>";
	} else {
		include 'classes/avisos.class.php';
		$a = new avisos();
		$avisos = $a->getAvisos($_SESSION['ID']);
	?>
		<div class="panel panel-primary">
			<div class="panel-heading">Quadro de Avisos</div>
			<div class="panel-body">
				<div class="table-responsive pre-scrollable">
					<table class="table table-striped table-hover">
						<tbody>
						<?php 
							if ($avisos->num_rows == 0){ echo "<tr><th colspan='4'>Não há avisos no momento.</th></tr>"; }
							else { 
								while($dados = $avisos->fetch_object()){ 
									if($dados->lido == 1){ 
										$lido = "<img src='img/lida.png' title='Aviso lido' />"; 
										$lido_icon = "";
									} else {
										$lido = "<img src='img/nao_lida.jpg' title='Aviso não lido' />"; 
										$lido_icon = "<div title='Marcar como lido' class='glyphicon glyphicon-eye-open'></div>";
									}
									echo "
									<tr id='aviso_".$dados->id."'>
										<td>$lido</td>
										<td>".stripslashes(utf8_decode($dados->texto))."</td>
										<td class='readrow'>$lido_icon</td>
										<td class='deleterow'><div title='Apagar aviso' class='glyphicon glyphicon-remove'></div></td>
									</tr>";
								}
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php
	}
	?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <!--<script src="js/vendor/holder.min.js"></script>-->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--<script src="js/ie10-viewport-bug-workaround.js"></script>-->
    
	<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
</html>
