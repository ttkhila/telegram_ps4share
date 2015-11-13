<?php
	include_once 'classes/compartilhamentos.class.php';
	include 'classes/phplot/phplot.php';
	$moeda = $_GET['moeda'];
	
	$c = new compartilhamentos();
	$cambio = $c->getVariacaoCambial($moeda);
	//Definindo o array.
	$varCambial = array();

	while($camb = $cambio->fetch_array()){
		$varCambial[] = array($camb['dia_compra'], $camb['fator_conversao']);
		$nomeMoeda = $camb['moedaNome'];
	}

	$graph =& new PHPlot(1150,350);
	$graph->SetDataValues($varCambial);
	$graph->SetXTitle(utf8_decode('Data'));
	$graph->SetYTitle(utf8_decode("Valor $nomeMoeda"));
	$graph->DrawGraph();
?>
