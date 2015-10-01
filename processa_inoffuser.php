<?php
	require 'classes/usuarios.class.php';
	
	$nome = addslashes(utf8_encode($_POST['txtNome']));
	$email = $_POST['txtEmail'];
	$tel = $_POST['txtTelefone'];
	$login = addslashes(utf8_encode($_POST['txtLogin']));
	$indicado_por = $_POST['original1_id'];
	$emailID = $_POST['txtIdEmail'];
	//echo $nome."<br />";
	//echo $email."<br />";
	//echo $tel."<br />";
	//echo $login."<br />";
	//echo $indicado_por."<br />";
	//echo $emailID."<br />";
	
	$u = new usuarios();
	$u->insereUsuario($nome, $login, $email, $tel, $emailID);
	
	echo "usuário inserido com sucesso!<br />";
	echo "<a href='inoffuser.php'>Voltar</a>";
?>