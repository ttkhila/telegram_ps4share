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
<script>
	$(function(){ 
		
		$("#btn-envia-busca").click(function(){
			var $dados = {}; //Object JSON
			$dados.jogo_id = $("#jogo1_id").val();
			$dados.comprador_id = $("#original1_id").val();
			$dados.nome = $("#txtNome").val();
			//alert($dados.comprador_id)
		
			$("#optFechado").is(":checked") ? $dados.fechado = 1 : $dados.fechado = -1; 
			//console.log($dados);  return;
			var pars = { dados: $dados,  funcao: 'executaFiltroAdmGrupos'};
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
					$("#collapseTwo .panel-body").html(data);
				}	
			});
		});
		
		$("#aba-grupos").on("click", "[name=btn-excluir-grupo]", function(e){
			e.preventDefault(); //previne o evento 'normal'
			if(!confirm("Atenção: A exclusão de um grupo é um processo irreversível!\nTenha absoluta certeza de não haver outra alternativa antes de realizar essa operação.\nConfirma a opção?"))
				return false;
			var $idGrupo = parseInt($(this).attr('id').split("_")[1]);
			//alert($idGrupo);
			var pars = { idGrupo: $idGrupo,  funcao: 'excluirGrupo'};
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
					$("#aba-grupos").find("#panel-grupo_"+$idGrupo).remove();
					alert("Grupo excluído!");
				}	
			});
		});
		
		//Inativa um grupo
		$("#aba-grupos").on("click", "[name=btn-inativar-grupo]", function(e){
			e.preventDefault(); //previne o evento 'normal'
			if(!confirm("A inativação não é definitiva e pode ser revertida posteriormente.\nConfirma a opção?"))
				return false;
			var $idGrupo = parseInt($(this).attr('id').split("_")[1]);
			//alert($idGrupo); return;
			var pars = { idGrupo: $idGrupo,  funcao: 'InativarGrupo'};
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
					$("#aba-grupos").find("#panel-grupo_"+$idGrupo).remove();
					alert("Grupo inativado!");
				}	
			});
		});
		
		// GRUPOS INATIVOS
		$("[href=#collapseThree]").click(function(){
			var pars = { funcao: 'mostraGruposInativos'};
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
					$("#collapseThree .panel-body").html(data);
				}	
			});
		});
		
		//RE-ativa um grupo
		$("#aba-grupos").on("click", "[name=btn-ativar-grupo]", function(e){
			e.preventDefault(); //previne o evento 'normal'
			if(!confirm("O grupo voltará a ficar disponível para os usuários.\nConfirma a opção?"))
				return false;
			var $idGrupo = parseInt($(this).attr('id').split("_")[1]);
			//alert($idGrupo); return;
			var pars = { idGrupo: $idGrupo,  funcao: 'reativarGrupo'};
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
					$("#aba-grupos").find("#panel-grupo_"+$idGrupo).remove();
					alert("Grupo re-ativado!");
				}	
			});
		});
		
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
			<li><a href="#aba-cadastros" data-toggle="tab">Cadastros</a></li>
			<li><a href="#aba-logs" data-toggle="tab">Logs</a></li>
			<li class="active"><a href="#aba-grupos" data-toggle="tab">Grupos</a></li>
			<li><a href="#aba-avisos" data-toggle="tab">Avisos</a></li>
			<li><a href="#aba-relatorios" data-toggle="tab">Relatórios</a></li>
		</ul>
		
		<div id="my-tab-content" class="tab-content">

			<div class="tab-pane" id="aba-cadastros" style="margin-top:5px;"><!-- ABA CADASTROS - INICIO -->
				<div class="panel panel-warning">
					<div class="panel-heading"><span class="glyphicon glyphicon-time"></span> Indicações Pendentes</div>
					<div class="panel-body">
					<?php
						if (!$indPend) $saida = "<div class='col-md-12'><label>Não há indicações pendentes de confirmação.</label></div>";
						else {
							$saida = "
								<table class='table table-striped'>
									<thead>
										<tr>
											<th>Nome</th>
											<th>E-mail</th>
											<th>Celular</th>
											<th>Indicado por</th>
											<th>Ação</th>
										</tr>
									</thead>
									<tbody>
							";
							while ($dados = $indPend->fetch_object()){
								$saida .= "
									<tr>
										<td>".stripslashes($dados->nome)."</td>
										<td>".stripslashes(utf8_decode($dados->email))."</td>
										<td><label id='lbl_tel'>".$dados->telefone."</label></td>
										<td><a href='perfil_usuario.php?user=".$dados->indicado_por."' target='_blank' title='".stripslashes($dados->nomeUsu)."'>".stripslashes(utf8_decode($dados->login))."</a></td>
										<td>
											<a href='#' id='aceita-indicacao_".$dados->id."'><span class='glyphicon glyphicon-ok-sign'></span> [aceitar]</a><br />
											<a role='button' href='#' id='negar-indicacao_".$dados->id."' name='btn-negar-indicacao' data-id='".$dados->indicado_por."' data-toggle='modal' data-target='#nega-indicacao'><span class='glyphicon glyphicon-ban-circle'></span> [negar]</a><br />
										</td>
									</tr>
								";
							}
							$saida .= "</tbody></table>";
						}
						echo $saida;
					?>
					</div>
				</div>
			</div><!-- ABA CADASTROS - FIM -->
	
			<div class="tab-pane" id="aba-logs">
				 Em construção
			</div>

			<div class="tab-pane active" id="aba-grupos"><!-- ABA GRUPOS - INICIO -->
				  <div class="alert alert-warning" style="margin-top:5px;">
					<span class="glyphicon glyphicon-exclamation-sign"></span> <b>Importante:</b> Aqui os administradores podem gerenciar aspectos nos grupos que não podem ser resolvidos em outro local, como uma possível exclusão de 
					grupo ou mudança de algum dado que seria impossível em outro local, como o e-mail da conta ou remoção de algum usuário da mesma.<br />Utilize com <b>cuidado</b>
					essas opções, pois podem causar inconsistência no sistema.
				  </div>
				  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					<div class="panel panel-info">
						<div class="panel-heading" role="tab" id="headingOne">
							<h4 class="panel-title">
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
									<span class="glyphicon glyphicon-filter"></span> Pesquisa de Grupos
								</a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
							<div class="panel-body" id="form-busca">
								<div class="alert alert-success">
									<span class="glyphicon glyphicon-info-sign"></span> Defina um ou mais filtros abaixo para auxiliar na busca
								 </div>
								<div class="form-group">
									<div class="form-group col-md-12">
										<label for="txtNome" class="control-label col-sm-2">- por Nome da Conta</label>
										<div class="col-sm-8">     
											<input type="text" class="form-control" name="txtNome" id="txtNome" placeholder="Digite o nome completo da conta" />
										</div>
									</div>
									<div class="form-group col-md-12">
										<label for="jogo1_id" class="control-label col-sm-2">- por Jogo</label>
										<div class="col-sm-8">     
											<input type="hidden" name="jogo_id[]" id="jogo1_id" /> 
											<input type="text" class="form-control" name="jogo[]" id="jogo1_autocomplete" placeholder="Digite parte do nome do jogo" />
										</div>
										<div class="col-sm-2">
											<span id="jogo1_check"><img src="" /></span>
										</div>
									</div>
									<div class="form-group col-md-12">
										<label class="control-label col-sm-2">- por Usuário (ID):</label>
										<div class="col-sm-8"> 
											<input type="hidden" name="original1_id" id="original1_id" />
											<input type="text" name="original1" class="form-control" id="original1_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
										</div>
									</div>
									<div class="form-group col-md-12">
										<label class="control-label col-sm-2">- adicional:</label>
										<div class="control-label col-sm-8">
											<label><input type="checkbox" id="optFechado" name="optFechado" value="1" /><span>&nbsp;&nbsp;Somente grupos fechados</span>&nbsp;&nbsp;</label> 
										</div>
									</div>
									<div class="form-group col-md-12">
										<button id="btn-envia-busca" class="btn btn-primary"  data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Buscar</button>
									</div>
								</div><!-- form-group -->
							</div><!-- panel-body -->
						</div><!-- collapseOne -->
					</div><!-- panel panel-default -->
				
					<div class="panel panel-default">
						<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
							<div class="panel-body">
								
							</div><!-- panel-body -->
						</div><!-- collapseTwo -->
					</div><!-- panel panel-default -->
					
					<div class="panel panel-warning">
						<div class="panel-heading" role="tab" id="headingThree">
							<h4 class="panel-title">
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
									<span class="glyphicon glyphicon-eye-close"></span> Grupos Inativos
								</a>
							</h4>
						</div>
						<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
							<div class="panel-body">
								bla
							</div><!-- panel-body -->
						</div><!-- collapseThree -->
					</div><!-- panel panel-default -->
					
				</div><!-- panel-group -->
			</div><!-- ABA GRUPOS - FIM -->
			
			<div class="tab-pane" id="aba-avisos">
				 Em construção
			</div>
			
			<div class="tab-pane" id="aba-relatorios">
				 Em construção
			</div>
		</div>
		
	</div>
	
	<!-- MODAL - Nega Indicação -->
	<div class="modal fade" id="nega-indicacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel2">Motivo da Negativa</h4>
				</div>
				<div class="modal-body">
					<div class="window" id="modal-indicacao-negada"></div>
				</div><!-- modal-body -->
			</div>
		</div>
	</div><!-- modal fade -->
	
	<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
</html>


