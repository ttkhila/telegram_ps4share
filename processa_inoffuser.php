<?php
	require 'classes/usuarios.class.php';
	$u = new usuarios();
	
	if(isset($_POST['txtNome'])){
		
		$nome = addslashes(utf8_encode($_POST['txtNome']));
		$email = $_POST['txtEmail'];
		$tel = $_POST['txtTelefone'];
		$login = addslashes(utf8_encode($_POST['txtLogin']));
		$indicado_por = $_POST['original1_id'];
		$emailID = $_POST['txtIdEmail'];
		
		$id = $u->insereUsuario($nome, $login, $email, $tel, $emailID, '', $indicado_por);
		$u->criaPreferencias($id);
		
		echo "usuário inserido com sucesso!<br />";
		echo "<a href='inoffuser.php'>Voltar</a>";
		
	} else {
		
		$nomesB = $_POST['arrNomesBrutos'];
		$nomesN = $_POST['arrNomesConv'];
		
		$nomesB = explode("||", $nomesB);
		$nomesN = explode("||", $nomesN);
		
		array_pop($nomesB);
		array_pop($nomesN);
		
		//print_r($nomesN); exit;
		
		//$bruto = current($nomesB);
		//$novo = current($nomesN);
		//$u->alteraNome($bruto, $novo);
		
		//echo current($nomesB)."<br /><br />";
		//echo next($nomesB)."<br /><br />";
		
		for ($i = 0; $i < count($nomesB); $i++){
			$u->alteraNome($nomesB[$i], $nomesN[$i]);
		}
		
		//foreach($nomes as $valor){
			//$u->alteraNome($velhoNome, $novoNome);
			
		//}
		
	}
	
	exit;
	
	
?>	
