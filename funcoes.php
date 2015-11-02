<?php
//---------------------------------------------------------------------------------------
function login($mn){
	if(!isset($_SESSION['login']))
		$msg = "<p class='navbar-inverse navbar-text' style='padding-left:15px;'>Ol&aacute;, Visitante! <a href='login.php' style='padding:0'>[login]</a></p>";
	else { 
		include_once 'classes/usuarios.class.php';
		$u = new usuarios();
		$adm = $u->is_adm($_SESSION['ID']);
		if ($adm) $msg = "
			<p class='navbar-inverse navbar-text' style='padding-left:15px;'>Ol&aacute;, ".$_SESSION['login']."! 
				<a href='#' id='deslogar'>[sair]</a>
				<a href='adm.php'>[adm]</a>
			</p>";
		else $msg = "<p class='navbar-inverse navbar-text' style='padding-left:15px;'>Ol&aacute;, ".$_SESSION['login']."! <a href='#' id='deslogar'>[sair]</a></p>";
	}
		
	$mn = str_replace("%%user%%", $msg, $mn);
	
	return $mn; 
}
//---------------------------------------------------------------------------------------
function after ($this, $inthat){
	if (!is_bool(strpos($inthat, $this)))
	return substr($inthat, strpos($inthat,$this)+strlen($this));
}

function after_last ($this, $inthat){
	if (!is_bool(strrevpos($inthat, $this)))
	return substr($inthat, strrevpos($inthat, $this)+strlen($this));
}

function before ($this, $inthat){
    return substr($inthat, 0, strpos($inthat, $this));
}

function before_last ($this, $inthat){
    return substr($inthat, 0, strrevpos($inthat, $this));};

function between ($this, $that, $inthat){
    return before ($that, after($this, $inthat));};

function between_last ($this, $that, $inthat){
    return after_last($this, before_last($that, $inthat));
}

// use strrevpos function in case your php version does not include it
function strrevpos($instr, $needle){
    $rev_pos = strpos (strrev($instr), strrev($needle));
    if ($rev_pos===false) return false;
    else return strlen($instr) - $rev_pos - strlen($needle);
}

/*
 * 
 * EXEMPLOS
 * 
after ('@', 'biohazard@online.ge');
//returns 'online.ge'
//from the first occurrence of '@'

before ('@', 'biohazard@online.ge');
//returns 'biohazard'
//from the first occurrence of '@'

between ('@', '.', 'biohazard@online.ge');
//returns 'online'
//from the first occurrence of '@'

after_last ('[', 'sin[90]*cos[180]');
//returns '180]'
//from the last occurrence of '['

before_last ('[', 'sin[90]*cos[180]');
//returns 'sin[90]*cos['
//from the last occurrence of '['

between_last ('[', ']', 'sin[90]*cos[180]');
//returns '180'
//from the last occurrence of '['

*/
//---------------------------------------------------------------------------------------
function geraSenha(){
	$palavra = array('tar', 'gar', 'for', 'jar', 'mar', 'dir', 'dor', 'zar', 'dum', 'ges', 'gis', 'mor', 
						'por', 'par', 'zeu', 'meu', 'din', 'mou', 'nau', 'val', 'liu', 'xor', 'vis', 
						'nos', 'mes', 'ter', 'tri', 'fiu', 'nat', 'dio', 'zoz', 'teb', 'kaf', 'koy', 'ret', 
						'tad', 'kob', 'los');
	$num1 = rand(0,9);
	$num2 = rand(0,9);
	$num3 = rand(0,9);

	$idx = rand(0,count($palavra)-1);
	$senha = $palavra[$idx].$num1.$num2.$num3;

	return $senha;
}
?>
