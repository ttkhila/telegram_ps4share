<?php
	$passOld = $_POST["passEmail"];
	$passNew = $_POST["passNova"];
	$rePassNew = $_POST["rePassNova"];
	$id = $_POST["hidId"];
	include_once 'classes/usuarios.class.php';
	$u = new usuarios();
	$u->carregaDados($id);
	
	$passOld = md5($passOld);
	$senhaAtual = $u->getSenha();
	if ($senhaAtual != $passOld){ 
		echo "A senha atual não confere com a cadastrada!<br />"; 
		echo "<button onclick='history.go(-1)'>voltar</button>";
		exit;
	}
	
	if(trim($passNew) != trim($rePassNew)){ 
		echo "A redigitação da senha nova não confere com a primeira!<br />"; 
		echo "<button onclick='history.go(-1)'>voltar</button>";
		exit;
	}
	$passNew = md5($passNew);
	$rePassNew = md5($rePassNew);
	if ($passNew == $passOld){ 
		echo "A nova senha tem que ser diferente da senha atual!<br />"; 
		echo "<button onclick='history.go(-1)'>voltar</button>";
		exit;
	}
	
	$u->troca_senha_inicial($id, $passNew);
	echo "Senha Alterada com Sucesso!<br />"; 
	echo "Clique <a href='login.php'>aqui</a> para fazer login com a nova senha.";
?>
