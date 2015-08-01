<?php
session_start();
require_once 'classes/jogos.class.php';
$j = new jogos();
if( isset( $_REQUEST['query'] ) && $_REQUEST['query'] != "" ){
    $q = addslashes(utf8_encode($_REQUEST['query']));
	
    if( isset( $_REQUEST['identifier'] ) && strstr($_REQUEST['identifier'],"jogo")){
		$r = $j->getAutocomplete($q);
		if ( $r ){
			echo '<ul>'."\n";
			while( $l = $r->fetch_array() ){
				$p = $l['nome'].' ('.$l['plataforma'].')';
				$p = preg_replace('/(' . $q . ')/i', '<span style="font-weight:bold;">$1</span>', $p);
				echo "\t".'<li id="autocomplete_'.$l['id'].'" rel="'.$l['id'].'|'.$l['nome'].' ('.$l['plataforma'].')">'. utf8_encode( $p ) .'</li>'."\n";
			}
			echo '</ul>';
		}
    }
}
?>
