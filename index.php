<?php
	session_start();
	include 'funcoes.php';
	
	if(isset($_SESSION['ID'])){
		include 'classes/usuarios.class.php';
		$u = new usuarios();
		$prefs = $u->retornaPreferencias($_SESSION['ID']);
		$feed = $prefs->feed;
	} else $feed = 0;

	if ($feed == 1){ //feeds habilitados ou usuario deslogado. Se $feed = 0, não mostra feeds
		if (@file_get_contents('http://adrenaline.uol.com.br/rss/2/25/noticias.xml')){
			$f1 = file_get_contents('http://adrenaline.uol.com.br/rss/2/25/noticias.xml');
			$rss1 = new SimpleXmlElement($f1);
			$cont = 1;
			$feed1 = array();
			foreach($rss1->channel->item as $entrada) {
				array_push($feed1, "
				<a class='list-group-item list-group-item alert-link' href='".$entrada->link."' target='_blank'>
					<h4 class='list-group-item-text small'><b>Adrenaline UOL</b> - ".substr($entrada->pubDate, 0, 24)."</h4>
					<p class='list-group-item-text'>".$entrada->title."</p>
				</a>");
				if($cont > 2) break;
				$cont++;
			}
		} //Adrenaline UOL
		
		if (@file_get_contents('http://rss.baixakijogos.com.br/feed')){
			$f2 = file_get_contents('http://rss.baixakijogos.com.br/feed'); 
			$rss2 = new SimpleXmlElement($f2);
			$cont = 1;
			$feed2 = array();
			foreach($rss2->channel->item as $entrada) {
				array_push($feed2, "
				<a class='list-group-item list-group-item alert-link' href='".$entrada->link."' target='_blank'>
					<h4 class='list-group-item-text small'><b>Baixaki Jogos</b> - ".substr($entrada->pubDate, 0, 24)."</h4>
					<p class='list-group-item-text'>".$entrada->title."</p>
				</a>");
				if($cont > 2) break;
				$cont++;
			}
		}//Baixaki Jogos
		
		if (@file_get_contents('http://www.eurogamer.pt/?format=rss&type=news')){
			$f3 = file_get_contents('http://www.eurogamer.pt/?format=rss&type=news'); 
			$rss3 = new SimpleXmlElement($f3);
			$cont = 1;
			$feed3 = array();
			foreach($rss3->channel->item as $entrada) {
				array_push($feed3, "
				<a class='list-group-item list-group-item alert-link' href='".$entrada->link."' target='_blank'>
					<h4 class='list-group-item-text small'><b>Eurogamer</b> - ".substr($entrada->pubDate, 0, 24)."</h4>
					<p class='list-group-item-text'>".$entrada->title."</p>
				</a>");
				if($cont > 2) break;
				$cont++;
			}
		} //Eurogamer
	}
	
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
				beforeSend: function() {  },
				complete: function(){ },
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
				beforeSend: function() {  },
				complete: function(){ },
				success: function(data){ 
					$readrow.fadeOut(1000,function(){
						$readrow.prop({'src': 'img/lida.png', 'title': 'Aviso lido'}).fadeIn(1000);
						$this_tr.find(".readrow").html("");
					});
				}	
			});
		});	
		
		$('#div-avaliacoes-panel').on('click', '[name="btn-avalia-compra"]', function(){
			$login = $(this).data('login-vendedor');
			$(".modal-body #login-vendedor").text( $login );
			
			$recomendacaoID = $(this).attr("id").split("_")[1];
			$(".modal-body #recomendacao_id").val( $recomendacaoID );
		});
		
		//Cancelamento da avaliação
		$('#div-avaliacoes-panel').on('click', '[name="btn-finaliza-compra"]', function(){
			if(!confirm("A recomendação é opcional, porém ao confirmar essa opção, não haverá mais possibilidade de voltar atrás.\nConfirma essa opção?"))
				return false;
			$recomendacaoID = $(this).attr("id").split("_")[1];
			
			var pars = { recomendacao: $recomendacaoID, funcao: 'cancelaRecomendacao'};
			$.ajax({
				url: 'funcoes_ajax.php',
				type: 'POST',
				contentType: "application/x-www-form-urlencoded;charset=UFT-8",
				data: pars,
				beforeSend: function() {  },
				complete: function(){  },
				success: function(data){ 
					location.reload();
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
		include 'classes/compartilhamentos.class.php';
		include 'classes/jogos.class.php';
		include 'classes/recomendacoes.class.php';
		$a = new avisos();
		$c = new compartilhamentos();
		$j = new jogos();
		$r = new recomendacoes();
		$avisos = $a->getAvisos($_SESSION['ID']);
		$vendas = $c->getVendasAbertasPorUsuario($_SESSION['ID']);
		$compras = $r->getRecomendacoes($_SESSION['ID']);
	?>
		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Quadro de Avisos</div>
				<div class="panel-body">
					<div class="table pre-scrollable">
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
											<td align='justify'>".stripslashes($dados->texto)."</td>
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

			<!-- MINHAS VENDAS -->
			<div class="panel panel-info" id="div-painel-minhas-vendas">
				<div class="panel-heading">Minhas Vendas</div>
				<div class="panel-body">
					<div class="table pre-scrollable">
						<table class="table table-striped">
							<?php
								if ($vendas->num_rows == 0){ $linhas = "<tr><th colspan='4'>Não há vendas ativas no momento.</th></tr>"; }
								else {
							?>
							<thead>
								<tr>
									<th>Jogo(s) da conta</th>
									<th>Vaga</th>
									<th>Preço</th>
									<th><span class='glyphicon glyphicon-usd btn-default btn-xs' title='Altera Valor Venda'></span></th>
									<th><span class='glyphicon glyphicon-trash btn-default btn-xs' title='Exclui Venda'></span></th>
								</tr>
							</thead>
							<tbody>
							<?php
								$linhas = "";
								while($v = $vendas->fetch_object()){
									$id = $v->compartilhamento_id;
									$vaga = $v->vaga;
									$jogos = $j->getJogosGrupo($id); //verifica se há mais de um jogo na conta
									if($jogos->num_rows > 1) { 
										$games = "";
										while($jogo = $jogos->fetch_object()){
											$nome = str_replace("'", " ", stripslashes($jogo->jogo));
											$nomeAbrev = $jogo->nome_abrev;
											$games  .= "- $nome ($nomeAbrev)<br />";
										}
									} else {
										$jogo = $jogos->fetch_object();
										$nome = str_replace("'", " ", stripslashes($jogo->jogo));
										$nomeAbrev = $jogo->nome_abrev;
										$games  = "- $nome ($nomeAbrev)";
									}
									
									$linhas .= "
										<tr>
											<td>$games</td>
											<td>".$c->getNomeVaga($vaga, 1)."</td>
											<td style='width:150px;'><label id='lblValor'>".number_format($v->valor_venda, 2, ',', '.')."</label>
											
												<div class='div-painel-altera-venda' id='div-painel-altera-venda_".$v->id."'>
													<label>novo valor - R$</label><br />
													<input class='input-edita-valor' type='text' id='input-valor-venda_".$v->id."' maxlength='10' />
													<button class='glyphicon glyphicon-ok btn btn-xs btn-primary' title='confirma'></button>
												</div>
											
											</td>
											<td>
												<button class='glyphicon glyphicon-usd btn btn-warning btn-xs' title='Alterar valor de venda' name='btn-altera-valor-venda' id='altera-venda_".$v->id."'></button>
											</td>
											<td>
												<button class='glyphicon glyphicon-trash btn btn-warning btn-xs' title='Excluir Venda' name='btn-exclui-venda' id='exclui-venda_".$v->id."'></button>
											</td>
										</tr>";
									}
								}
								echo $linhas;
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div> <!-- Fim - Minhas Vendas -->

			<!-- MINHAS COMPRAS -->
			<div class="panel panel-warning" id="div-avaliacoes-panel">
				<div class="panel-heading">Minhas Compras</div>
				<div class="panel-body">
					<div class="table pre-scrollable">
						<table class="table table-striped">
							<?php
								if ($compras->num_rows == 0){ $linhas = "<tr><th colspan='4'>Não há compras a serem avaliadas no momento.</th></tr>"; }
								else {
							?>
							<thead>
								<tr>
									<th>Jogo(s) da conta</th>
									<th>Vendedor</th>
									<th>Vaga</th>
									<th><span class='glyphicon glyphicon-thumbs-up btn-default btn-xs' title='Avaliar compra'></span></th>
									<th><span class='glyphicon glyphicon-remove-circle btn-default btn-xs' title='Finaliza sem avaliar'></span></th>
								</tr>
							</thead>
							<tbody>
							<?php
								$linhas = "";
								while($co = $compras->fetch_object()){
									$id = $co->compartilhamento_id;
									$vaga = $co->vaga;
									$jogos = $j->getJogosGrupo($id); //verifica se há mais de um jogo na conta
									if($jogos->num_rows > 1) { 
										$games = "";
										while($jogo = $jogos->fetch_object()){
											$nome = str_replace("'", " ", stripslashes($jogo->jogo));
											$nomeAbrev = $jogo->nome_abrev;
											$games  .= "- $nome ($nomeAbrev)<br />";
										}
									} else {
										$jogo = $jogos->fetch_object();
										$nome = str_replace("'", " ", stripslashes($jogo->jogo));
										$nomeAbrev = $jogo->nome_abrev;
										$games  = "- $nome ($nomeAbrev)";
									}
									
									$linhas .= "
										<tr id='tr-".$co->recomendacaoID."'>
											<td>$games</td>
											<td title='Nome: ".stripslashes($co->vendedorNome)."'>".stripslashes($co->vendedorLogin)."</td>
											<td>".$c->getNomeVaga($vaga, 1)."</td>
											<td>
												<button class='glyphicon glyphicon-thumbs-up btn btn-warning btn-xs' title='Avaliar compra' name='btn-avalia-compra' id='avalia-compra_".$co->recomendacaoID."' data-toggle='modal' 
													data-target='#avaliacao' data-login-vendedor='".stripslashes($co->vendedorLogin)."'></button>
											</td>
											<td>
												<button class='glyphicon glyphicon-remove-circle btn btn-warning btn-xs' title='Finaliza sem avaliar' name='btn-finaliza-compra' id='finaliza-compra_".$co->recomendacaoID."'></button>
											</td>
										</tr>";
									}
								}
								echo $linhas;
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div> <!-- Fim - Minhas Compras -->	
			
		</div>
	<?php
	}
	?>
	
	<!-- Fedd - RSS-->
	<div class="list-group alert">
		<div class='list-group-item list-group-item-success'>Últimas notícias</div>
		
	 	<!--
		<a class='list-group-item list-group-item alert-link' href='#' target='_blank'>
			<h4 class='list-group-item-text small'><b>Site</b> - Data</h4>
			<p class='list-group-item-text'>Título da notícia</p>
		</a>
		<a class='list-group-item list-group-item alert-link' href='#' target='_blank'>
			<h4 class='list-group-item-text small'><b>Site</b> - Data</h4>
			<p class='list-group-item-text'>Título da notícia</p>
		</a>
		<a class='list-group-item list-group-item alert-link' href='#' target='_blank'>
			<h4 class='list-group-item-text small'><b>Site</b> - Data</h4>
			<p class='list-group-item-text'>Título da notícia</p>
		</a>
		-->
		<?php
			
			if($feed == 0){ //feeds desabilitados
				echo "
					<div class='list-group-item list-group-item-text'>Feed/RSS Desabilitado. Para habilitar, vá em \"Meu Perfil -> Preferências\". É preciso estar logado.</div>
				";
			} else {
				for ($i=0; $i<3; $i++){
					if(isset($feed1[$i]) && isset($f1)) echo $feed1[$i];
					if(isset($feed2[$i]) && isset($f2)) echo $feed2[$i];
					if(isset($feed3[$i]) && isset($f3)) echo $feed3[$i];	
				}
			}
			
		?>
	</div>
	

	<!-- formulário de avaliação da compra -->
	<div class="modal fade" id="avaliacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Avalie sua compra</h4>
				</div><!-- modal-header -->
				<div class="modal-body">
					<input type="hidden" name="recomendacao_id" id="recomendacao_id" />
					<div class="form-group">
						<label class="control-label">Vendedor:</label>
						<label class="control-label" name="login-vendedor" id="login-vendedor" /></label>
					</div>
					
					<div class="form-group">
						<label>Comentário (relate sua experiência nessa transação):</label>
						<textarea class="form-control" maxlength="250" id="txtTexto" autofocus></textarea>
						<small>Máximo de 250 caracteres</small>
						<p class="bg-danger" id="sp-erro-msg-modal" style="display:none;"></p>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" id="btn-confirma-avaliacao" class="btn btn-primary">Avaliar</button>
					</div>
				</div><!-- modal-body -->
			</div><!-- modal-content -->
		</div><!-- modal-dialog -->
	</div><!-- modal fade -->


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
