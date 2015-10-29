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
	include_once 'classes/grupos_acesso.class.php';
	include_once 'classes/jogos.class.php';
	include 'funcoes.php';

	$j = new jogos();
	$plat = $j->getPlataformas();
	
	$ga = new grupos_acesso();
	
	$indPend = $u->getIndicadosPendentes();
	$allUser = $u->retornaTudoQuery();
	
	$grupos = $ga->retornaTudo('nome');
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script type="text/javascript" src="js/lib/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="js/lib/jquery.mask.min.js"/></script>
<link href="css/blue/style.css" rel="stylesheet" />
<script>
	$(function(){ 
	
		
		$("#btn-envia-busca").click(function(){
			var $dados = {}; //Object JSON
			$dados.jogo_id = $("#jogo1_id").val();
			$dados.comprador_id = $("#original2_id").val();
			$dados.nome = $("#txtNome").val();
			//alert($dados.comprador_id); return;
		
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
		
		//LOGS
		$("#btn-envia-busca-logs").click(function(){
			var $dados = {}; //Object JSON
			$dados.usuario_id = $("#original1_id").val();
			$dados.ultimos = $("#txtLogUltimos").val();
			if($dados.ultimos == "") $dados.ultimos = 0;
			//alert($dados.usuario_id); return;

			var pars = { dados: $dados,  funcao: 'executaFiltroAdmLogs'};
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
					$('.tablesorter').trigger('update');//reseta consulta anterior. Consertou o problema do setTimeout
					$("#collapseTwo2 tbody").html(data);
				}	
			});
		});
		
		// ORDENAÇÃO DA TABELA DE LOGS
		$('#tab-log').tablesorter({
			headers: { 0: 
				{ sorter: false }	
			},
		})
		
		// ORDENAÇÃO DA TABELA DE USUÁRIOS
		$('#tab-user').tablesorter({
			headers: { 
				0: {
					sorter: "text"
				},
				1: {
					sorter: "text"
				},
				2: {
					sorter: "text"
				},
				3: {
					sorter: false
				},
				4: { 
					sorter: "text"
				},
				5: {
					sorter: "text"
				},
				6: {
					sorter: "text"
				},
				7: { 
					sorter: false 
				}
			},
		})
		
		$('#abas-adm').tab();
		
		$(".mskTel").mask("(00) 0000-00009");
		
		$('#usuario-aviso_autocomplete').simpleAutoComplete('autocomplete_ajax.php',{
		autoCompleteClassName: 'autocomplete',
			selectedClassName: 'sel',
			attrCallBack: 'rel',
			identifier: 'original'
		},usuarioAvisoCallback);

		function usuarioAvisoCallback( par ){ 
			$('#usuario-aviso_autocomplete').val("");
			$("#selUsuario").append("<option value='"+par[0]+"'>"+par[1]+"</option>");
		}
	});	
</script>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- Conteúdo Principal: Início -->
	<h2 class="page-header">Administrativo</h2>
	
	<div>
		<ul class="nav nav-tabs" id="abas-adm" data-tabs="tabs">
			<li><a href="#aba-cadastros" data-toggle="tab">Usuários</a></li>
			<li><a href="#aba-logs" data-toggle="tab">Logs</a></li>
			<li class="active"><a href="#aba-grupos" data-toggle="tab">Grupos</a></li>
			<li><a href="#aba-avisos" data-toggle="tab">Avisos</a></li>
			<li><a href="#aba-jogos" data-toggle="tab">Jogos</a></li>
			<li><a href="#aba-relatorios" data-toggle="tab">Relatórios</a></li>
		</ul>
		
		<div id="my-tab-content" class="tab-content">

			<div class="tab-pane" id="aba-cadastros" style="margin-top:5px;"><!-- ABA CADASTROS - INICIO -->
				<div class="panel-group" id="accordion3" role="tablist" aria-multiselectable="true" style="margin-top:5px;">
					
					<div class="panel panel-primary">
						<div class="panel-heading" role="tab" id="headingOne4">
							<h4 class="panel-title">
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion3" href="#collapseOne4" aria-expanded="true" aria-controls="collapseOne4">
									<span class="glyphicon glyphicon-user"></span> Gerenciar Cadastros
								</a>
							</h4>
						</div>
						<div id="collapseOne4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne4">
							<div class="panel-body">
							
								<div class="alert alert-info" style="margin-top:5px;">
									<b><u>Dicas:</u></b><br />
									<span class="glyphicon glyphicon-hand-right"></span> Clique nos cabeçalhos da tabela abaixo para ordenar os campos de forma crescente 
									ou decrescente.<br />
									<span class="glyphicon glyphicon-hand-right"></span> Clique em cima do dado do usuário que deseja alterar para editar seu valor. Obs.: Nem todos os dados 
									podem ser editados.
								</div>
							
								<table class="table table-hover tablesorter" id="tab-user">
									<thead>
										<tr>
											<th class="header">ID (login)</th>
											<th class="header">Nome</th>
											<th class="header">E-mail</th>
											<th>Celular</th>
											<th class="header">Telegram ID</th>
											<th class="header">ID E-mail</th>
											<th class="header">Grupo de Acesso</th>
											<th>
												Ações
												<span class='glyphicon glyphicon-info-sign' data-toggle='tooltip' data-placement='top' data-html='true' 
													title='
														<ul>
															<li>
																<b>Inativar:</b> Usuário fica sem acesso ao sistema, mas a apresentação de seu login não fica com nenhum alerta visível para outros usuários. 
																Essa opção pode ser usada quando o usuário, por exemplo, tem alguma pendência cadastral ou quando se quer suspender o usuário por determinado tempo.
															</li>
															<li>
																<b>Banir:</b> Usuário fica sem acesso ao sistema e a apresentação de seu login fica com status <em>"Usuário Banido"</em> visível para outros usuários. 
																Essa opção pode ser revertida.
															</li>
														</ul>'>
												</span> 
											</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$saida = "";
										while($user = $allUser->fetch_object()){
											$status = "";
											if($user->ativo == 0){ $status .= "linha-inativa "; $role = 1; $txtButton = "Ativar"; }
											else { $status .= ""; $role = 0; $txtButton = "Inativar"; }
											
											if($user->banido == 0){ $status .= ""; $roleBan = 1; $txtButtonBan = "Banir"; }
											else { $status .= "linha-banida "; $roleBan = 0; $txtButtonBan = "Reintegrar"; }
											$saida .= "
												<tr id='tr-usuario_".$user->id."' class='$status'>
													<td rel='login'>
														<div class='div-float-edit form-inline' style='display:none;'>
															<input type='text' value='".stripslashes($user->login)."' style='width:125px;' />
															<button class='btn btn-xs btn-success' name='edita-cadastro'>ok</button>
														</div>
														<span class='sp-clicavel'>".stripslashes($user->login)."</span>
													</td>
													<td rel='nome'>
														<div class='div-float-edit' style='display:none;'>
															<input type='text' value='".stripslashes($user->nome)."' />
															<button class='btn btn-xs btn-success' name='edita-cadastro'>ok</button>
														</div>
														<span class='sp-clicavel'>".stripslashes($user->nome)."</span>
													</td>
													<td rel='email'>
														<div class='div-float-edit' style='display:none;'>
															<input type='text' value='".stripslashes($user->email)."' />
															<button class='btn btn-xs btn-success' name='edita-cadastro'>ok</button>
														</div>
														<span class='sp-clicavel'>".stripslashes($user->email)."</span>
													</td>
													<td rel='telefone'>
														<div class='div-float-edit' style='display:none;'>
															<input type='text' class='mskTel' value='".$user->telefone."' style='width:105px;' />
															<button class='btn btn-xs btn-success' name='edita-cadastro'>ok</button>
														</div>
														<span class='mskTel sp-clicavel'>".$user->telefone."</span>
													</td>
													<td>".$user->telegram_id."</td>
													<td rel='id_email'>
														<div class='div-float-edit' style='display:none;'>
															<input type='text' value='".$user->id_email."' style='width:50px;' />
															<button class='btn btn-xs btn-success' name='edita-cadastro'>ok</button>
														</div>
														<span class='sp-clicavel'>".$user->id_email."</span>
													</td>
													<td rel='grupo_acesso_id'>
														<div class='div-float-edit' style='display:none;'>
															<select>";
															while ($g = $grupos->fetch_object()){
																if($user->grupo_acesso_id == $g->id)
																$saida .= "
																	<option value='".$g->id."' selected>".stripslashes($g->nome)."</option>
																";
																else
																$saida .= "
																	<option value='".$g->id."'>".stripslashes($g->nome)."</option>
																";
															}
															$grupos->data_seek(0);
											$saida .= "
															</select>
															<button class='btn btn-xs btn-success' name='edita-cadastro'>ok</button>
														</div>
														<span class='sp-clicavel'>".stripslashes($user->grupo)."</span>
													</td>
													<td>
														<button data-role='$role' class='btn btn-xs btn-default' name='btn-inativar-user'>$txtButton</button>
														<button data-role='$roleBan' class='btn btn-xs btn-warning' name='btn-banir-user'>$txtButtonBan</button>
													</td>
												</tr>";
										}
										echo $saida;
									?>
									</tbody>
								</table>
							</div><!-- panel-body -->
						</div><!-- collapseOne4 -->
					</div><!-- panel panel-primary -->
					
					<div class="panel panel-warning">
						<div class="panel-heading" role="tab" id="headingOne3">
							<h4 class="panel-title">
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion3" href="#collapseOne3" aria-expanded="true" aria-controls="collapseOne3">
									<span class="glyphicon glyphicon-time"></span> Indicações Pendentes
								</a>
							</h4>
						</div>
						<div id="collapseOne3" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne3">
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
												<td>".stripslashes($dados->email)."</td>
												<td><label id='lbl_tel'>".$dados->telefone."</label></td>
												<td><a href='perfil_usuario.php?user=".$dados->indicado_por."' target='_blank' title='".stripslashes($dados->nomeUsu)."'>".stripslashes($dados->login)."</a></td>
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
							</div><!-- panel-body -->
						</div><!-- collapseOne3 -->
					</div><!-- panel panel-warning -->

				</div><!-- panel-group -->
			</div><!-- ABA CADASTROS - FIM -->
	
			<div class="tab-pane" id="aba-logs"><!-- ABA LOGS - INICIO -->
				 <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true" style="margin-top:5px;">
					<div class="panel panel-info">
						<div class="panel-heading" role="tab" id="headingOne2">
							<h4 class="panel-title">
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne2" aria-expanded="true" aria-controls="collapseOne2">
									<span class="glyphicon glyphicon-filter"></span> Pesquisa de Logs
								</a>
							</h4>
						</div>
						<div id="collapseOne2" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne2">
							<div class="panel-body" id="form-busca">
								<div class="alert alert-warning">
									<span class="glyphicon glyphicon-info-sign"></span> Defina um ou mais filtros abaixo para auxiliar na busca. 
									Em <em>"Quantidade de Registros"</em>, se quiser apresentar TODOS os logs, deixe o campo vazio ou informe 0 (zero).
								 </div>
								<div class="form-group">
									<div class="form-group col-md-12">
										<label for="txtNome" class="control-label col-sm-2">- por Usuário (ID)</label>
										<div class="col-sm-8">     
											<input type="hidden" name="original1_id" id="original1_id" />
											<input type="text" name="original1" class="form-control" id="original1_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
										</div>
									</div>
									<div class="form-group col-md-12">
										<label for="txtLogUltimos" class="control-label col-sm-2">- por qtd. de registros</label>
										<div class="col-sm-10- form-inline">     
											Últimos <input type="text" class="form-control" name="txtLogUltimos" id="txtLogUltimos" value="30" /> registros. 
										</div>
									</div>

									<div class="form-group col-md-12">
										<button id="btn-envia-busca-logs" class="btn btn-primary"  data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo2" aria-expanded="false" aria-controls="collapseTwo2">Buscar</button>
									</div>
								</div><!-- form-group -->
							</div><!-- panel-body -->
						</div><!-- collapseOne -->
					</div><!-- panel panel-default -->
				
					<div class="panel panel-default">
						<div id="collapseTwo2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo2">
							<div class="panel-body">
								<table class="table table-striped tablesorter" id="tab-log">
									<thead>
										<tr>
											<th>Dia/Hora</th>
											<th class="header">Usuário (ID)</th>
											<th class="header">Ação</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div><!-- panel-body -->
						</div><!-- collapseTwo -->
					</div><!-- panel panel-default -->
				</div><!-- panel-group -->
			</div><!-- ABA LOGS- FIM -->

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
											<input type="hidden" name="original2_id" id="original2_id" />
											<input type="text" name="original2" class="form-control" id="original2_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
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
				
					<div class="panel panel-default"><!-- Resultado da busca -->
						<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
							<div class="panel-body">	
							</div><!-- panel-body -->
						</div><!-- collapseTwo -->
					</div><!-- panel panel-default -->
					
					<div class="panel panel-warning"><!-- Grupos Inativos -->
						<div class="panel-heading" role="tab" id="headingThree">
							<h4 class="panel-title">
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
									<span class="glyphicon glyphicon-eye-close"></span> Grupos Inativos
								</a>
							</h4>
						</div>
						<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
							<div class="panel-body">
							</div><!-- panel-body -->
						</div><!-- collapseThree -->
					</div><!-- panel panel-default -->
					
				</div><!-- panel-group -->
			</div><!-- ABA GRUPOS - FIM -->
			
			<div class="tab-pane" id="aba-avisos"><!-- ABA AVISOS - INICIO -->	
				<div class="alert alert-warning" style="margin-top:5px;">
					<span class="glyphicon glyphicon-info-sign"></span> Escolha uma das opções de envio de avisos abaixo
				 </div>
				
				 <div class='panel-group' id="accordion-avisos" role="tablist" aria-multiselectable="true">
					<div class='panel panel-success'>
						<div class='panel-heading' role="tab" id="headingOne-avisos">
							<h4 class="panel-title">
								<a role="button" data-toggle="collapse" data-parent="#accordion-avisos" href="#collapseOne-avisos" aria-expanded="false" aria-controls="collapseOne-avisos">
									<span class="glyphicon glyphicon-envelope"></span> Enviar aviso a todos os usuários
								</a>
							</h4>
						</div>
						<div id="collapseOne-avisos" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne-avisos">
							<div class='panel-body'>
								<div class='form-group'>
									<label>Mensagem</label>
									<textarea maxlength="200" class='form-control'></textarea>
									<small>Máximo 200 caracteres</small><br /><br />
									<button name='btn-envia-avisos' class='btn btn-primary' id='aviso_1'>Enviar</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class='panel panel-primary'>
						<div class='panel-heading' role="tab" id="headingTwo-avisos">
							<h4 class="panel-title">
								<a role="button" data-toggle="collapse" data-parent="#accordion-avisos" href="#collapseTwo-avisos" aria-expanded="false" aria-controls="collapseTwo-avisos">
									<span class="glyphicon glyphicon-envelope"></span> Enviar aviso a um grupo de usuários
								</a>
							</h4>
						</div>
						<div id="collapseTwo-avisos" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo-avisos">
							<div class='panel-body'>
								<div class='form-group'>
									<label>Enviar a: </label>
									<select class="form-control">
									<?php
										while ($g = $grupos->fetch_object()){
											echo "<option value='".$g->id."'>".stripslashes($g->nome)."</option>";
										}
									?>
									</select>
									<br />
									<label>Mensagem</label>
									<textarea maxlength="200" class='form-control'></textarea>
									<small>Máximo 200 caracteres</small><br /><br />
									<button name='btn-envia-avisos' class='btn btn-primary' id='aviso_2'>Enviar</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class='panel panel-danger'>
						<div class='panel-heading' role="tab" id="headingThree-avisos">
							<h4 class="panel-title">
								<a role="button" data-toggle="collapse" data-parent="#accordion-avisos" href="#collapseThree-avisos" aria-expanded="false" aria-controls="collapseThree-avisos">
									<span class="glyphicon glyphicon-envelope"></span> Enviar aviso a usuários selecionados
								</a>
							</h4>
						</div>
						<div id="collapseThree-avisos" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree-avisos">
							<div class='panel-body'>
								<div class='form-group'>
									<label>Usuario</label>
									<input type="text" name="usuario-aviso" class="form-control" id="usuario-aviso_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
									<br />
									<label>Selecionados</label>
									<select class="form-control" id="selUsuario" multiple="selUsuario" style="height:150px;"></select>
									<br />
									<label>Mensagem</label>
									<textarea maxlength="200" class='form-control'></textarea>
									<small>Máximo 200 caracteres</small><br /><br />
									<button name='btn-envia-avisos' class='btn btn-primary' id='aviso_3'>Enviar</button>
								</div>
							</div>
						</div>
					</div>
				 </div>
			</div><!-- ABA AVISOS - FIM -->
			
			<div class="tab-pane" id="aba-jogos"><!-- ABA JOGOS - INICIO -->
				 <div class='panel-group' id="accordion-jogos" role="tablist" aria-multiselectable="true" style="margin-top:5px;">
					<div class='panel panel-primary'>
						<div class='panel-heading' role="tab" id="headingOne-jogos">
							<h4 class="panel-title">
								<a role="button" data-toggle="collapse" data-parent="#accordion-jogos" href="#collapseOne-jogos" aria-expanded="false" aria-controls="collapseOne-jogos">
									<span class="glyphicon glyphicon-plus"></span> Incluir
								</a>
							</h4>
						</div>
						<div id="collapseOne-jogos" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-jogos">
							<div class='panel-body'>		
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
													echo "<option value='".$p->id."'>".stripslashes($p->nome)."</option>";
												}
												?>
											</select>
										</div>
									</div>
									<div class='form-group'>
										<div class="col-sm-offset-1 col-sm-4">
											<p class="bg-danger" id="p-erro-msg" style="display:none;"></p>
											<p class="bg-success" id="p-sucesso-msg" style="display:none;"></p>
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
						</div>
					</div>
					
					<div class='panel panel-warning'>
						<div class='panel-heading' role="tab" id="headingTwo-jogos">
							<h4 class="panel-title">
								<a role="button" data-toggle="collapse" data-parent="#accordion-jogos" href="#collapseTwo-jogos" aria-expanded="false" aria-controls="collapseTwo-jogos">
									<span class="glyphicon glyphicon-edit"></span> Alterar
								</a>
							</h4>
						</div>
						<div id="collapseTwo-jogos" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo-jogos">
							<div class='panel-body'>
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
													echo "<option value='".$p->id."'>".stripslashes($p->nome)."</option>";
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
									<div class='form-group'>
										<div class="col-sm-offset-1 col-sm-4">
											<p class="bg-danger" id="p2-erro-msg" style="display:none;"></p>
											<p class="bg-success" id="p2-sucesso-msg" style="display:none;"></p>
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
						</div>
					</div>
				</div><!-- panel-group -->
			</div><!-- ABA JOGOS - FIM -->
			
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


