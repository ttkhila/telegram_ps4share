$(function(){ 
	var QTD_JOGOS_CADASTRO = 1;
	var VAGA_REPASSE;
	var GRUPO_REPASSE;
	var JOGO_AUTOCOMPLETE;
	var FLAG_HISTORICO = 0;
	
	function IsEmail(email) {
	  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  return regex.test(email);
	}
	
	//Altera TODAS as ocorr�ncias de um determinado parametro em uma string
	function replaceAll(string, token, newtoken) {
        while (string.indexOf(token) != -1) {
            string = string.replace(token, newtoken);
        }
        return string;
    }
    
//********************** MODAIS ****************************************
	function abreModal(id, data){ $(id).html(data); }	
//***********************************************************************
//Insere e retira spinner do elemento para efeitos "loading"...
function doAnimated(botao){
	botao.html("<span class='glyphicon glyphicon-refresh glyphicon-refresh-animate'></span> Loading...");
}
function resetaHtml(orig, clone){
	orig.replaceWith(clone.clone());
	orig.replaceWith(clone);
}
//***********************************************************************
//LOGIN
$("#frmLogin").submit(function(e){
	e.preventDefault(); //previne o evento 'normal'
	var botao = $(this).find("button");
	var divClone = botao.clone(); 

	var $form = $(this).serialize();
	$form = decodeURI(replaceAll($form, '+', ' ')); //retira alguns caracteres especiais   
	$form = $form.split('&'); //transforma em array, separado pelo "&"
	var pars = { dados: $form, funcao: 'realizaLogin'};
	
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { doAnimated(botao); botao.attr('disabled', 'disabled'); },
		complete: function(){ },
		success: function(data){ 
			console.log(data); 
			if(data[0] == 0){ //error
				$("#sp-erro-msg")
					.fadeIn()
					.html(data[1]+"<span class='badge'>x</span>");
				resetaHtml(botao, divClone);
				botao.removeAttr('disabled');
			} else if (data[0] == 2) { //primeiro acesso
				$(location).attr('href', 'primeiro_acesso.php?id='+data[1]);	
			} else {
				$(location).attr('href', 'index.php');
			} 			
		}
	});
});
//********************************************************************************
//LINK VAZIO
$("#collapseTwo").on("click", "[name='link_vazio']", function(e){
	e.preventDefault(); //previne o evento 'normal'
});
//********************************************************************************
$("#deslogar").click(function(e){
	e.preventDefault(); //previne o evento 'normal'
	
	var pars = { funcao: 'realizaLogout'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		//dataType: "json",
		//contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { $("img.pull-right").fadeIn('fast'); },
		complete: function(){ $("img.pull-right").fadeOut('fast'); },
		success: function(){ 
			$(location).attr('href', 'index.php');	
		}
	});
});
//********************************************************************************	
//TOOLTIP
$('body').tooltip({
	selector: '[data-toggle="tooltip"]',
	container: 'body'
}); 
//********************************************************************************	
//POPOVER
$('body').popover({
	selector: '[data-toggle="popover"]',
	html: true,
	container: 'body',
	content: function() {
		//alert($(this).attr("data-id"));
		$("[data-toggle='popover']").not(this).popover('hide');
		return $('#show-popover_'+$(this).attr("data-id")).html();
	}
}); 
//********************************************************************************
//AUTOCOMPLETE 
//Original 1
$('#original1_autocomplete').simpleAutoComplete('autocomplete_ajax.php',{
autoCompleteClassName: 'autocomplete',
	selectedClassName: 'sel',
	attrCallBack: 'rel',
	identifier: 'original1'
},original1Callback);

function original1Callback( par ){ 
	$('#original1_autocomplete').val(par[1]);
	$('#original1_id').val(par[0]);
}
$('#original1_autocomplete').keyup(function(){ //limpar ID quando campo estiver vazio
	if($('#original1_autocomplete').val() == "" || $('#original1_autocomplete').val().length <= 0)
		$('#original1_id').val("");
});

//Original 2
$('#original2_autocomplete').simpleAutoComplete('autocomplete_ajax.php',{
autoCompleteClassName: 'autocomplete',
	selectedClassName: 'sel',
	attrCallBack: 'rel',
	identifier: 'original2'
},original2Callback);

function original2Callback( par ){ 
	$('#original2_autocomplete').val(par[1]);
	$('#original2_id').val(par[0]);
}
$('#original2_autocomplete').keyup(function(){ //limpar ID quando campo estiver vazio
	if($('#original2_autocomplete').val() == "" || $('#original2_autocomplete').val().length <= 0)
		$('#original2_id').val("");
});

//Fantasma - "Original 3"
$('#original3_autocomplete').simpleAutoComplete('autocomplete_ajax.php',{
autoCompleteClassName: 'autocomplete',
	selectedClassName: 'sel',
	attrCallBack: 'rel',
	identifier: 'original3'
},original3Callback);
function original3Callback( par ){ 
	$('#original3_autocomplete').val(par[1]);
	$('#original3_id').val(par[0]);
}
$('#original3_autocomplete').keyup(function(){ //limpar ID quando campo estiver vazio
	if($('#original3_autocomplete').val() == "" || $('#original3_autocomplete').val().length <= 0)
		$('#original3_id').val("");
});

//Repasse de conta
$("#repasse").on("keydown","#original-repasse_autocomplete",function(e) {
if (!$(this).data("simpleAutoComplete")) { 
		var tecla = e.which;
		if(tecla != 9 && tecla != 13){
			$("#original-repasse_id").val("");
			$("#original-repasse_check img").prop({'src':"img/uncheck.png"});
		}
        $(this).simpleAutoComplete(
            'autocomplete_ajax.php',{
				autoCompleteClassName: 'autocomplete',
				selectedClassName: 'sel',
				attrCallBack: 'rel',
				identifier: 'original-repasse'
		},originalRepasseCallback);
    }
});
function originalRepasseCallback( par ){ 
	$('#original-repasse_autocomplete').val(par[1]);
	$('#original-repasse_id').val(par[0]);
	$('#original-repasse_check img').prop({'src':"img/check.png"});
}

// Jogos - Alteração Cadastro
$('#jogo-nome-altera_autocomplete').simpleAutoComplete('autocomplete_jogos_ajax.php',{
autoCompleteClassName: 'autocomplete',
	selectedClassName: 'sel',
	attrCallBack: 'rel',
	identifier: 'jogo-altera'
},jogo_nome_altera);
function jogo_nome_altera( par ){
	/* par[0] = ID
	 * par[1] = NOME
	 * par[2] = plataforma_id
	 * par[3] = Ativo/Inativo (1/0)
	 */
	var char1 = parseInt(par[1].indexOf("("));
	var nome = par[1].substring(0, char1-1); //nome sem a abrev da plataforma
	$('#jogo-nome-altera_autocomplete').val(par[1]);
	$('#nome-jogo-altera').val(nome);
	$('#jogo-nome-altera_id').val(par[0]);
	$("#plataforma-altera").find("option[value='"+par[2]+"']").prop("selected", "selected");
	$("#frm-altera-jogos").show();
	if(par[3] == 1) $html = "Jogo Ativo -> <a href='#' name='a-ativar' rel='0'>Desativar Jogo</a>";
	else $html = "Jogo Desativado -> <a href='#' name='a-ativar' rel='1'>Ativar Jogo</a>";
	
	$("#sp-ativo-altera").html($html);
}

//jogos
$("#collapseOne").on("keydown","[name='jogo[]']",function(e) {
    if (!$(this).data("simpleAutoComplete")) { 
		JOGO_AUTOCOMPLETE = $(this).attr('id').split("_")[0];
		var tecla = e.which;
		if(tecla != 9 && tecla != 13){
			$("#"+JOGO_AUTOCOMPLETE+"_id").val("");
			$('#'+JOGO_AUTOCOMPLETE+'_check img').prop({'src':"img/uncheck.png"});
		}
        $(this).simpleAutoComplete(
            'autocomplete_jogos_ajax.php',{
				autoCompleteClassName: 'autocomplete',
				selectedClassName: 'sel',
				attrCallBack: 'rel',
				identifier: 'jogo'
		},jogoCallback);
    }
});
function jogoCallback( par ){ 
	$('#'+JOGO_AUTOCOMPLETE+'_autocomplete').val(par[1]);
	$('#'+JOGO_AUTOCOMPLETE+'_id').val(par[0]);
	$('#'+JOGO_AUTOCOMPLETE+'_check img').prop({'src':"img/check.png"});
}

$("#collapseOne").on("blur","[name='jogo[]']",function(e) {
	//apaga o campo de ID se o nome do jogo foi apagado pelo usuário
	var dado = $(this).attr('id').split("_")[0];
	if($(this).val() == ""){
		$("#"+dado+"_id").val("");
		$('#'+dado+'_check img').prop({'src':"img/uncheck.png"});
	}
});
//********************************************************************************
$("#btn-add-jogo").click(function(e){
	e.preventDefault(); //previne o evento 'normal'
	QTD_JOGOS_CADASTRO++;
	var $html = "<div class='form-group col-md-12'>"
		$html += "<label class='control-label col-sm-2'>Jogo "+QTD_JOGOS_CADASTRO+":</label>";
		$html += "<div class='col-sm-8'>";
			$html += "<input type='hidden' class='form-control' name='jogo_id[]' id='jogo"+QTD_JOGOS_CADASTRO+"_id' />";
			$html += "<input type='text' class='form-control' name='jogo[]' id='jogo"+QTD_JOGOS_CADASTRO+"_autocomplete' placeholder='Digite parte do nome do jogo "+QTD_JOGOS_CADASTRO+"' />";
		$html += "</div>";
		$html += "<div class='col-sm-2'>";
			$html += "<span id='jogo"+QTD_JOGOS_CADASTRO+"_check'><img scr='' /></span>";
		$html += "</div>";
	$html += "</div>";
	$("#div-jogos-extras").append($html);
});
//********************************************************************************
//fecha div msg erro
$("#sp-erro-msg").on("click", ".badge", function(){
	$(this).parent().hide();
});
//fecha div msg sucesso
$("#sp-sucesso-msg").on("click", ".badge", function(){
	$(this).parent().hide();
});
//********************************************************************************
$(".btn-danger").click(function(e){
	e.preventDefault(); //previne o evento 'normal'
	var id = $(this).attr('id');
	$("#original"+id+"_id").val("");
	$("#original"+id+"_autocomplete").val("");
	$("#valor"+id).val("");
});
//********************************************************************************
$("#collapseOne").on("click", "#btn-grupo-novo", function(e){
	e.preventDefault(); //previne o evento 'normal'
	var botao = $(this);
	var divClone = botao.clone(); 

	//var $campos = ["nome", "email", "original1_id", "valor1", "original2_id", "valor2", "original3_id", "valor3" ];
	var $campos = ["email", "original1_id", "valor1", "original2_id", "valor2", "original3_id", "valor3" ];
	var $dados = new Array();
	if($("#fechado").is(':checked')){ $('#email').attr('required', 'required');  $fechado = 1;}//se marcar grupo como FECHADO, assinala EMAIL como requerido
	else {$('#email').attr('required', false);  $fechado = 0; }
	
	// "escuta" os campos requeridos e os campos de valor
	cont = 0;
	$("#collapseOne").find("input").each(function(){
		$valor = $.trim($(this).val());
		
		if($(this).attr('required') && $valor == ''){
			$("#sp-erro-msg")
				.fadeIn()
				.html("Campo Requerido.<span class='badge'>x</span>");
			$(this).focus();
			cont++;
		}
		
		if($(this).attr('name') == 'valor'){
			$valor = $valor.replace(",", ".");
			if(!$.isNumeric($valor) && $valor != ""){
				$("#sp-erro-msg")
					.fadeIn()
					.html("Valor precisa ser numérico.<span class='badge'>x</span>");
				$(this).focus();
				$(document).scrollTop( $("#foco").offset().top );
				cont++;
			}
		}	
		
		if($.inArray($(this).attr('id'), $campos) >=0 || $(this).attr("name") == "jogo_id[]")
			$dados.push($(this).attr('id')+"%=%"+$valor); //preenche array com os dados do form
	});
	if(cont > 0) return false;
		
	//verifica se usuário colocou seu ID numa das vagas
	cont = 0;
	var selfID = $("#selfID").val(); //ID do próprio usuario logado
	$("#collapseOne").find("[name*='_id']").each(function(){
		$id = $(this).val();
		if($id != "" && $id == selfID)
			cont++;
	});
	
	if(cont <= 0){
		$("#sp-erro-msg")
			.fadeIn()
			.html("É necessário informar seu próprio ID numa das vagas do grupo.<span class='badge'>x</span>");
		$("#original1_autocomplete").focus();
		$(document).scrollTop( $("#foco").offset().top );
		return false;
	} 
	
	//verifica se o email digitado é válido
	if($("#email").val() != ""){
		if(!IsEmail($("#email").val())){
			$("#sp-erro-msg")
				.fadeIn()
				.html("E-mail Inválido.<span class='badge'>x</span>");
			$("#email").focus();
			$(document).scrollTop( $("#foco").offset().top );
			return false;
		}
	}
	
	if($("#original1_id").val() == "" && $("#original2_id").val() == ""){
		$("#sp-erro-msg")
			.fadeIn()
			.html("Não é possível criar uma conta somente com fantasma.<span class='badge'>x</span>");
		$("#original1_autocomplete").focus();
		$(document).scrollTop( $("#foco").offset().top );
		return false;
	}
	
	$dados.push("moeda_id%=%"+$("#moedas option:selected").val());
	$moeda_nome = $("#moedas option:selected").text(); 
	$dados.push("fechado%=%"+$fechado);
	$("#sp-erro-msg").fadeOut();
	//console.log($dados);return;

	var pars = { dados: $dados, id: selfID, fechado: $fechado, moeda: $moeda_nome, funcao: 'novoGrupo'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { doAnimated(botao); botao.attr('disabled', 'disabled'); },
		complete: function(){  },
		success: function(data){ 
			console.log(data); //return;
			if(data == 1){ //sucesso
				location.reload();
			} else { //erro
				$error = "";
				$.each(data, function(i, item) {
					var qtd = item.length;
					for(var z=0;z<qtd;z++)
						$error += item[z]+"<br />";
				});
				$(document).scrollTop( $("#foco").offset().top );
				$("#sp-erro-msg")
					.fadeIn()
					.html($error+"<span class='badge'>x</span>");	
				
				resetaHtml(botao, divClone);
				botao.removeAttr('disabled');
			}
		}
	});
});
//********************************************************************************
//Botão para preencher parte do e-mail padrão - Novo Grupo
$("div").on("click", "#btn-email-padrao", function(e){
	e.preventDefault(); //previne o evento 'normal'
	var botao = $(this);
	var divClone = botao.clone(); 
	
	$selfID = $("#selfID").val();
	var pars = { id: $selfID, funcao: 'montaPadraoEmail'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { doAnimated(botao); botao.attr('disabled', 'disabled'); },
		complete: function(){ },
		success: function(data){ 
			console.log(data); //return;
			if(data[0] == 1){
				alert(data[1]);
			} else {
				//$("#collapseOne").find("#email").val(data[1]);
				$("input[type=email]").val(data[1]);
			}
			resetaHtml(botao, divClone);
			botao.removeAttr('disabled');
		}
	});
});
//********************************************************************************
//LISTAGEM DE GRUPOS
$("#div-listagem-grupos").find("[name='div-casulo-grupo'] img[name='imgMais']").click(function(){
	var $selfId = $("#selfID").val();
	var $id = $(this).parent().parent().attr('id').split("_")[1]; //ID do grupo
	if($(this).attr("id") == "_0"){
		$("#grupo-conteudo_"+$id)
			.slideUp();
		$(this).prop("id", "_1");
		$(this).prop("src", "img/plus.png");
		return false;
	}
	var $elem = $(this);
		
	var pars = { id: $id, selfid: $selfId, funcao: 'mostraGrupo'};
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
			$("#grupo-conteudo_"+$id)
				.html(data)
				.slideDown();
			
			$elem.prop("src", "img/minus.png");
			$elem.prop("id", "_0");
		},
		error: function(e){
			console.log(e.responseText);
        }
	});
});
//********************************************************************************
//visualizar histórico
$(".list-group").on("click", "[name='historico-grupo']", function(e){
	e.preventDefault();
	$elem = $(this);
	$idGrupo = $(this).attr("id").split("_")[1];
	var pars = { id: $idGrupo, funcao: 'mostraHistorico'};
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
			abreModal("#dialog", data);
		}
	});
});

//********************************************************************************
$(".container-grupos").on("click", "[name='img-repasse']", function(){
	VAGA_REPASSE = $(this).attr('rel');
	GRUPO_REPASSE = parseInt($(this).attr("id").split("_")[1]);
	dataID = $(this).data('id');
	//abreModal("#repasse", $("#repasse").html());
	if (dataID == '1'){
		$("#sp-tipo-moeda").text('(Valores em Real)');
		$("#repasse").find("#lbl-alterou-senha").show();
	} else {
		$("#sp-tipo-moeda").text('(Valores em '+dataID+')');
		$("#repasse").find("#lbl-alterou-senha").hide();
	}
});
//********************************************************************************
$("#repasse").on("click", "#btn-confirma-repasse", function(){
	var botao = $(this);
	var divClone = botao.clone(); 
	
	var $erros = new Array();
	var $vaga = VAGA_REPASSE;
	var $grupo = GRUPO_REPASSE;
	//alert($grupo); return;
	$valor = $.trim($("#valor").val());
	$valor = $valor.replace(",", ".");
	$comprador = $("#original-repasse_id").val();
	$data_venda = $("#data_venda").val();
	if ($("#alterou_senha").is(":checked")) $alterou_senha = 1; else $alterou_senha = 0;
	
	if($comprador == ""){ $erros.push("- Informe um comprador válido.<br />"); }
	if($valor == "") { $erros.push("- Digite o valor da transação.<br />"); }
	if(!$.isNumeric($valor) && $valor != ""){ $erros.push("- [Valor] precisa ser numérico.<br />"); }
	
	if($data_venda == "") { $erros.push("- Data Inválida."); }
	if($erros.length > 0){
		$("#sp-erro-msg-modal")
			.fadeIn()
			.html($erros)
			.delay(2000)
			.fadeOut('slow');
		return;
	}

	var pars = { grupo: $grupo, vaga: $vaga, comprador: $comprador, valor: $valor, data_venda: $data_venda, alterou_senha: $alterou_senha, funcao: 'gravaRepasse'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { doAnimated(botao); botao.attr('disabled', 'disabled'); },
		complete: function(){  },
		success: function(data){ 
			console.log(data); //return;
		
			if(data == 1){ //sucesso
				alert("Vaga repassada!");
				location.reload();
			} else { //erro
				$error = "";
				$.each(data, function(i, item) {
					var qtd = item.length;
					for(var z=0;z<qtd;z++)
						$error += "- "+item[z]+"<br />";
				});
				$("#sp-erro-msg-modal")
					.fadeIn()
					.html($error)
					.delay(2500)
					.fadeOut('slow');
				
				resetaHtml(botao, divClone);
				botao.removeAttr('disabled');
			}	
		}
	});
});
//********************************************************************************
//LISTAGEM DE GRUPOS ANTIGOS
$("#div-listagem-grupos-antigos").find("[name='div-titulo-grupos-antigos'] img[name='imgMais']").click(function(){
	var $selfId = $("#selfID").val();
	var $id = $(this).parent().parent().attr('id').split("_")[1]; //ID do histórico
	//alert($(this).attr("id")); return;
	if($(this).attr("id") == "_0"){
		$("#div-conteudo-grupos-antigos_"+$id)
			.slideUp();
		$(this).prop("id", "_1");
		$(this).prop("src", "img/plus.png");
		return false;
	}
	var $elem = $(this);
		
	var pars = { id: $id, selfid: $selfId, funcao: 'mostraGrupoAntigo'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() {  },
		complete: function(){  },
		success: function(data){ 
			console.log(data);
			$("#div-conteudo-grupos-antigos_"+$id)
				.html(data)
				.slideDown();
			
			$elem.prop("src", "img/minus.png");
			$elem.prop("id", "_0");
		},
		error: function(e){
			console.log(e.responseText);
        }
	});
});
//********************************************************************************
//Abre DIV para confrmar disponibilização de vaga
$(".container-grupos").on("click", "[name='img-disponibiliza']", function(){
	var parte = $(this).attr('id').split("_");
	var id = parte[1];
	var vaga = parte[2];
	$("#input-valor_"+id+"_"+parte[2]).show();
});
//Fecha a DIV acima
$(".container-grupos").on("click", "[name='sp-close-input-valor']", function(){
	$(this).parent().hide();
});
//********************************************************************************
//Mostra caixa de diálogo para alteração do preço de venda no painel (HOME)
$("#div-painel-minhas-vendas").find("[name='btn-altera-valor-venda']").click(function(){
	$id = $(this).attr('id').split('_')[1];

	if($("#div-painel-altera-venda_"+$id).is(":visible")){
		$("#div-painel-altera-venda_"+$id).hide();
		return false;
	}
	
	$("#div-painel-altera-venda_"+$id).show();
	$("#div-painel-altera-venda_"+$id+" input").focus();
});
//********************************************************************************
//Altera valor da vaga no painel (HOME)
$(".div-painel-altera-venda").on("click", "button", function(){
	if(!confirm("Deseja realmente alterar o valor dessa venda?")) return false;
	var botao = $(this);
	var divClone = botao.clone(); 
	var $histID = $(this).siblings("input").attr("id").split("_")[1];
	var $valor = $(this).siblings("input").val();
	$valor = $valor.replace(",", ".");
	if(!$.isNumeric($valor) && $.trim($valor) != ""){ alert("Valor precisa ser numérico!"); return false; }
	
	var pars = { valor: $valor, id: $histID, funcao: 'alteraValorVendaVaga'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { doAnimated(botao); botao.attr('disabled', 'disabled'); },
		complete: function(){ },
		success: function(data){ 
			console.log(data);
			if (data == 1){ 
				if($valor == "") botao.parent().parent().find("#lblValor").text("0,00");
				else { $valor = $valor.replace(".", ","); botao.parent().parent().find("#lblValor").text($valor); }
				botao.parent().hide();
				//alert("Valor alterado com sucesso!"); 
			}else{ 
				alert(data["valor"][0]); 
			}
			resetaHtml(botao, divClone);
			botao.removeAttr('disabled');
		}
	});
});
//******************************************************************************** 
//Exclui a venda da vaga no painel (HOME)
$("#div-painel-minhas-vendas").find("[name='btn-exclui-venda']").click(function(){
	if(!confirm("Deseja realmente cancelar essa venda?")) return false;
	var $histID = $(this).attr("id").split("_")[1];
	var pars = { id: $histID, funcao: 'excluiVenda'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() {},
		complete: function(){ },
		success: function(data){ 
			console.log(data);
			if (data == 1) location.reload();
			else{
				alert(data); 
			}
		}
	});
});
//********************************************************************************
// Grava a disponibilização da vaga
$(".container-grupos").on("click", "[name='input-valor'] button[name='btn-grupo']", function(){
	var botao = $(this);
	var divClone = botao.clone(); 
	parte = $(this).attr('id').split("_");
	grupo = parte[1];
	//alert(grupo);
	$vaga = parte[2];
	$valor = $("#txt-valor-venda_"+grupo+"_"+$vaga).val();
	$valor = $valor.replace(",", ".");
	if(!$.isNumeric($valor) && $.trim($valor) != ""){ alert("Valor precisa ser numérico!"); return false; }
	//alert("grupo: "+grupo+" / Valor: "+$valor+" / Vaga: "+$vaga);
	var pars = { valor: $valor, id: grupo, vaga: $vaga, funcao: 'gravaDisponibilidadeVaga'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { doAnimated(botao); botao.attr('disabled', 'disabled'); },
		complete: function(){ },
		success: function(data){ 
			console.log(data);
			if (data == 1){ alert("Vaga colocada a venda com sucesso!"); location.reload(); }
			else{ 
				alert(data["valor"][0]); 
				resetaHtml(botao, divClone);
				botao.removeAttr('disabled');
			}
		}
	});
});
//********************************************************************************
//Exclui usuario de vaga de grupo aberto
$(".container-grupos").on("click", "[name='img-excluir']", function(){
	if(!confirm("Deseja realmente excluir o usuário desta vaga?")) return false;
	
	var partes = $(this).attr("id").split("_");
	var $grupo = parseInt(partes[1]);
	var $user = parseInt(partes[2]);
	var $vaga = partes[3];
	
	//alert("GRUPO: "+$grupo+" / USER: "+$user+" / VAGA: "+$vaga); return;
	var pars = { grupo: $grupo, user: $user, vaga: $vaga, funcao: 'excluiUsuarioVaga'};
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
			if (data == 1)
				location.reload();
		}
	});
});
//********************************************************************************
$('#frm-cadastra-jogos').submit(function(e){
	e.preventDefault();
	$formulario = $(this);
	if($("#nome-jogo").val() == ""){
		$("#sp-sucesso-msg").hide();
		$("#sp-erro-msg")
			.fadeIn()
			.html("Preencha o nome do jogo.<span class='badge'>x</span>");
		$("#nome-jogo").focus();
		return false;
	}
	var $form = $(this).serialize();
	$form = decodeURI(replaceAll($form, '+', ' ')); //retira alguns caracteres especiais
	$form = $form.split("&");
	//alert($form);return;
	var pars = { dados: $form, funcao: 'gravaJogo'};
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
			if(data[0] == 1){ //erro
				$("#sp-sucesso-msg").hide();
				$("#sp-erro-msg").fadeIn().html(data[1]+"<span class='badge'>x</span>");
			} else { //ok
				$("#sp-erro-msg").hide();
				$("#sp-sucesso-msg").fadeIn().html(data[1]+"<span class='badge'>x</span>");
				$formulario[0].reset();
			}
		}
	});
});
//********************************************************************************
$('#frm-altera-jogos').submit(function(e){
	e.preventDefault();
	$formulario = $(this);
	if($("#nome-jogo-altera").val() == ""){
		$("#sp-sucesso-msg").hide();
		$("#sp-erro-msg")
			.fadeIn()
			.html("Preencha o nome do jogo.<span class='badge'>x</span>");
		$("#nome-jogo-altera").focus();
		return false;
	}
	var $form = $(this).serialize();
	$form = decodeURI(replaceAll($form, '+', ' ')); //retira alguns caracteres especiais
	$form = $form.split("&");
	//alert($form);return;
	var pars = { dados: $form, funcao: 'alteraJogo'};
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
			if(data[0] == 1){ //erro
				$("#sp-sucesso-msg").hide();
				$("#sp-erro-msg").fadeIn().html(data[1]+"<span class='badge'>x</span>");
			} else { //ok
				$("#sp-erro-msg").hide();
				$("#sp-sucesso-msg").fadeIn().html(data[1]+"<span class='badge'>x</span>");
				$("#jogo-nome-altera_autocomplete").val("");
				$formulario[0].reset();
			}
		}
	});
});
//********************************************************************************
$("#aba-altera-jogos").on("click", "[name='a-ativar']", function(e){
	e.preventDefault();
	var $flag = parseInt($(this).attr('rel'));
	var $id = parseInt($("#jogo-nome-altera_id").val());
	var pars = { id: $id, flag: $flag, funcao: 'ativaInativaJogo'};
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
			if($flag == 1) $html = "Jogo Ativo -> <a href='#' name='a-ativar' rel='0'>Desativar Jogo</a>";
			else $html = "Jogo Desativado -> <a href='#' name='a-ativar' rel='1'>Ativar Jogo</a>";
			$("#sp-ativo-altera").html($html);
			$("#sp-erro-msg").hide();
			$("#sp-sucesso-msg").fadeIn().html(data+"<span class='badge'>x</span>");
		}
	});
});
//********************************************************************************  
//Mostra tela de Fechamento de Grupo
$("[name='div-casulo-conteudo-grupo']").on('click', "[name='btn-fechar-grupo']", function(){
	var idGrupo = parseInt($(this).attr('id').split("_")[1]);
	var pars = { id: idGrupo, funcao: 'mostraFechamentoGrupo'};
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
			//alert(data);
			abreModal("#modal-conteudo-fechamento-grupo", data);
		}
	});

});
//********************************************************************************
//Grava fechamento de grupo
$("#fecha-grupo").on('click', '#btn-confirma-fechamento', function(e){
	e.preventDefault(); //previne o evento 'normal'
	var botao = $(this);
	var divClone = botao.clone(); 
	
	var $erros = new Array();
	var $campos = new Array();
	var $valores = new Array();

	var idGrupo = parseInt($("#id-grupo-fechamento").val());
	var moeda_id = parseInt($("#moedas-fechamento option:selected").val());

	if($("#email-fechamento").length) var email = $.trim($("#email-fechamento").val());
	var id1 = parseInt($("#id1-fechamento").val());
	var id2 = parseInt($("#id2-fechamento").val());
	var id3 = parseInt($("#id3-fechamento").val());
	var valor1 = $.trim($("#valor-fechamento-1").val()).replace(",", ".");
	var valor2 = $.trim($("#valor-fechamento-2").val()).replace(",", ".");
	var valor3 = $.trim($("#valor-fechamento-3").val()).replace(",", ".");
	
	if(typeof email !== 'undefined'){ //se a var email existe
		if(email != ""){
			if(!IsEmail(email)) { //verifica se o email digitado é válido
				$erros.push("- E-mail do grupo Inválido.<br />");
			} else { $campos.push("email"); $valores.push(email); }
		} else {
			$erros.push("- Informe o e-mail do grupo.<br />"); 
		}
	}

	$campos.push("moeda_id"); $valores.push(moeda_id);

	if(id1 > 0){
		if(valor1 == "") $erros.push("- Informe o valor pago pelo Original 1.<br />");
		else if(!$.isNumeric(valor1) && valor1 != "") $erros.push("- Valor1 precisa ser um valor válido.<br />");
		else { $campos.push("valor1"); $valores.push(valor1); } 
	}
	if(id2 > 0){
		if(valor2 == "") $erros.push("- Informe o valor pago pelo Original 2.<br />");
		else if(!$.isNumeric(valor2) && valor2 != "") $erros.push("- Valor2 precisa ser um valor válido.<br />");
		else { $campos.push("valor2"); $valores.push(valor2); }
	}
	if(id3 > 0){
		if(valor3 == "") $erros.push("- Informe o valor pago pelo Fantasma.<br />");
		else if(!$.isNumeric(valor3) && valor3 != "") $erros.push("- Valor3 precisa ser um valor válido.<br />");
		else { $campos.push("valor3"); $valores.push(valor3); }
	}
	
	if($erros.length > 0){
		$("#sp-erro-msg-modal2")
			.fadeIn()
			.html($erros)
			.delay(2000)
			.fadeOut('slow');
		return;
	}
	$campos.push("senha_alterada");
	if ($("#alterou_senha-fechamento").is(":checked")) $valores.push(1); else $valores.push(0);
	//console.log($campos); return;
	var pars = { id: idGrupo, campos: $campos, valores: $valores, moeda: moeda_id, funcao: 'gravaFechamentoGrupo'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { doAnimated(botao); botao.attr('disabled', 'disabled'); },
		complete: function(){ },
		success: function(data){ 
			console.log(data); 
			if (data == 1) location.reload();
			else {
				$("#sp-erro-msg-modal2")
					.fadeIn()
					.html(data);
				resetaHtml(botao, divClone);
				botao.removeAttr('disabled');
			}
		}
	});
});
//********************************************************************************
$("#avaliacao").on("click", "#btn-confirma-avaliacao", function(e){
	e.preventDefault(); //previne o evento 'normal'
	var botao = $(this);
	var divClone = botao.clone(); 
	
	$recomendacaoID = $("#recomendacao_id").val();
	$texto = $("#txtTexto").val();

	if($texto == ""){
		$("#avaliacao #sp-erro-msg-modal")
			.fadeIn()
			.html("- O campo Comentário é obrigatório!")
			.delay(2000)
			.fadeOut('slow');
		$("#txtTexto").focus();
		return;
	}
	
	var pars = { recomendacaoID: $recomendacaoID, texto: $texto, funcao: 'gravaRecomendacao'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { doAnimated(botao); botao.attr('disabled', 'disabled'); },
		complete: function(){ resetaHtml(botao, divClone); botao.removeAttr('disabled'); },
		success: function(data){ 
			console.log(data); 
			if (data == 1) location.reload();
			else {
				$error = "";
				$.each(data, function(i, item) {
					var qtd = item.length;
					for(var z=0;z<qtd;z++)
						$error += "- "+item[z]+"<br />";
				});
				$("#avaliacao #sp-erro-msg-modal")
					.fadeIn()
					.html($error)
					.delay(2500)
					.fadeOut('slow');
			}
		}
	});
	//alert("Desenvolver essa funcionalidade!");
});
//********************************************************************************  
/*
 * 	ALTERAÇÕES NOS DADOS DE PERFIL DE USUÁRIO
 */
$("#aba-dados_cadastrais").on("click", "[name='btn-edita-perfil']", function(e){
	e.preventDefault(); //previne o evento 'normal'
	var botao = $(this);
	var divClone = botao.clone();
	
	var tipo = botao.attr("id").split("_")[1]; //nome, email, telegram, celular
	var campo = $("#txt_"+tipo);
	var valor = campo.val();
		
	//alert(tipo);return;
	
	switch(tipo){
		case 'telegram':
			var match = valor.match(/^[a-zA-Z0-9_]{5,30}$/); //Somente letras maiúsculas e minúsculas, numeros e sublinhado(_)
			if(!match || match == "null") {
				botao.parent().parent().children("p")
					.fadeIn()
					.html("Telegram ID inválido")
					.delay(2500)
					.fadeOut('slow');	
				campo.focus();
				return false;
			}
			break;
		case 'nome':
			if($.trim(valor) == ""){
				botao.siblings("p")
					.fadeIn()
					.html("Nome inválido")
					.delay(2500)
					.fadeOut('slow');	
				campo.focus();
				return false;
			}
			break;
		case 'email':
			var match = valor.match(/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/);
			if(!match || match == "null") {
				botao.siblings("p")
					.fadeIn()
					.html("E-mail inválido")
					.delay(2500)
					.fadeOut('slow');	
				campo.focus();
				return false;
			}
			break;
		case 'senha':
			var match = valor.match(/^[\w-!#@+]{6,10}$/);
			var senha2 = $("#txt_senha2").val();
			if(!match || match == "null") {
				botao.siblings("p")
					.fadeIn()
					.html("Senha inválida")
					.delay(2500)
					.fadeOut('slow');	
				campo.focus();
				return false;
			} else if (valor != senha2){
				botao.siblings("p")
					.fadeIn()
					.html("A redigitação da senha nova não confere com a primeira!")
					.delay(2500)
					.fadeOut('slow');	
				$("#txt_senha2").focus();
				return false;
			}
			break;
	}

	var pars = { tp: tipo, vl: valor, funcao: 'alteraPerfil'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { doAnimated(botao); botao.attr('disabled', 'disabled'); },
		complete: function(){ resetaHtml(botao, divClone); botao.removeAttr('disabled'); },
		success: function(data){ 
			console.log(data); 
			if (data == 1) location.reload();
			else {
				$("#edita_"+tipo+" p")
					.fadeIn()
					.html(data)
					.delay(2500)
					.fadeOut('slow');
			}
		}
	});
});
//********************************************************************************  
//Formulário de Indicação
$("#aba-indicacoes").find("form").submit(function(e){
	e.preventDefault(); //previne o evento 'normal'
	var botao = $(this).find("button[type=submit]");
	var divClone = botao.clone();
	
	var $nome = $.trim($("#nome").val());
	var $email = $.trim($("#email").val());
	var $tel = $.trim($("#telefone").val()); 

	var pars = { nome: $nome, email: $email, tel: $tel, funcao: 'indicaUsuario'};
	$.ajax({
		url: 'funcoes_ajax.php',
		type: 'POST',
		dataType: "json",
		contentType: "application/x-www-form-urlencoded;charset=UFT-8",
		data: pars,
		beforeSend: function() { doAnimated(botao); botao.attr('disabled', 'disabled'); },
		complete: function(){ resetaHtml(botao, divClone); botao.removeAttr('disabled'); },
		success: function(data){ 
			console.log(data); 
			if (data == "0"){ //indicação efetuada
				$("#aba-indicacoes").find("#sp-sucesso-msg-modal")
					.fadeIn()
					.html("<label>Indicação enviada a Administração com sucesso!</label>")
					.delay(2500)
					.fadeOut('slow');
				$("#aba-indicacoes").find("form")[0].reset();
			}else { //erros
				$error = "";
				$.each(data, function(i, item) {
					var qtd = item.length;
					for(var z=0;z<qtd;z++)
						$error += "- "+item[z]+"<br />";
				});
				$("#aba-indicacoes").find("#sp-erro-msg-modal")
					.fadeIn()
					.html($error)
					.delay(2500)
					.fadeOut('slow');
			}
			
			resetaHtml(botao, divClone);
			botao.removeAttr('disabled');
		}
	});
});
//********************************************************************************  

//********************************************************************************  

//********************************************************************************  

//******************************************************************************** 

//******************************************************************************** 

//********************************************************************************

});
