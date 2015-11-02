<?php
	$senhaEmail = addslashes(strip_tags(trim($_POST["passEmail"])));
	$senhaNova = addslashes(strip_tags(trim($_POST["passNova"])));
	$reSenhaNova = addslashes(strip_tags(trim($_POST["rePassNova"])));
	$id = $_POST["hidId"];
	include_once 'classes/usuarios.class.php';
	include_once 'classes/validacoes.class.php';
	$u = new usuarios();
	$v = new validacoes();
	$u->carregaDados($id);
	//$primeiro_acesso = $u->getPrimeiroAcesso();
	
	//verifica se o código informado pertence a usuário
	$check = $u->checaCodTrocaSenha($id, $senhaEmail);
	if (!$check){ //código ou ID incorretos
		echo "Esse código não pertence a essa ID!<br />"; 
		echo "<button onclick='history.go(-1)'>voltar</button>";
		exit;
	}

	if($senhaNova != $reSenhaNova){ 
		echo "A redigitação da senha nova não confere com a primeira!<br />"; 
		echo "<button onclick='history.go(-1)'>voltar</button>";
		exit;
	}
	
	$v->set("Senha", $senhaNova)->is_required()->is_alpha_num();
	if($v->validate()){
		$senhaNova = md5($senhaNova);
	
		$u->troca_senha_requisicao($id, $senhaNova); //troca a senha
    	$u->deletaSenhaTemp($id);//apaga a senha temporária
		echo "Senha Alterada com Sucesso!<br />"; 
		echo "Clique <a href='login.php'>aqui</a> para fazer login com a nova senha.";
	}else{
		$erros = $v->get_errors();
		foreach ($erros as $erro){ //Percorre todos os erros
			foreach ($erro as $err){ //Percorre cada erro do campo especifico
				echo '<p>' . $err . '</p>';
			}
		}
		echo "<button onclick='history.go(-1)'>voltar</button>";
	}
?>
