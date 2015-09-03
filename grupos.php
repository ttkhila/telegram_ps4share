<?php
	session_start();
	if(!isset($_SESSION['login']))
		header('Location: aviso.php?a=2');
	require_once 'classes/compartilhamentos.class.php';
	require_once 'classes/jogos.class.php';
	include 'funcoes.php';

	$c = new compartilhamentos();
	$j = new jogos();
	$selfID = $_SESSION['ID'];
	$moedas = $c->recupera_moedas();
	$jogos = $j->getJogos();

	//recupera dados dos compartilhamentos existentes
	$dados1 = $c->getDadosPorUsuario($selfID);
	$dados2 = $c->getGruposAntigos($selfID);
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
	<input type="hidden" id="selfID" name="selfID" value="<?php echo $selfID; ?>" />
	<h1 class="page-header">Gerenciar Grupos</h1>
	<a href="#" id="foco"></a>
	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingOne">
				<h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">Novo Grupo</a>
				</h4>
			</div>
			<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
				<div class="panel-body">

					<p class="bg-danger col-sm-10" id="sp-erro-msg" style="display:none;"></p>
					
					<div class="form-group col-md-12">
						<label for="exampleInputnome" class="control-label col-md-2">Nome da conta</label>
						<div class="col-md-8">
							<input type="text" class="form-control" name="nome" id="nome" required="true" placeholder="Nome da conta" maxlength="60" />
						</div>
						<div class="col-md-2">
							<img src='img/help.png' width='16' height='16' data-toggle="tooltip" data-placement="right" title="Digite um nome para o grupo que identifique o(s) jogo(s) contido(s) nele ou seus integrantes." />
						</div>
					</div>
					
					<div class="form-group col-md-12">
						<label for="exampleInputEmail1" class="control-label col-md-2">Email da conta</label>
						<div class="col-md-8">
							<input type="email" class="form-control" name="email" id="email" placeholder="E-mail da conta" />
						</div>  	
						<div class="col-md-2">
							<img src='img/help.png' width='16' height='16' data-toggle="tooltip" data-placement="right" 
								title="E-mail da conta de jogo. N&atilde;o &eacute; obrigat&oacute;rio informar na criação do grupo, a n&atilde;o ser que seja um grupo j&aacute; fechado." />
						</div>
					</div>
				
					<div class="form-group col-md-12">
						<label for="exampleInputEmail1" class="control-label col-md-2">Moeda de Compra</label>
						<div class="col-md-4">
							<select class="form-control" id="moedas" name="moedas">                            
								<?php
								while($m = $moedas->fetch_object()){
									if($m->pais == "BRL") echo "<option value='".$m->id."' selected='selected'>".stripslashes(utf8_decode($m->nome))." (".$m->pais.")</option>";
									else echo "<option value='".$m->id."'>".stripslashes(utf8_decode($m->nome))." (".$m->pais.")</option>";
								}
								?>
							</select>
						</div>
					</div>
				
					<!-- JOGOS-->
					<div class="form-group col-md-12">
						<h3>
							Jogos <img src='img/help.png' width='16' height='16' data-toggle="tooltip" data-placement="right" 
								title="&Eacute; obrigat&oacute;rio o preenchimento de pelo menos um jogo." />
						</h3>
					</div>
					<div class="form-group col-md-12">
						<label for="jogo1_id" class="control-label col-sm-2">Jogo1</label>
						<div class="col-sm-8">     
							<input type="hidden" class="form-control" name="jogo_id[]" id="jogo1_id" required /> 
							<input type="text" class="form-control" name="jogo[]" id="jogo1_autocomplete" placeholder="Digite parte do nome do jogo 1" required />
						</div>
						<div class="col-sm-2">
							<span id="jogo1_check"><img src="" /></span>
						</div>
					</div>
					<div id="div-jogos-extras" class="form-group"></div><!--campos dinamicos -->
					<div class="form-group col-md-12">
						<div class="col-md-12">   
							<button class="btn btn-primary" id="btn-add-jogo" type="button">+ Jogo</button>
						</div>
					</div>

                			<!-- VAGAS-->
                			<div class="form-group col-md-12">
						<h3>
							Vagas <img src='img/help.png' width='16' height='16' data-toggle="tooltip" data-placement="right" 
							title="Na cria&ccedil;&atilde;o do grupo &eacute; obrigat&oacute;rio o preenchimento do seu pr&oacute;prio ID numa das vagas." />
						</h3>
					</div>
					<div class="form-group col-md-12">
						<form class="form-inline">
							<form class="form-group">                        			
								<label class="exampleInputEmail1">Original 1 / ID:</label>
								<input type="hidden" name="original1_id" id="original1_id" />
								<input type="text" name="original1" class="form-control" id="original1_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
								<label class="exampleInputEmail1">Valor:</label>
								<input type="text" class="form-control" name="valor" id="valor1" maxlength="10" />
								<button class="btn btn-danger" id="1">Limpar</button>

							</form>
						</form>
					</div>
					<div class="form-group col-md-12">
						<form class="form-inline">   
							<form class="form-group">	
								<label class="exampleInputEmail1">Original 2 / ID:</label>
								<input type="hidden" name="original2_id" id="original2_id" />
								<input type="text" name="original2" class="form-control" id="original2_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
								<label class="exampleInputEmail1">Valor:</label>
								<input type="text" class="form-control" name="valor" id="valor2" maxlength="10" />
								<button class="btn btn-danger" id="2">Limpar</button>
							</form>
						</form>
					</div>
					<div class="form-group col-md-12">
						<form class="form-inline">
							<form class="form-group">	
								<label class="exampleInputEmail1">Fantasma / ID:</label>
								<input type="hidden" name="original3_id" id="original3_id" />
								<input type="text" name="original3" class="form-control" id="original3_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
								<label class="sp-form-direita">Valor:</label>
								<input type="text" class="form-control" name="valor" id="valor3" maxlength="10" />
								<button class="btn btn-danger" id="3">Limpar</button><br /><br />
								<div class="checkbox">
									<label><input type="checkbox" id="fechado" name="fechado" /><span class="sp-form">&nbsp;&nbsp;Grupo j&aacute; fechado?</span>&nbsp;&nbsp;</label> 
									<label>
										<img src='img/help.png' width='16' height='16' data-toggle="tooltip" data-placement="right" 
										title="Se esse campo for marcado, ser&aacute; obrigat&oacute;rio o preenchimento de pelo menos uma vaga, os valores das vagas preenchidas, assim como o e-mail da conta criada." />
									</label>
								</div><br /><p></p>
								<button id="btn-grupo-novo" class="btn btn-success">Criar Grupo</button>
							</form>
						</form>
					</div>
                        	</div><!-- panel-body -->
			</div><!-- panel-collapse collapse -->
               </div><!-- panel panel-default -->
               
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingTwo">
				<h4 class="panel-title"><a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">Meus Grupos</a></h4>
			</div><!-- panel-heading -->
			<div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
				<div class="panel-body">
					<div id="div-listagem-grupos" class="panel panel-group container-grupos">
						<?php
							if ($dados1->num_rows == 0){
								echo "<span>Não há nenhum grupo ativo para este usuário!<br />
								Clique NOVO GRUPO acima para criar um novo grupo.
								</span>";
							} else {
								while($d = $dados1->fetch_object()){
									if($d->fechado == 1) $fechado = "<img src='img/closed.png' title='Grupo Fechado' />"; else $fechado = "<img src='img/open.png' title='Grupo Aberto' />";
									echo "<div id ='grupo_".$d->id."' class='panel'>";
										echo "<div name='div-casulo-grupo' id='grupo-titulo_".$d->id."' class='panel-title'>";
											echo "<div class='panel'><img src='img/plus.png' width='16' height='16' id='_1' name='imgMais' /> ".stripslashes(utf8_decode($d->nome));
											echo " <font color='#999'>(criado por: ".stripslashes(utf8_decode($d->login)).")</font> $fechado</div>";
										echo "</div>";
										echo "<div name='div-casulo-conteudo-grupo' id ='grupo-conteudo_".$d->id."' class='list-group col-md-12' style='display:none;'></div>";
										//echo "<hr />";
									echo "</div><br /><br />";
								}
							}
						?>
					</div><!-- div-listagem-grupos -->
				</div><!-- panel-body -->
				<div class="panel-footer">
					<ul class='list-group'>
						<li class='list-group-item active'>Legenda:</li>
						<li class='list-group-item'><div class='glyphicon glyphicon-transfer btn btn-xs btn-primary'></div>&nbsp;Informar vaga repassada</li>
						<li class='list-group-item'><div class='glyphicon glyphicon-shopping-cart btn btn-xs btn-primary'></div>&nbsp;Colocar vaga a venda (item ainda não anunciado)</li>
						<li class='list-group-item'><div class='glyphicon glyphicon-shopping-cart btn btn-xs btn-default'></div>&nbsp;Colocar vaga a venda (item já anunciado)</li>
						<li class='list-group-item'><div class='glyphicon glyphicon-trash btn btn-xs btn-primary'></div>&nbsp;Excluir usuário da vaga (somente grupos abertos)</li>
					</ul>
				</div>
				<input type="hidden" id="hidFlag" value="0" />
			</div><!-- collapseTwo -->
		</div><!-- panel panel-default -->
		
		
		
		
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingThree">
				<h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Grupos Antigos (vagas já repassadas)</a>
				</h4>
			</div>
			<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
				<div class="panel-body">
					<div id="div-listagem-grupos-antigos" class="panel panel-group">
						<?php
							if ($dados2->num_rows == 0){
								$saida = "<span>Não há grupo com vagas repassadas para ser mostrado!<br />
								Clique NOVO GRUPO acima para criar um novo grupo.
								</span>";
							} else {
								$saida = "";
								while($d = $dados2->fetch_object()){
									$saida .= "
										<div class='panel'>
											<div name='div-titulo-grupos-antigos' id='div-titulo-grupos-antigos_".$d->id."' class='panel-title'>
												<div class='panel'><img src='img/plus.png' width='16' height='16' id='_1' name='imgMais' /> ".stripslashes(utf8_decode($d->nome))."</div>
											</div>
											<div name='div-conteudo-grupos-antigos' id ='div-conteudo-grupos-antigos_".$d->id."' class='list-group col-md-12' style='display:none;'></div>
										</div>";
								}
							}
							echo $saida;
						?>
					</div>
				</div>
			</div><!-- collapseThree -->
		</div><!-- panel panel-default -->
		
		
		
		
		
		
		
	</div><!-- panel-group -->
</div><!-- ROW - menu.php -->
</div><!-- CONTAINER - menu.php -->

	<!-- DIV que vai receber formulario para cadastro de comprador da vaga -->
	<div class="modal fade" id="repasse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Dados do Comprador</h4>
				</div><!-- modal-header -->
				<div class="modal-body">
					<input type="hidden" name="original-repasse_id" id="original-repasse_id" />
					<label class="control-label">ID Comprador:</label>
					<input type="text" class="form-control" name="original-repasse" id="original-repasse_autocomplete" autocomplete="off" placeholder="Digite parte do ID do comprador" required="" /></span>
					<span class="sp-form" id="original-repasse_check"><img src="" alt="" /></span><br />
					<label class="control-label">Valor <span id='sp-tipo-moeda'></span>:</label>
					<input type="text" class="form-control" name="valor" id="valor" maxlength="10" required="" /><br />
					<label class="control-label">Data da venda:</label>
					<input type="date" class="form-control" name="data_venda" id="data_venda" value="<?php echo date('Y-m-d'); ?>" />
					<div class="control-label">
						<label id="lbl-alterou-senha" class="control-label">Alterou a senha? <input type="checkbox" name="alterou_senha" id="alterou_senha" /></label>
						<p class="bg-danger" id="sp-erro-msg-modal" style="display:none;"></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" id="btn-confirma-repasse" class="btn btn-primary">Confirmar Repasse</button>
					</div>
				</div><!-- modal-body -->
			</div><!-- modal-content -->
		</div><!-- modal-dialog -->
	</div><!-- modal fade -->
	

	<!--recebe infos do historico das contas -->
	<div class="modal fade" id="historico" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="mySmallModalLabel">Histórico</h4>
				</div>
				<div id="dialog" class="window" style="padding:10px;"></div>
			</div>
		</div>
	</div>


	<!-- Formulário de fechamento de grupos abertos -->
	<div class="modal fade" id="fecha-grupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel2">Dados do Grupo</h4>
				</div>
				<div class="modal-body form-horizontal">
					<div class="window" id="modal-conteudo-fechamento-grupo"></div>
				</div><!-- modal-body -->
			</div>
		</div>
	</div><!-- modal fade -->



    </div>    
