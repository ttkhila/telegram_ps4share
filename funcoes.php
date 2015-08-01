<?php
//---------------------------------------------------------------------------------------
function login($mn){
	if(!isset($_SESSION['login']))
		$msg = "<li style='color:#ff0;padding-top:5px;'>Ol&aacute;, Visitante!<a href='login.php' style='padding:0'>[login]</a></li>";
	else  
		$msg = "<li style='color:#ff0;padding-top:5px;'>Ol&aacute;, ".$_SESSION['login']."!<br />
			<a href='/fifa_telegram/adm/index.php' id='' style='padding:0;display:inline-table'></a>
			<a href='#' id='deslogar' style='padding:0;display:inline-table'>[sair]</a></li>";
		
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
?>
