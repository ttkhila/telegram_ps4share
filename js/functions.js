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
    
	function abreModal(id, data, top){
		//id->ID do elemento que recebe o conteúdo (com#)
		//data->Conteúdo do elemento id
		//top->Altura do elemento chamador (que será parametro para a altura de id)
		//var maskHeight = $(document).height();
		//var maskWidth = $(window).width();
	
		//$('#mask').css({'width':maskWidth,'height':maskHeight});
		//$('#mask').fadeIn(1000);	
		//$('#mask').fadeTo("slow",0.8);	

		//var winH = $(window).height();
		//var winW = $(window).width();
              
		//$(id).css({ 'top': top-$(id).height()/2, 'left': winW/2-$(id).width()/2 });
	
		//$(id).fadeIn(2000); 
		$(id).html(data);
	}
	
	function closeModal(){
		//return '<a href="#" class="close">Fechar [X]</a>';
	}
	
	//$('.window').on('click', '.close', function (e) {
//		e.preventDefault();
//		$('#mask').hide();
//		$('.window').hide();
//	});		
//	
//	$('#mask').click(function () {
//		$(this).hide();
//		$('.window').hide();
	//});			
//********************************************************************************
//LOGIN
$("#frmLogin").submit(function(e){
	e.preventDefault(); //previne o evento 'normal'
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
		beforeSend: function() { $("img.pull-right").fadeIn('fast'); },
		complete: function(){ $("img.pull-right").fadeOut('fast'); },
		success: function(data){ 
			console.log(data);
			if(data[0] == 0){ //error
				$("#sp-erro-msg")
					.fadeIn()
					.html(data[1]+"<span class='badge'>x</span>");
			} else {
				$(location).attr('href', 'index.php');
			} 			
		}
	});
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
$('[data-toggle="tooltip"]').tooltip(); 
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
$("#btn-grupo-novo").click(function(e){
	e.preventDefault(); //previne o evento 'normal'
	var $campos = ["nome", "email", "original1_id", "valor1", "original2_id", "valor2", "original3_id", "valor3" ];
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
			return false;
		}
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
		beforeSend: function() { $("img.pull-right").fadeIn('fast'); },
		complete: function(){ $("img.pull-right").fadeOut('fast'); },
		success: function(data){ 
			console.log(data); 
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
			}
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
	$elemTop = parseInt($elem.offset().top);
	//alert($elemTop);return;
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
			//$("#div-historico-grupo").show().css("top", ($elemTop-100)).html(data);
			abreModal("#dialog", data, $elemTop);
			//$("#hidFlag").val("1");
			//$elem.text("Fechar");
		}
	});
});

//********************************************************************************
$(".container-grupos").on("click", "[name='img-repasse']", function(){
	VAGA_REPASSE = $(this).attr('rel');
	GRUPO_REPASSE = parseInt($(this).attr("id").split("_")[1]);
	$elemTop = parseInt($(this).offset().top);
	$(".close").html("");
	abreModal("#repasse", closeModal()+$("#repasse").html(), $elemTop);
});
//********************************************************************************
$("#repasse").on("click", "#btn-confirma-repasse", function(){
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
		beforeSend: function() { $("img.pull-right").fadeIn('fast'); },
		complete: function(){ $("img.pull-right").fadeOut('fast'); },
		success: function(data){ 
			console.log(data);
		
			if(data == 1){ //sucesso
				location.reload();
			} else { //erro
				$error = "";
				$.each(data, function(i, item) {
					var qtd = item.length;
					for(var z=0;z<qtd;z++)
						$error += "- "+item[z]+"<br />";
				});
				//$(document).scrollTop( $("#foco").offset().top );
				$("#sp-erro-msg-modal")
					.fadeIn()
					.html($error)
					.delay(2500)
					.fadeOut('slow');
			}
		
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

//********************************************************************************  

//********************************************************************************  

//********************************************************************************  

//********************************************************************************  

//********************************************************************************  

//******************************************************************************** 

//******************************************************************************** 

//********************************************************************************

});
