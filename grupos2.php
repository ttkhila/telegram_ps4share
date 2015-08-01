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
<div id="div-cadastro-jogos">
		<input type="hidden" id="selfID" name="selfID" value="<?php echo $selfID; ?>" /> 
		<img src="img/add.png" width="20" height="20" id="imgNovo" name="abre" style="cursor:pointer;" />&nbsp;&nbsp;<strong>Novo Grupo</strong>
		<!-- Novo Grupo - Início -->
		<div id="div-novo-grupo" style="display:none;" class="div-grupos">
			<div class="sp-erro-msg" style="display:none;"></div><!-- mensagem de erro -->
			<span class="sp-form">Nome:</span>
			<input type="text" name="nome" id="nome" maxlength="50" required="" style="width:450px;" />&nbsp;&nbsp;
			<a href="#" title="Digite um nome para o grupo que identifique o(s) jogo(s) contido(s) nele ou seus integrantes." class="masterTooltip"><img src="img/help.png" width="16" height="16" /></a><br /><!--TOOLTIP -->
			
			<span class="sp-form">E-mail:</span>
			<input type="email" name="email" id="email" style="width:450px;" />&nbsp;&nbsp;
			<a href="#" title="E-mail da conta de jogo. N&atilde;o &eacute; obrigat&oacute;rio informar na criação do grupo, a n&atilde;o ser que seja um grupo j&aacute; fechado." class="masterTooltip">
				<img src="img/help.png" width="16" height="16" /></a><br /><!--TOOLTIP -->
			
			<span class="sp-form">Moeda de compra:</span>
			<select id="moedas" name="moedas">
				<?php
				while($m = $moedas->fetch_object()){
					if($m->pais == "BRL") echo "<option value='".$m->id."' selected='selected'>".stripslashes(utf8_decode($m->nome))." (".$m->pais.")</option>";
					else echo "<option value='".$m->id."'>".stripslashes(utf8_decode($m->nome))." (".$m->pais.")</option>";
				}
				?>
			</select><br /><br />
			
			<!-- JOGOS-->
			<span class="sp-form-titulo" style="min-width:55px">Jogo(s)</span>
			<a href="#" title="&Eacute; necess&aacute;rio o preenchimento de pelo menos um jogo." class="masterTooltip">
				<img src="img/help.png" width="16" height="16" /></a><br /><!--TOOLTIP -->
			<span class="sp-form">Jogo 1:</span>
			<input type="hidden" name="jogo_id[]" id="jogo1_id" required="" />
			<input type="text" name="jogo[]" id="jogo1_autocomplete" autocomplete="off" style="width:250px;" placeholder="Digite parte do nome do jogo" required="" />
			<span class="sp-form" id="jogo1_check"><img src="" /></span>
			<button id="btn-add-jogo">+ Jogo</button>&nbsp;&nbsp;
			<a href="#" name="lista_jogos">Lista dos jogos</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<div class='div-suspenso-jogos' style="display:none"><!-- Lista de jogos -->
				<span id="sp-fecha-lista" style="float:right;">Fechar<a href="#" name="fecha_lista_jogos">[X]</a></span><br />
				<?php
					while($dados = $jogos->fetch_object()){
						echo stripslashes(utf8_decode($dados->nome))." (".stripslashes(utf8_decode($dados->plataforma)).")<br />";
					}
				?>
			</div>
			<!--campos dinamicos -->
			<div id="div-jogos-extras"></div><br /><br />

			<!-- VAGAS-->
			<span class="sp-form-titulo" style="min-width:70px">Usu&aacute;rios</span>
			<a href="#" title="Na cria&ccedil;&atilde;o do grupo &eacute; obrigat&oacute;rio o preenchimento de apenas seu pr&oacute;prio ID numa das vagas." class="masterTooltip">
				<img src="img/help.png" width="16" height="16" /></a><br /><!--TOOLTIP -->
			<span class="sp-form">Original 1 / ID:</span>
			<input type="hidden" name="original1_id" id="original1_id" />
			<input type="text" name="original1" id="original1_autocomplete" autocomplete="off" style="width:250px;" placeholder="Digite parte do ID do usu&aacute;rio" />
			<span class="sp-form-direita">Valor:</span>
			<input type="text" class="monetario" name="valor" id="valor1" maxlength="10" />
			<button class="btn-limpar" id="1">Limpar</button><br />
			
			<span class="sp-form">Original 2 / ID:</span>
			<input type="hidden" name="original2_id" id="original2_id" />
			<input type="text" name="original2" id="original2_autocomplete" autocomplete="off" style="width:250px;" placeholder="Digite parte do ID do usu&aacute;rio" />
			<span class="sp-form-direita">Valor:</span>
			<input type="text" class="monetario" name="valor" id="valor2" maxlength="10" />
			<button class="btn-limpar" id="2">Limpar</button><br />
			
			<span class="sp-form">Fantasma / ID:</span>
			<input type="hidden" name="original3_id" id="original3_id" />
			<input type="text" name="original3" id="original3_autocomplete" autocomplete="off" style="width:250px;" placeholder="Digite parte do ID do usu&aacute;rio" />
			<span class="sp-form-direita">Valor:</span>
			<input type="text" class="monetario" name="valor" id="valor3" maxlength="10" />
			<button class="btn-limpar" id="3">Limpar</button><br /><br />
			
			<input type="checkbox" id="fechado" name="fechado" /><span class="sp-form">&nbsp;&nbsp;Grupo j&aacute; fechado?</span>&nbsp;&nbsp;
			<a href="#" title="Se esse campo for marcado, ser&aacute; obrigat&oacute;rio o preenchimento de pelo menos uma vaga, os valores das vagas preenchidas, assim como o e-mail da conta criada." class="masterTooltip"><img src="img/help.png" width="16" height="16" /></a><br /><br /><!--TOOLTIP -->
			<button id="btn-grupo-novo" class="btn-padrao">Criar Grupo</button>
		</div><!-- Novo Grupo - Fim -->
		<br /><br />
		
		
		<!-- Meus Grupos: Início -->
		
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
		<input type="hidden" id="hidFlag" value="0" />
		
		<!-- Janelas Modais -->
		<div id="modal fade">
			<div id="dialog" class="window"></div><!-- DIV que vai receber dados do histórico dos grupos selecionados -->
			
			<div id="repasse" class="window"><!-- DIV que vai receber formulario para cadastro de comprador da vaga -->
				<center><span class="sp-titulo-modal">Dados do Comprador</span></center><br />
				<input type="hidden" name="original-repasse_id" id="original-repasse_id" />
				<span class="sp-campos-modal">ID Comprador:</span>
				<span><input type="text" name="original-repasse" id="original-repasse_autocomplete" autocomplete="off" style="width:250px;" placeholder="Digite parte do ID do comprador" required="" /></span>
				<span class="sp-form" id="original-repasse_check"><img src="" alt="" /></span><br />
				<span class="sp-campos-modal">Valor (em reais):</span><span><input type="text" class="monetario" name="valor" id="valor" maxlength="10" required="" /></span><br />
				<span class="sp-campos-modal">Data da venda:</span><span><input type="date" name="data_venda" id="data_venda" value="<?php echo date('Y-m-d'); ?>" /></span><br />
				<span class="sp-campos-modal">Alterou a senha?</span><span><input type="checkbox" name="alterou_senha" id="alterou_senha" /></span><br /><br />
				<span class="sp-campos-modal"></span><button id="btn-confirma-repasse" class="btn-padrao">Ok</button>
				<span class='sp-erro-msg-modal'></span>
			</div>
			
			<div id="mask"></div>
		</div>
		<!-- Meus Grupos: Fim-->
		
	</div>