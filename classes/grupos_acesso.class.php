<?php
header('Content-Type: text/html; charset=UTF-8');
class grupos_acesso{
	private $id;
	private $adm;
	private $nome;
	private $manipula_jogos;
	private $manipula_usuarios;
	private $manipula_configuracoes;
	private $libera_indicados;
	private $con;

	public function __construct(){
		include_once 'conexao.class.php'; 
		$this->con = new conexao();
		$this->con->abreConexao();
	}
    
	public function setId($valor){$this->id = $valor;}
	public function getId(){return $this->id;}
	
	public function setAdm($valor){$this->adm = $valor;}
	public function getAdm(){return $this->adm;}
    
	public function setNome($valor){$this->nome = $valor;}	
	public function getNome(){return $this->nome;}
	
	public function setManipulaJogos($valor){$this->manipula_jogos = $valor;}	
	public function getManipulaJogos(){return $this->manipula_jogos;}
	
	public function setManipulaUsuarios($valor){$this->manipula_usuarios = $valor;}	
	public function getManipulaUsuarios(){return $this->manipula_usuarios;}
	
	public function setManipulaConfiguracoes($valor){$this->manipula_configuracoes = $valor;}	
	public function getManipulaConfiguracoes(){return $this->manipula_configuracoes;}
	
	public function setLiberaIndicados($valor){$this->libera_indicados = $valor;}	
	public function getLiberaIndicados(){return $this->libera_indicados;}

//---------------------------------------------------------------------------------------------------------------   
	// Descarrega os dados QUE ESTÃO PREVIAMENTE CARREGADOS NAS VARIÁVEIS DA CLASSE
	// para um array
	public function getDados(){
		$dados = array();
		array_push($dados, $this->getAdm());
		array_push($dados, $this->getNome());
		array_push($dados, $this->getManipulaJogos());
		array_push($dados, $this->getManipulaUsuarios());
		array_push($dados, $this->getManipulaConfiguracoes());
		array_push($dados, $this->getLiberaIndicados());
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
		$this->setAdm($res->adm);
		$this->setNome($res->nome);
		$this->setManipulaJogos($res->manipula_jogos);
		$this->setManipulaUsuarios($res->manipula_usuarios);
		$this->setManipulaConfiguracoes($res->manipula_configuracoes);
		$this->setLiberaIndicados($res->libera_indicados);
	}
//---------------------------------------------------------------------------------------------------------------
function setPermissao($usuario){
	$query = "SELECT ga.id FROM grupos_acesso ga, usuarios u WHERE (ga.id = u.grupo_acesso_id) AND (u.id = $usuario)";
	try{ $res = $this->con->uniConsulta($query); } catch(Exception $e){ return $e.message; }
	
	$this->carregaDados($res->id);
}
//---------------------------------------------------------------------------------------------------------------
	//retorna um array com as IDs dos grupos de acessos que tem um determinado acesso
	public function getGruposPorAcesso($acesso){
		$query = "SELECT id FROM grupos_acesso WHERE $acesso = 1";
		try{ $res = $this->con->multiConsulta($query); } catch(Exception $e){ return $e.message; }
		if ($res->num_rows == 0) return false;
		
		$ids = array();
		while($id = $res->fetch_object()){
			array_push($ids, $id->id);
		}
		return $ids;
	}
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------


}
?>
