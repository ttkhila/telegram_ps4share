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
			<li class="active"><a href="#aba-cadastros" data-toggle="tab">Cadastros</a></li>
			<li><a href="#aba-logs" data-toggle="tab">Logs</a></li>
			<li><a href="#aba-grupos" data-toggle="tab">Grupos</a></li>
			<li><a href="#aba-avisos" data-toggle="tab">Avisos</a></li>
			<li><a href="#aba-relatorios" data-toggle="tab">Relatórios</a></li>
		</ul>
		
		<div id="my-tab-content" class="tab-content">
			
			<!-- ABA CADASTROS - INICIO -->
			<div class="tab-pane active" id="aba-cadastros" style="margin-top:5px;">
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
										<td>".stripslashes(utf8_decode($dados->nome))."</td>
										<td>".stripslashes(utf8_decode($dados->email))."</td>
										<td><label id='lbl_tel'>".$dados->telefone."</label></td>
										<td><a href='perfil_usuario.php?user=".$dados->indicado_por."' target='_blank' title='".stripslashes(utf8_decode($dados->nomeUsu))."'>".stripslashes(utf8_decode($dados->login))."</a></td>
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
			</div>
			<!-- ABA CADASTROS - FIM -->
			
			<div class="tab-pane" id="aba-logs">
				 Logs 
			</div>
			
			<div class="tab-pane" id="aba-grupos">
				 Grupos 
			</div>
			
			<div class="tab-pane" id="aba-avisos">
				 Avisos 
			</div>
			
			<div class="tab-pane" id="aba-relatorios">
				 Relatórios 
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


