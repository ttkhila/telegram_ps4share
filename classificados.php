<?php
	session_start();
	if(!isset($_SESSION['login']))
		header('Location: aviso.php?a=2');
	require_once 'classes/compartilhamentos.class.php';
	require_once 'classes/jogos.class.php';
	include 'funcoes.php';
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script>
	$(function(){ 
		$("#btn-envia-busca").click(function(){
			var $dados = {}; //Object JSON
			$dados.jogo_id = $("#jogo1_id").val();
			$dados.comprador_id = $("#original1_id").val();
			$dados.vaga = "";
			$.each($("#optVaga:checked"), function(i, item){
				$dados.vaga += $(this).val()+"-";
			});
			if (!$.isNumeric($.trim($("[name='filtro-valor']").val()).replace(",", ".")) && $.trim($("[name='filtro-valor']").val()) != ""){
				alert("valor precisa ser numérico");
				return false;
			} 
			var $tipoValor = parseInt($("#selValor :selected").val());
			switch($tipoValor){
				case 1:
					$dados.valor1 = $.trim($("#valor1").val()).replace(",", ".");
					$dados.valor2 = $.trim($("#valor2").val()).replace(",", ".");
					if($dados.valor1 != "" && $dados.valor2 == ""){ alert("preencha o valor 2"); return false; }
					break;
				case 2:
				case 3:
					$dados.valor1 = $.trim($("#valor1").val()).replace(",", ".");
					break;
				default:
					alert("erro");
					return false;
			}
			$("#optFechado").is(":checked") ? $dados.fechado = 1 : $dados.fechado = -1; 
			//alert($dados.comprador_id); return;
			var pars = { dados: $dados, tipoValor: $tipoValor, funcao: 'executaFiltro'};
			$.ajax({
				url: 'funcoes_ajax.php',
				type: 'POST',
				dataType: "json",
				contentType: "application/x-www-form-urlencoded;charset=UFT-8",
				data: pars,
				beforeSend: function() { $("img.pull-right").fadeIn('fast'); },
				complete: function(){ $("img.pull-right").fadeOut('fast'); },
				success: function(data){ 
					console.log(data); 
					$("#collapseTwo tbody").html(data);
				}	
			});
		});
		
		$("#selValor").change(function(){
			var opt = parseInt($(this).val());
			if(opt >= 2) $("#valor-parte2").hide();
			else $("#valor-parte2").show();
		});
		
		$("[name='filtro-valor']").keydown(function(e){
			var tecla = e.which
			//alert(tecla);
			if((tecla>47 && tecla<58) || (tecla>95 && tecla < 106)) return true;
			else{
				if (tecla==8 || tecla==0 || tecla ==188 || tecla==46) return true;
					else  return false;
				}	
		});
		
	});	
</script>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- Conteúdo Principal: Início -->
	
	<h1 class="page-header">Classificados</h1>
	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingOne">
				<h4 class="panel-title">
					<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Filtros</a>
				</h4>
			</div>
			<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
				<div class="panel-body" id="form-busca">
					<div class="form-group">
						<div class="alert text-info">
							<h4>Preencha um ou mais campos abaixo para filtrar sua pesquisa</h4>
						</div>
						<div class="form-group col-md-12">
							<label for="jogo1_id" class="control-label col-sm-2">- por Jogo</label>
							<div class="col-sm-8">     
								<input type="hidden" name="jogo_id[]" id="jogo1_id" /> 
								<input type="text" class="form-control" name="jogo[]" id="jogo1_autocomplete" placeholder="Digite parte do nome do jogo 1" />
							</div>
							<div class="col-sm-2">
								<span id="jogo1_check"><img src="" /></span>
							</div>
						</div>
						<div class="form-group col-md-12">
							<label class="control-label col-sm-2">- por Vendedor (ID):</label>
							<div class="col-sm-8"> 
								<input type="hidden" name="original1_id" id="original1_id" />
								<input type="text" name="original1" class="form-control" id="original1_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
							</div>
						</div>
						<div class="form-group col-md-12">
							<label class="control-label col-sm-2">- por Vaga:</label>
							<div class="form-group col-sm-8">
								<label><input type="checkbox" id="optVaga" name="optOrig1" value="1" /><span>&nbsp;&nbsp;Original 1</span>&nbsp;&nbsp;</label> &nbsp;&nbsp;
								<label><input type="checkbox" id="optVaga" name="optOrig2" value="2" /><span>&nbsp;&nbsp;Original 2</span>&nbsp;&nbsp;</label> &nbsp;&nbsp;
								<label><input type="checkbox" id="optVaga" name="optOrig3" value="3" /><span>&nbsp;&nbsp;Fantasma</span>&nbsp;&nbsp;</label> 
							</div>
						</div>
						<div class="form-group col-md-12">
							<label class="control-label col-sm-2">- por Valor:</label>
							<form class="form-inline col-sm-8">
								<select id="selValor" class="form-control">
									<option value="1" selected>entre</option>
									<option value="2">maior que</option>
									<option value="3">menor que</option>
								</select>
								<input type="text" class="form-control" id="valor1" name="filtro-valor" maxlength="8" />
								<span id="valor-parte2">&nbsp;&nbsp;e&nbsp;&nbsp;<input type="text" class="form-control" id="valor2" name="filtro-valor" maxlength="8" /></span>
							</form>
						</div>
						<div class="form-group col-md-12">
							<label class="control-label col-sm-2">- adicional:</label>
							<div class="control-label col-sm-8">
								<label><input type="checkbox" id="optFechado" name="optFechado" value="1" /><span>&nbsp;&nbsp;Somente grupos fechados</span>&nbsp;&nbsp;</label> 
							</div>
						</div>
						<div class="form-group col-md-12">
							<button id="btn-envia-busca" class="btn btn-primary"  data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Enviar</button>
						</div>
					</div><!-- form-group -->
				</div><!-- panel-body -->
			</div><!-- collapseOne -->
		</div><!-- panel panel-default -->
	
		<div class="panel panel-default">
			
			<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
				<div class="panel-body">
					<div class="table-responsive"> 
						<table class="table table-striped">
							<thead>
								<tr><th colspan="6"><label class='text-muted small'>Clique no botão azul com o nome do usuário para ver suas recomendações e os detalhes de como entrar em contato com o mesmo.</label></th></tr>
								<tr>
									<th colspan="3">&nbsp;</th>
									<th colspan="3" class="text-center success">Dados do Grupo</th>
								</tr>
								<tr class="success">
									<th >Jogo(s) na conta</th>
									<th >Proprietários das vagas atuais</th>
									<th>Preço da vaga</th>
									<th>Criador</th>
									<th>Data criação</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div><!-- table-responsive -->
				</div><!-- panel-body -->
			</div><!-- collapseTwo -->
		</div><!-- panel panel-default -->
	</div><!-- panel-group -->
	
	<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
</html>
