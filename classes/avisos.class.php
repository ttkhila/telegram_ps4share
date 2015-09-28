<?php
//header('Content-Type: text/html; charset=UTF-8');
class avisos{
	private $id;
	private $texto;
	private $lido;
	private $con;

	public function __construct(){
		include_once 'conexao.class.php'; 
		$this->con = new conexao();
		$this->con->abreConexao();
	}
    
	public function setId($valor){$this->id = $valor;}
	public function getId(){return $this->id;}
	public function setTexto($valor){$this->texto = $valor;}	
	public function getTexto(){return $this->texto;}
	public function setLido($valor){$this->lido = $valor;}
	public function getLido(){return $this->lido;}
//---------------------------------------------------------------------------------------------------------------   
    // Descarrega os dados QUE ESTÃO PREVIAMENTE CARREGADOS NAS VARIÁVEIS DA CLASSE
    // para um array
	public function getDados(){
		$dados = array();
		array_push($dados, $this->getId());
		array_push($dados, $this->getTexto());
		array_push($dados, $this->getLido());
		return $dados;
	}
//---------------------------------------------------------------------------------------------------------------
	public function carregaDados($aviso_id){ 
		$res = $this->con->uniConsulta("SELECT * FROM avisos WHERE id = '$aviso_id'");    
		$this->setId($res->id);
		$this->setTexto($res->texto);
		$this->setLido($res->lido);
	}	
 //---------------------------------------------------------------------------------------------------------------
	public function insereAviso($para, $texto){
		if (is_array($para)){ //array - original 1 e 2 ou adms
			foreach ($para as $valor){
				if ($valor != 0) { //vaga diferente de "Vaga em aberto"
					$query = "INSERT INTO avisos (para, texto) VALUES ($valor, '$texto')";
					try{ $res = $this->con->executa($query); } catch(Exception $e) { die($e.message); }
				}
			}
		} else {
			if($para != 0) { //vaga diferente de "Vaga em aberto"
				$query = "INSERT INTO avisos (para, texto) VALUES ($para, '$texto')";
				try{ $res = $this->con->executa($query); } catch(Exception $e) { die($e.message); }
			}
		}
	}
//---------------------------------------------------------------------------------------------------------------
	public function getAvisos($para){
		$query = "SELECT * FROM avisos WHERE para = $para ORDER BY id desc";
		try{ $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function marcaLido($id){
		$query = "UPDATE avisos SET lido = 1 WHERE id = $id";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }	
	}
//---------------------------------------------------------------------------------------------------------------
	public function removeAviso($id){
		$query = "DELETE FROM avisos WHERE id = $id";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }	
	}
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------



}
?>
