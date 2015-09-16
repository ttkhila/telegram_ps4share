<?php
header('Content-Type: text/html; charset=UTF-8');
class usuarios{
	private $id;
	private $nome;
	private $login;
	private $email;
	private $telefone;
	private $senha;
	private $telegram_id;
	private $primeiro_acesso;
	private $ativo;	
	private $pontos;
	private $id_email;
	private $grupo_acesso_id;
	private $con;

	public function __construct(){
		include_once 'conexao.class.php'; 
		$this->con = new conexao();
		$this->con->abreConexao();
	}
    
	public function setId($valor){$this->id = $valor;}
	public function getId(){return $this->id;}
	public function setNome($nome){$this->nome = $nome;}	
	public function getNome(){return $this->nome;}
	public function setLogin($login){$this->login = $login;}
	public function getLogin(){return $this->login;}
	public function setEmail($valor){$this->email = $valor;}
	public function getEmail(){return $this->email;}
	public function setTelefone($valor){$this->telefone = $valor;}
	public function getTelefone(){return $this->telefone;}
	public function setSenha($valor){$this->senha = $valor;}
	public function getSenha(){return $this->senha;}
	public function setTelegramId($valor){$this->telegram_id = $valor;}
	public function getTelegramId(){return $this->telegram_id;}
	public function setPrimeiroAcesso($email){$this->primeiro_acesso = $email;}
	public function getPrimeiroAcesso(){return $this->primeiro_acesso;} 
	public function setAtivo($valor){$this->ativo = $valor;}
	public function getAtivo(){return $this->ativo;}
	public function setPontos($valor){$this->pontos = $valor;}
	public function getPontos(){return $this->pontos;}
	public function setIdEmail($valor) { $this->id_email = $valor; }
	public function getIdEmail() { return $this->id_email; }
	public function setGrupoAcessoId($valor) { $this->grupo_acesso_id = $valor; }
	public function getGrupoAcessoId() { return $this->grupo_acesso_id; }
//---------------------------------------------------------------------------------------------------------------   
    // Descarrega os dados QUE ESTÃO PREVIAMENTE CARREGADOS NAS VARIÁVEIS DA CLASSE
    // para um array
    public function getDados(){
        $dados = array();
        array_push($dados, $this->getId());
		array_push($dados, $this->getNome());
		array_push($dados, $this->getLogin());
		array_push($dados, $this->getEmail());
		array_push($dados, $this->getTelefone());
		array_push($dados, $this->getSenha());
		array_push($dados, $this->getTelegramId());
		array_push($dados, $this->getPrimeiroAcesso());
		array_push($dados, $this->getAtivo());
		array_push($dados, $this->getPontos());
		array_push($dados, $this->getIdEmail());
		array_push($dados, $this->getGrupoAcessoId());
        return $dados;
    }
//---------------------------------------------------------------------------------------------------------------
    public function carregaDados($usuario_id){ 
        $res = $this->con->uniConsulta("SELECT * FROM usuarios WHERE id = '$usuario_id'");    
        $this->setId($res->id);
        $this->setNome($res->nome);
        $this->setlogin($res->login);
        $this->setEmail($res->email); 
        $this->setTelefone($res->telefone); 
        $this->setSenha($res->senha); 
        $this->setTelegramId($res->telegram_id); 
        $this->setPrimeiroAcesso($res->primeiro_acesso);
        $this->setAtivo($res->ativo);
        $this->setPontos($res->pontos);
        $this->setIdEmail($res->id_email);  
        $this->setGrupoAcessoId($res->grupo_acesso_id);
    }	
//---------------------------------------------------------------------------------------------------------------
	public function getAutocomplete($q){
		$q = $this->con->escape($q);
		$sql = "SELECT * FROM usuarios where locate('$q',login) > 0  AND ativo = 1 order by locate('$q',login) limit 10";
		return $res = $this->con->multiConsulta( $sql );
	}
//---------------------------------------------------------------------------------------------------------------
    public function validaLogin($dados){
	$query = "SELECT * FROM usuarios WHERE login='".addslashes(utf8_encode($dados['login']))."' AND senha='".md5(trim($dados['senha']))."'";
        $res = $this->con->multiConsulta($query); 
        if ($res->num_rows > 0) //login OK
            return $res->fetch_object();
    }
//---------------------------------------------------------------------------------------------------------------
    public function troca_senha_inicial($id, $senhaNova){
        //$senhaNova = md5($senhaNova);
        $dt = date("Y/m/d");
        $query = "UPDATE usuarios SET senha = '$senhaNova', primeiro_acesso = 0, primeiro_acesso_data = '$dt' WHERE id = $id";
        try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
    }
//---------------------------------------------------------------------------------------------------------------
	public function alteraCampoPerfil($campo, $valor, $id){
		//echo json_encode("dweqdqwed"); exit;
		$query = "UPDATE usuarios SET $campo = '$valor' WHERE id = $id";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
//---------------------------------------------------------------------------------------------------------------
	public function retornaTudo($ordem){
		return $res = $this->con->multiConsulta("SELECT * FROM usuarios WHERE id <> 0 AND ativo = 1 ORDER BY $ordem"); 
	}
//---------------------------------------------------------------------------------------------------------------
	public function totalUsuarios(){
		$res = $this->con->multiConsulta("SELECT * FROM usuarios"); 
		return $res->num_rows;
	}
//---------------------------------------------------------------------------------------------------------------
	/*
	 * Esta função recebe 3 parâmetros:
	 * - um array com as clausulas WHERE no formato ([campo] = valor)
	 * - uma string com somente uma clausula ORDER BY (login, login desc)
	 * - um array com os campos retornados na consulta (login, nome, email, etc)
	*/
	public function montaQuerySimples($w, $o, $f = array('*')){
		$query = "";
		foreach ($f as $v) { $query .= $v.", "; }
		$filtro = substr_replace($query ,"",-2);
		$query = "";
		
		foreach($w as $c => $v){ $query .= $c." = ".$v.", "; }
		$where = substr_replace($query ,"",-2);

		$query = "SELECT ".$filtro." FROM usuarios WHERE ".$where." ORDER BY ".$o;

		return $res = $this->con->multiConsulta($query);
	}
//---------------------------------------------------------------------------------------------------------------
	public function primeiraConsultaPaginada($ordem, $off, $pp){
		 return $res = $this->con->multiConsulta("SELECT u.*, g.nome as grupo_nome FROM usuarios u, grupos_acesso g 
			WHERE (g.grupo_id = u.grupo_acesso_id) AND (u.usuario_id <> 0) ORDER BY $ordem LIMIT $off, $pp"); 
	}
//---------------------------------------------------------------------------------------------------------------
	public function ConsultaPaginada($where = '',$ordem, $off, $pp){
		$res = $this->con->multiConsulta("SELECT u.*, g.nome as grupo_nome FROM usuarios u, grupos_acesso g 
			WHERE (g.grupo_id = u.grupo_acesso_id) AND (u.usuario_id <> 0) ORDER BY $ordem LIMIT $off, $pp"); 
		return $this->montaPaginaListagem($res);
	}
//--------------------------------------------------------------------------------------------------------------- 
	private function montaPaginaListagem($r){
		$cont = "";
		$cor = 0;
		while($d = $r->fetch_object()){
			if($d->ativo == 1) { //se cadastro ATIVO, seleciona cor da linha...
				if ($cor % 2 == 0) $classe = 'cor1';
				else $classe = 'cor2';
			} else { //...ou seleciona INATIVO
				$classe = 'inativo';
			}
			$cont .= "<tr class='".$classe."'><td>".stripslashes(utf8_decode($d->login))."</td>
				<td>".stripslashes(utf8_decode($d->nome))."</td>
				<td>".stripslashes(utf8_decode($d->email))."</td>
				<td>".stripslashes(utf8_decode($d->grupo_nome))."</td>
				<td align='left'>
				<a href='#' name='btnAltera' id='Usuario_".$d->usuario_id."'>alterar</a> | ";
				if($d->ativo == 1)
					$cont .= "<a href='0' name='btnAtivaDesativa' id='Usuario_".$d->usuario_id."'>inativar</a>";
				else
					$cont .= "<a href='1' name='btnAtivaDesativa' id='Usuario_".$d->usuario_id."'>ativar</a>";
			$cont .= "</td></tr>";
			$cor++;
		}

		return $cont;
	}

//---------------------------------------------------------------------------------------------------------------
	private function geraSenha(){
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
//---------------------------------------------------------------------------------------------------------------   
    public function incluiUsuario($dados, $id = null){
    	$query1 = '';
		$query2 = '';

    	foreach ($dados as $key => $value) {
    		if ($key == 'login'){ //checar PSN (login) em duplicidade
                if ($this->checaDuplicidade(trim($value)) > 0){ //duplicidade
                	//$result[0] = utf8_encode("Já existe usuário cadastrado com esse login!");
                    //return $result;
                    return array("login", "Já existe usuário cadastrado com esse login!", 0);
                }
            }
			
			if($key == 'email')
				$email = trim($value); //valor a ser comparado com o e-mail repetido
			
			if($key == 'senha'){ //senha sendo último campo do formulário, caso contrário haverá erro
				$senha_descrip = addslashes(utf8_encode($value)); //senha descriptografada
				$query1 .= 'senha_descriptografada, ';
				$query2 .= "'".$senha_descrip."', ";
				$value = md5($value); //criptografa a senha
				$query1 .= $key;
				$query2 .= "'".addslashes(utf8_encode($value))."'";
			} else {
				$query1 .= $key.', ';
				$query2 .= "'".addslashes(utf8_encode($value))."', ";
			}
		}

    	$query = "INSERT INTO usuarios (".$query1.") values (".$query2.")";
    	$id = $this->con->executa($query, 1); //insere usuário e retorna seu ID
        
        //Cria registro de disponibilidade
        $query = "INSERT INTO disponibilidades (usuario_id) VALUES ($id)";
        $this->con->executa($query);
        
        return array(1, "Cadastro efetuado com sucesso! Clique <a href='login.php'>aqui</a> para fazer login.", $id);
        /*
		 * 
		 *  ENVIA E-MAIL   
        $query = "SELECT nome, login, senha_descriptografada FROM usuarios WHERE usuario_id = ".$id;
        $res = $this->con->uniConsulta($query);
        
        $body_email = "Olá, ".stripslashes(utf8_decode($res->login))."!\r\nSua senha provisória de acesso ao sistema é: ".$res->senha_descriptografada."\r\n";
        $body_email .= "Acesse nosso site e faça login com essa senha e o login cadastrado.\r\n";
        $body_email .= "Nesse primeiro acesso, você terá que mudar sua senha. Assim que o fizer, estará apto a utilizar todas as funcionalidades do site.";
        
        $body_email = wordwrap($body_email, 70, "\r\n");
        
            
       // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        
        // Additional headers
        $headers .= 'To: '.stripslashes(utf8_decode($res->nome))."\r\n";  
        $headers .= 'X-Mailer: PHP/' . phpversion();
        
        ini_set("SMTP", "smtp.mail.yahoo.com.br");
        ini_set("sendmail_from", "e_rocha78");
        ini_set("smtp_port", "465");
        
        if (@mail($email, "Seja Bem Vindo!", $body_email, $headers)){
            $msg = "Seu cadastro foi efetuado, porém precisamos que ele seja ativado.<br />";
            $msg .= "Para isso, pedimos que vá a caixa postal do seu e-mail cadastrado e siga as instruções que foram enviadas.<br />";
            $msg .= "Qualquer dúvida, escreva para xxxxxx@xxxxxx.com.br para que posssamos ajudá-lo.<br />Obrigado!";
            
            return $msg;
            
        } 
            
        return "Erro ao enviar a mensagem";
*/
    }
//---------------------------------------------------------------------------------------------------------------   
    public function alteraUsuario($dados, $id){
        $query = '';
        foreach ($dados as $key => $value) {  
			if($id == $_SESSION['ID'] && $key == "grupo_acesso_id"){ //chance do usuário logado ter alterado o próprio Grupo de Acesso
				$this->carregaDados($id); //carrega os dados do usuario
				if($this->getGrupoId() != $value){ //Foi alterado o grupo de acesso

					require 'mensagens.class.php';
					require 'grupos_acesso.class.php';
					$m = new mensagens();
					$gaAtual = new grupos_acesso();
					$gaNovo = new grupos_acesso();
					//recupera os NOMES dos grupos de acesso atual e os novos
					$gaAtual->carregaDados($this->getGrupoId());$grupoAtual = stripslashes(utf8_decode($gaAtual->getNome()));
					$gaNovo->carregaDados($value);$grupoNovo = stripslashes(utf8_decode($gaNovo->getNome())); 
					
					$usuario = stripslashes(utf8_decode($this->getLogin())); //recupera NOME do usuário qu está fazendo a alteração			
					$acao = $m->incluiAcao($_SESSION['ID']); //cria uma nova ação
					$dados['texto'] = "O usuário <strong>$usuario</strong> alterou seu próprio acesso ao sistema, passando de <strong>'$grupoAtual'</strong> para <strong>'$grupoNovo'</strong>.<br />
						Para que essa mudança tenha validade, é preciso que outro ADM confirme.<br />
						Você confirma essa mudança?<br />
						<button class='btn-intra-msg' name='btn-confirma-permissao' id='btn-sim' alt='$acao'>Sim</button>&nbsp;&nbsp;&nbsp;
						<button class='btn-intra-msg' name='btn-confirma-permissao' id='btn-nao'>Não</button>";
 						
					$para = $this->getAdministradores($_SESSION['ID']);
					$m->incluiMensagem($dados, 0, $para, $acao);
					
					//envia mensagem de aviso para o usuario que fez a mudança
					$dados['texto'] = "Você efetuou uma alteração em seu próprio grupo de acesso.<br />
						Para que esta alteração tenha efeito, é preciso aguardar que outro ADM faça uma confirmação.";
					$para = array($_SESSION['ID']);
					$m->incluiMensagem($dados, 0, $para);
					
					//faz alteração do grupo TEMPORÁRIA
					$query .= 'grupo_acesso_id_temp = "'.addslashes(utf8_encode($value)).'", ';
				}
			} else 
				$query .= $key.' = "'.addslashes(utf8_encode($value)).'", ';
        }
        $query = substr_replace($query ,"",-2);
        $query = "UPDATE usuarios SET ".$query." WHERE usuario_id = ".$id;
        
        //return "ID: ".$_SESSION['ID'];exit;
		
        $this->con->executa($query);
		
		return array(1, "CADASTRO ALTERADO COM SUCESSO!", $id);
    }
//---------------------------------------------------------------------------------------------------------------   
	public function ativo_inativo_alterna($f){
		$query = "UPDATE usuarios SET ativo = $f WHERE usuario_id = ".$this->getId();
		$this->con->executa($query);
		
		return array(1, "CADASTRO ALTERADO COM SUCESSO!");
	}
//---------------------------------------------------------------------------------------------------------------
    public function checaDuplicidade($login){
    	
    	$login = addslashes(utf8_encode($login));
        $query = "SELECT usuario_id FROM usuarios WHERE login = '$login'";
        $res = $this->con->multiConsulta($query);

        return $res->num_rows;    
    }
//--------------------------------------------------------------------------------------------------------------- 
  
    public function busca($tipos, $pagina, $perPage){ // 10 = registros por pÃ¡gina
        $pagina--;
        $pagina *= $perPage;
        //monta o final da consulta
        $query = $this->montaQuery($tipos);

        $query = "SELECT u.id, u.nome, u.login, u.email, g.nome as grupo 
                            FROM usuarios u, grupos g
                            WHERE (u.grupo_id = g.id) AND ".$query." ORDER BY u.login LIMIT $pagina, $perPage";

        $res = $this->con->multiConsulta($query);

        return $res;
    }
//---------------------------------------------------------------------------------------------------------------
    //retorna a quantidade de registros sem levar em conta a paginaÃ§Ã£o
    public function preBusca($tipos){
        $query = $this->montaQuery($tipos);

        $query = "SELECT u.id, u.nome, u.login, u.email, g.nome as grupo
                            FROM usuarios u, grupos g
                            WHERE (u.grupo_id = g.id) AND ".$query." ORDER BY u.login";

        $res = $this->con->multiConsulta($query);

        return $res->num_rows;
    }
//---------------------------------------------------------------------------------------------------------------
    private function montaQuery($tipo){
        $query = '';
        foreach ($tipo as $value) {
            $query .= $value." AND ";
        }

        $query = substr_replace($query ,"",-4);
        return $query;
    }
//---------------------------------------------------------------------------------------------------------------   
    public function carregaCampos(){
        $campos = array(
            'login' => 'login',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'grupo' => 'Grupo'
        );
        return $campos;
    }
//---------------------------------------------------------------------------------------------------------------
	private function getAdministradores($out){
		//$out = ID do proprio usuário logado no sistema - SESSION[id]
		$query = "SELECT usuario_id FROM usuarios WHERE grupo_acesso_id = 1 AND usuario_id <> $out";
		try{ $ret = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		$para = array();
		while($d = $ret->fetch_object()){
			array_push($para, $d->usuario_id);
		}
		return $para;
	}
//---------------------------------------------------------------------------------------------------------------
	public function confirmaPermissao($alvo){
		$this->carregaDados($alvo);
		$gTemp = $this->getGrupoIdTemp();
		$query = "UPDATE usuarios SET grupo_acesso_id = $gTemp, grupo_acesso_id_temp = 'NULL' WHERE usuario_id = $alvo";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------


}
?>
