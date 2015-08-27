<?php
header('Content-Type: text/html; charset=UTF-8');
class grupos_acesso{
	private $id;
	private $nome;
	private $manipula_jogos;
	private $manipula_usuarios;
	private $manipula_configuracoes;
	private $con;

	public function __construct(){
		include_once 'conexao.class.php'; 
		$this->con = new conexao();
		$this->con->abreConexao();
	}
    
	public function setId($valor){$this->id = $valor;}
	public function getId(){return $this->id;}
    
	public function setNome($valor){$this->nome = $valor;}	
	public function getNome(){return $this->nome;}
	
	public function setManipulaJogos($valor){$this->manipula_jogos = $valor;}	
	public function getManipulaJogos(){return $this->manipula_jogos;}
	
	public function setManipulaUsuarios($valor){$this->manipula_usuarios = $valor;}	
	public function getManipulaUsuarios(){return $this->manipula_usuarios;}
	
	public function setManipulaConfiguracoes($valor){$this->manipula_configuracoes = $valor;}	
	public function getManipulaConfiguracoes(){return $this->manipula_configuracoes;}

//---------------------------------------------------------------------------------------------------------------   
	// Descarrega os dados QUE ESTÃO PREVIAMENTE CARREGADOS NAS VARIÁVEIS DA CLASSE
	// para um array
	public function getDados(){
		$dados = array();
		array_push($dados, $this->getNome());
		array_push($dados, $this->getManipulaJogos());
		array_push($dados, $this->getManipulaUsuarios());
		array_push($dados, $this->getManipulaConfiguracoes());
		return $dados;
	}
//---------------------------------------------------------------------------------------------------------------
	public function retornaTudo($ordem){
		return $res = $this->con->multiConsulta("SELECT * FROM grupos_acesso ORDER BY $ordem"); 
	}
//---------------------------------------------------------------------------------------------------------------
	public function carregaDados($grupo_id){ 
		$res = $this->con->uniConsulta("SELECT * FROM grupos_acesso WHERE id = '$grupo_id'");

		$this->setId($res->id);
		$this->setNome($res->nome);
		$this->setManipulaJogos($res->manipula_jogos);
		$this->setManipulaUsuarios($res->manipula_usuarios);
		$this->setManipulaConfiguracoes($res->manipula_configuracoes);
	}
//---------------------------------------------------------------------------------------
function setPermissao($usuario){
	$query = "SELECT ga.id FROM grupos_acesso ga, usuarios u WHERE (ga.id = u.grupo_acesso_id) AND (u.id = $usuario)";
	try{ $res = $this->con->uniConsulta($query); } catch(Exception $e){ return $e.message; }
	
	$this->carregaDados($res->id);
}






















//---------------------------------------------------------------------------------------------------------------   
	/*
	 * 
	 * 
	 *  ATENÇÃO: AS VARIÁVEIS ABAIXO PRECISAM SER REVISTAS, POIS O ARQUIVO FOI COPIADO DE OUTRA CLASSE
	 * 
	 * 
	 */
    public function incluiGrupo($dados, $id = null){
    	$query1 = '';
		$query2 = '';

    	foreach ($dados as $key => $value) {
    		if ($key == 'login'){ //checar PSN (login) em duplicidade
                if ($this->checaDuplicidade(trim($value)) > 0){ //duplicidade
                	//$result[0] = utf8_encode("Já existe usuário cadastrado com esse login!");
                    //return $result;
                    return array("login", "Já existe usuário cadastrado com esse login!");
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
		//echo $query;
		//exit;
    	$id = $this->con->executa($query, 1); //insere usuário e retorna seu ID
        
        
        return array(1, "Cadastro efetuado com sucesso!");
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
    public function validaLogin($dados){
        $usuario = addslashes(utf8_encode($dados[0]));
        $senha = md5(trim($dados[1]));
        
        $query = "SELECT id, login, trocar_senha FROM usuarios WHERE login = '$usuario' AND senha = '$senha'";

        $res = $this->con->multiConsulta($query);
        
        if ($res->num_rows > 0) //login OK
            return $res->fetch_object();
    }
//---------------------------------------------------------------------------------------------------------------
    public function troca_senha($id, $senhaNova){
        $senhaNova = md5($senhaNova);
        $query = "UPDATE usuarios SET senha = '$senhaNova', senha_temp = '', trocar_senha = 0, ativo = 1 WHERE id = $id";
        $this->con->executa($query);
    }
//---------------------------------------------------------------------------------------------------------------

}
?>
