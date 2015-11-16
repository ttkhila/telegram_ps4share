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
	include_once 'classes/usuarios.class.php';
	//include_once 'classes/grupos_acesso.class.php';
	//include_once 'classes/compartilhamentos.class.php';
	include 'funcoes.php';
	
	$u = new usuarios();
	$usus = $u->retornaTudoArray('id');
	$nomes = "";
	foreach($usus as $valor){
		$nomes .= $valor."||";
	}
	
	//print_r($usus); exit;
	
	$indPend = $u->getIndicadosPendentes();
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script type="text/javascript" src="js/lib/jquery.mask.min.js"/></script>
<script>
	$(function(){ 
	
		//Té Oliveira||Estevão Rocha||Flávio Gutierres||Glauber Mattar||João Ricardo Zorzi||Rafael Avolio||Railanderson Rodrigues||Ricardo Matos||Rodolfo Antunes Dias Cintra||Roni Domene||Willian Freitas||Fernando Salvador||Jhonatan Anschau||Andre Almeida||Bruno Filipe silva||Bruno Filizola||Bruno Michel||carlos daniel||Carlos Eduardo Costa||Cesar severo||Daniel Camargo||Daniel Marques Belém||Diogo Camargo||Douglas Braga Rocha||Edson Freitas||Elissandro Feliciano||Emerson Russo||Fábio Aparecido||Fabricio Muniz Palmas||Francisco Cagnoni||George Sausen de Almeida||Geovane Perez Nilce||Glauber Brigatti Castro||Gomides Manduca||Guilherme Rolemberg||guilherme tripode Colognesi||Gustavo Cavalcante||Henrique Medeiros||Igor GonÒ§alves||Igor Juliani||Jacques Charles Rangel||Jailson Santos||João Claudio Monteiro||Joao Naves||José Edson Barbosa||julio César simei||Kaue Franco||Kleverson Witczak||Ladislau Melo||Leandro Baroni Claudino||Leonardo Mariano||Leandro Carvalho||Lucas Santana||Luciano José Gras||Lúcio Vitorio||Marcelo Ávila||Marcelo Edson Alves||Marcelo Ulrichsen||Marcos Santana Junior||Maurí­cio Filho||Mauricio Machado||Max Ramires||Nicholas Correa||Odeir Rolim||Oscar mendes junior||Otavio pereira gouveia||paulo roberto||Rafael Costa lessi||Rafael Nascimento||Rafael Prado Seibel||Roberto Oliveira||Rodrigo Almeida||Rodrigo Filippi||Rogério Júnior||Simão Pedro Olivencia||Tharcisio Citrângulo Tortelli Jr||Thiago bento||Thiago Carloni||Thiago Maltempe||Thiago Santos||Tiago RIbeiro de Sousa Cerqueira||Victor Vinicius da Silva alves||Vinicius Andre Delagustinhi||Vitor de Carvalho||welton Eduardo||wesley de souza prado||William Maggi||Yuri Silveira de Almeida Freitas||Felipe Neves||alexandre freitas||Bruno Augusto||Bruno Sanchez||Cleverton Pereira Pires||Cleverton Pereira Pires||Daniel Pires de Lima||Danillo Sampaio Nogueira||Diogo aguiar||Erik Trevisan||Felipe Rocha Moraes||Igor Camerieri||Jean Nunes||João Gabriel Andrade||Juliano Paulino||Leonardo Fabricio A. Manzotti||Marcelo Ferrari Filho||Marlos Maciel||rodrigo Lopes de moura||Victor Estima||Wanderson Buzzonaro||josé carneiro||Juliano Gonçalves||Lucas Honório pereira||Rodrigo Moreira||Thiago Bento||Tadeu Gois||Alesandro de moraes||bruno chiara||Luiz Maia||César Vieira||Guilhermee botelho||Renan calixto||José Carneiro Araúo Júnior||mauricio wanderley||rafael nascimento||Jair zamboni||Marcelo Teixeira||Jhonathan Martins Jardim||Thales César||Fernando Mitidieri||vinicius bolgar||maycon costa||Rubens Márcio||Afonso Cândido Silva||Gustavo Silva||Vinicius Machado||Diogo Luiz de Almeida||Rodrigo Oliveira||Bruno Aguiar||Cleber Lourenço||Alesandro de moraes||Alexandre Motolo||Rafael Munhoz||Ademir Gonçalves junior||Vitor Nishikiori||Eliézio Rodrigo dos Santos||Eduardo Guimarães||Rafael Dutra||Gilvani Flores dos Santos||João Neto||Wellington Vieira||william||Darlã Cerqueira||Ronaldo Passberg||Marllon Silva||Maury Vieira Falcão||Gabriel Antoniança Megale||Ulysses Ferreira||Thiago Favaro||Vinicius Maggi||Jonatas Maffei||Raphael magno dos santos||Rafael Lessi||alesandro de moraes||Roberto Medeiros Caetano||Diogo Lopes||Leonardo Alves de Oliveira||Luciano José gras||Eduardo Guimarães||Bruno Araujo||Tadeu gois||Felipe Neves||Tiago Santos||Bruno silva||Nicholas Correa||Fellipe Amaral||Daniel Iglesias||Carlos antonio||Tiago pereira||André Vargas||Anderson||Matheus Luiz Carneiro||Thiago Fausto||Leoncio Marques||Talles Raif||Fabricio dos Santos||Leandro oliveira||Nelson Gabriel||Cleverton (Cleverthonn)||Rodrigo Santos Inacio||André Luiz||Allan Figueiredo||Helamã Rédua||Wellington Bune||Jhonny Dutra Garcia||Raphael rodrigues||Raphael Mattar||Rodrigo Leite||Jean||Victor da Costa Aguiar||Arthur Dantas Silvestre ||José Carneiro||Leandro Jacovani||Richard Dias||Gustavo Henrique ||Luand Mendes||Nicollas Rolemberg Silva||Kleiton Rubio||Jefferson Ricardo ||Gustavo Teixeira Larsen||
		
		$("form[0]").submit(function(e){
			e.preventDefault(); //previne o evento 'normal'

			$(this)[0].submit();
		});
		
		$("#btn-id-email").click(function(e){
			e.preventDefault(); //previne o evento 'normal'
				
			var login = $("#txtLogin").val();
			var initLogin = login.substr(0, 3).toLowerCase();
			
			var tel = $("#txtTelefone").val();
			var finTel = tel.substr(-3, 3);
			
			$("#txtIdEmail").val(initLogin+""+finTel);
			//alert(initLogin+""+finTel);
		});
		
		
		function utf8_decode (str_data) {
		  var tmp_arr = [],
		    i = 0,
		    ac = 0,
		    c1 = 0,
		    c2 = 0,
		    c3 = 0;

		  str_data += '';

		  while (i < str_data.length) {
		    c1 = str_data.charCodeAt(i);
		    if (c1 < 128) {
		      tmp_arr[ac++] = String.fromCharCode(c1);
		      i++;
		    } else if (c1 > 191 && c1 < 224) {
		      c2 = str_data.charCodeAt(i + 1);
		      tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
		      i += 2;
		    } else {
		      c2 = str_data.charCodeAt(i + 1);
		      c3 = str_data.charCodeAt(i + 2);
		      tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
		      i += 3;
		    }
		  }

		  return tmp_arr.join('');
		}
		
		
		$("#btn-converte-iso").click(function(){
			var nomesB = $("#arrNomesBrutos").val();
			var nomesN = utf8_decode(nomesB);
			$("#arrNomesConv").val(nomesN);
		});
		
		$("form[1]").submit(function(e){
			e.preventDefault(); //previne o evento 'normal'
			
			$(this).submit();
		});
		
		
	});	
</script>
</head>
<body>
	<?php $menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
	<!-- Conteúdo Principal: Início -->
	<a href="#" id="foco"></a>
	 <h1 class="page-header">Inclui usuários da planilha</h1>
	<form id="form-cadastro-planilha" role="form" method="post" action="processa_inoffuser.php">
		<div id="form-group">
			<label>Nome</label>
			<input class="form-control" type="text" id="txtNome" name="txtNome" required />
		</div>	
		
		<div id="form-group">
			<label>ID</label>
			<input class="form-control" type="text" id="txtLogin" name="txtLogin" required />
		</div>
		
		<div id="form-group">
			<label>Celular</label>
			<input class="form-control" type="tel" id="txtTelefone" name="txtTelefone" required />
			<script type="text/javascript">$("#txtTelefone").mask("(00) 0000-00009");</script>
		</div>		
		
		<div id="form-group">
			<label>E-mail</label>
			<input class="form-control" type="email" id="txtEmail" name="txtEmail" required />
		</div>	
		
		<div id="form-group">                      			
			<label>Indicado Por:</label>
			<input type="hidden" name="original1_id" id="original1_id" />
			<input type="text" name="original1" class="form-control" id="original1_autocomplete" autocomplete="off" placeholder="Digite parte do ID do usu&aacute;rio" />
		</div>
		
		<div id="form-group">
			<label>ID E-mail</label>
			<input class="form-control" type="text" id="txtIdEmail" name="txtIdEmail" required />
			<button type="button" class="btn btn-success" id="btn-id-email">Preencher</button>
		</div><br />
		<button type="submit" class="btn btn-primary">Enviar</button>
	</form>
	
	<br /><br /><br />
	<h3>Converte UTF-8 para ISO</h3>
	<form id="form-converte" role="form" method="post" action="processa_inoffuser.php">
		<div id="form-group">
			<label>Nomes Brutos</label>
			<input class="form-control" type="text" id="arrNomesBrutos" name="arrNomesBrutos" value="<?php echo $nomes; ?>" />
		</div>
		<div id="form-group">
			<label>Nomes Convertidos</label>
			<input class="form-control" type="text" id="arrNomesConv" name="arrNomesConv" value="<?php echo $nomes; ?>" />
		</div>
		<button type="button" class="btn btn-success" id="btn-converte-iso">Converter</button>
		<button type="submit" class="btn btn-primary">Salvar</button>
	</form>
	
	<!-- Conteúdo Principal: Fim -->
	<?php $rodape = file_get_contents('rodape.html'); echo $rodape; //insere rodapé ?>
</html>
