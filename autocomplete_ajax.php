<?php
session_start();
require_once 'classes/usuarios.class.php';
$u = new usuarios();

if( isset( $_REQUEST['query'] ) && $_REQUEST['query'] != "" ){
    $q = addslashes($_REQUEST['query']);
	
    if( isset( $_REQUEST['identifier'] ) && strstr($_REQUEST['identifier'],"original")){
		$r = $u->getAutocomplete($q);
		if ( $r ){
			echo '<ul>'."\n";
			while( $l = $r->fetch_array() ){
				$p = $l['login'];
				$p = preg_replace('/(' . $q . ')/i', '<span style="font-weight:bold;">$1</span>', $p);
				echo "\t".'<li id="autocomplete_'.$l['id'].'" rel="'.$l['id'].'|'.$l['login'].'">'. $p .'</li>'."\n";
			}
			echo '</ul>';
		}
    }
}
?>
