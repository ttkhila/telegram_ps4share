<?php
//header('Content-Type: text/html; charset=UTF-8');
class alertas{
	private $id;
	private $usuario_id;
	private $texto;
	private $data_alerta;
	private $autor_id;

	public function __construct(){
		include_once 'conexao.class.php'; 
		$this->con = new conexao();
		$this->con->abreConexao();
	}
    
	public function setId($valor){$this->id = $valor;}
	public function getId(){return $this->id;}
	public function setUsuarioId($valor){$this->usuario_id = $valor;}
	public function getUsuarioId(){return $this->usuario_id;}
	public function setTexto($valor){$this->texto = $valor;}	
	public function getTexto(){return $this->texto;}	
	public function setDataAlerta($valor){$this->data_alerta = $valor;}	
	public function getDataAlerta(){return $this->data_alerta;}
	public function setAutorId($valor){$this->autor_id = $valor;}
	public function getAutorId(){return $this->autor_id;}
//---------------------------------------------------------------------------------------------------------------   
    // Descarrega os dados QUE ESTÃO PREVIAMENTE CARREGADOS NAS VARIÁVEIS DA CLASSE
    // para um array
	public function getDados(){
		$dados = array();
		array_push($dados, $this->getId());
		array_push($dados, $this->getUsuarioId());
		array_push($dados, $this->getTexto());
		array_push($dados, $this->getDataAlerta());
		array_push($dados, $this->getAutorId());
		return $dados;
	}
//---------------------------------------------------------------------------------------------------------------
	public function carregaDados($alerta_id){ 
		$res = $this->con->uniConsulta("SELECT * FROM alertas WHERE id = '$alerta_id'");    
		$this->setId($res->id);
		$this->setUsuarioId($res->usuario_id);
		$this->setTexto($res->texto);
		$this->setDataAlerta($res->data_alerta);
		$this->setAutorId($res->autor_id);
	}	
 //---------------------------------------------------------------------------------------------------------------
	public function insereAlerta($usuarioID, $texto, $dt, $autorID){
		$query = "INSERT INTO alertas (usuario_id, texto, data_alerta, autor_id) VALUES ($usuarioID, '$texto', '$dt', $autorID)";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------
	public function getAlertas(){
		$query = "SELECT count(*) as qtd, a.usuario_id, u.login, u.nome FROM alertas a, usuarios u  
			WHERE (a.usuario_id = u.id) GROUP BY a.usuario_id ORDER BY qtd DESC, u.login";
		try{ $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function getAlertasUsuario($usuarioID){
		$query = "SELECT a.texto, a.usuario_id, DATE_FORMAT(a.data_alerta,'%d/%m/%Y') as dataAlerta, u.login FROM alertas a, usuarios u 
			WHERE (usuario_id = $usuarioID) AND (a.autor_id = u.id) ORDER BY data_alerta DESC";
		try{ $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function getQtdAlerta($usuarioID){
		$query = "SELECT count(*) as qtd FROM alertas WHERE usuario_id = $usuarioID";
		try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { return $e.message; }
		return $res->qtd;
	}
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------



}
?>

