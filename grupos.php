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
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script>
	$(function(){ 
		$('#imgNovo').click(function(e){ 
			if($(this).attr('name') == 'abre'){
				$('#div-novo-grupo').slideDown(); 
				$(this).prop({'src':"img/close.png",'width':'18','height':'18'});
				$(this).prop('name', 'fecha');
			} else {
				$('#div-novo-grupo').slideUp(); 
				$(this).prop({'src':"img/add.png",'width':'20','height':'20'});
				$(this).prop('name', 'abre');
			}
		});
		
		$("[name='lista_jogos']").mousemove(function(e){
			e.preventDefault(); //previne o evento 'normal'
			$(".div-suspenso-jogos").slideDown('slow');
		}).click(function(e){ e.preventDefault(); });
		
		$("[name='fecha_lista_jogos']").click(function(e){ $(this).parent().parent().fadeOut(); });

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
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                      Novo Grupo
                    </a>
                  </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                  <div class="panel-body">
                        <div class="form-group">
                            <h4>Digite um nome para o grupo que identifique o(s) jogo(s) contido(s) nele ou seus integrantes.</h4>
                            <label for="exampleInputnome">Nome</label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome"> 
                            <label for="exampleInputEmail1">Email</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="E-mail">         			
                			
                            <label for="exampleInputEmail1">Moeda de Compra</label>
                			<select class="form-control" id="moedas" name="moedas">                            
                				<?php
                				while($m = $moedas->fetch_object()){
                					if($m->pais == "BRL") echo "<option value='".$m->id."' selected='selected'>".stripslashes(utf8_decode($m->nome))." (".$m->pais.")</option>";
                					else echo "<option value='".$m->id."'>".stripslashes(utf8_decode($m->nome))." (".$m->pais.")</option>";
                				}
                				?>
                			</select>
                			
                			<!-- JOGOS-->
                            <h3>Jogos</h3>
                            <label for="">Jogo1</label>
                            <input type="hidden" class="form-control" name="jogo_id[]" id="jogo1_id" required>                			
                            <input type="text" class="form-control" name="jogo[]" id="jogo1_autocomplete" placeholder="Digite parte do nome do jogo 1" required/>
                			
                			<span class="sp-form" id="jogo1_check"><img src="" /></span>
                            <!--campos dinamicos -->
                			<div id="div-jogos-extras"></div><br /><br />
                            <button class="btn btn-primary" id="btn-add-jogo" type="button">+ Jogo</button>
                           </div> 
                			
                
                			<!-- VAGAS-->
                            <form class="form-inline">
                                <h3>Vagas</h3>
                                <form class="form-group">                        			
                        			<label class="exampleInputEmail1">Original 1 / ID:</label>
                        			<input type="hidden" name="original1_id" id="original1_id" />
                        			<input type="text" name="original1" class="form-control" id="original1_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
                        			<label class="exampleInputEmail1">Valor:</label>
                        			<input type="text" class="form-control" name="valor" id="valor1" maxlength="10" />
                        			<button class="btn btn-danger" id="1">Limpar</button>
                                </form>
                            </form>
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
                            <form class="form-inline">
                                <form class="form-group">	
                        			<label class="exampleInputEmail1">Fantasma / ID:</label>
                        			<input type="hidden" name="original3_id" id="original3_id" />
                        			<input type="text" name="original3" class="form-control" id="original3_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
                        			<label class="sp-form-direita">Valor:</label>
                        			<input type="text" class="form-control" name="valor" id="valor3" maxlength="10" />
                        			<button class="btn btn-danger" id="3">Limpar</button><br /><br />
                        			<div class="checkbox">
                                        <label>
                        			     <input type="checkbox" id="fechado" name="fechado" /><span class="sp-form">&nbsp;&nbsp;Grupo j&aacute; fechado?</span>&nbsp;&nbsp;
                                        </label> 
                        			</div><br /><p></p>
                        			<button id="btn-grupo-novo" class="btn btn-success">Criar Grupo</button>
                                </form>
                           </form>
                        		</div><!-- fim novo grupo -->
                            </form>
                  </div>
                </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingTwo">
                  <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                      	Meus Grupos
                    </a>
                  </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
                  <div class="panel-body">
                    <div id="div-listagem-grupos" class="container-grupos">
                			<h3>Meus Grupos</h3>
                    		<?php
                    			if ($dados1->num_rows == 0){
                    				echo "<span>Não há nenhum grupo ativo para este usuário!<br />
                    				Clique no ícone <img src='img/add.png' width='20' height='20'  /> acima para criar um novo grupo.
                    				</span>";
                    			} else {
                    				while($d = $dados1->fetch_object()){
                    					if($d->fechado == 1) $fechado = "Grupo Fechado"; else $fechado = "Grupo aberto";
                    					echo "<div id ='grupo_".$d->id."' class='casulo-grupo'>";
                    					echo "<div name='div-casulo-grupo' id ='grupo-titulo_".$d->id."' class='casulo-grupo-titulo'>";
                    					echo "<span><img src='img/plus.png' width='16' height='16' id='_1' /></span><span>".stripslashes(utf8_decode($d->nome))."</span>";
                    					echo "<span>&nbsp;&nbsp;($fechado)</span>";
                    					echo "</div>";
                    					echo "<div id ='grupo-conteudo_".$d->id."' class='casulo-grupo-conteudo' style='display:none;'></div>";
                    					echo "<hr />";
                    					echo "</div>";
                    				}
                    			}
                    		?>
                    		<span><strong>Legenda (grupos fechados):</strong></span><br />
                    		<span><img src='img/cash.gif' />&nbsp;&nbsp;Informar vaga repassada</span><br />
                    		<span><img src='img/checkout.png' />&nbsp;&nbsp;Colocar vaga a venda</span>
                		</div>
                    </div>
                    <input type="hidden" id="hidFlag" value="0" />
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="repasse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><!-- DIV que vai receber formulario para cadastro de comprador da vaga -->
            				<div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Dados do Comprador</h4>
                            </div>
                            <div class="modal-body">
                            
            				<input type="hidden" name="original-repasse_id" id="original-repasse_id" />
            				<label class="sp-campos-modal">ID Comprador:</label>
            				<input type="text" class="form-control" name="original-repasse" id="original-repasse_autocomplete" autocomplete="off" placeholder="Digite parte do ID do comprador" required="" /></span>
            				<span class="sp-form" id="original-repasse_check"><img src="" alt="" /></span><br />
            				<label class="sp-campos-modal">Valor (em reais):</label>
                            <input type="text" class="form-control" name="valor" id="valor" maxlength="10" required="" />
            				<label class="sp-campos-modal">Data da venda:</label>
                            <input type="date" class="form-control" name="data_venda" id="data_venda" value="<?php echo date('Y-m-d'); ?>" />
            				
                             <div class="checkbox">
                                <label>   
                                    Alterou a senha? <input type="checkbox" name="alterou_senha" id="alterou_senha" />
                                </label>
            				
            				<span class='sp-erro-msg-modal'></span>
            			</div>
                        <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="btn-confirma-repasse" class="btn btn-primary">Confirmar Repasse</button>
                  </div>
                </div>
              </div>
            </div>
            

