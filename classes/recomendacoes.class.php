<?php
header('Content-Type: text/html; charset=UTF-8');
class recomendacoes{
	private $id;
	private $historico_id;
	private $comprador_id;
	private $vendedor_id;
	private $texto;
	private $efetuada;
	private $con;

	public function __construct(){
		include_once 'conexao.class.php'; 
		$this->con = new conexao();
		$this->con->abreConexao();
	}
    
	public function setId($valor){$this->id = $valor;}
	public function getId(){return $this->id;}
	public function setHistoricoId($valor){$this->historico_id= $valor;}	
	public function getHistoricoId(){return $this->historico_id;}
	public function setCompradorId($valor){ $this->comprador_id = $valor; }
	public function getCompradorId(){ return $this->comprador_id; }
	public function setVendedorId($valor){ $this->vendedor_id = $valor; }
	public function getVendedorId(){ return $this->vendedor_id; }
	public function setTexto($valor){$this->texto = $valor;}
	public function getTexto(){return $this->texto;}
	public function setEfetuada($valor){$this->efetuada = $valor;}
	public function getEfetuada(){return $this->efetuada;}
//---------------------------------------------------------------------------------------------------------------   
    // Descarrega os dados QUE ESTÃO PREVIAMENTE CARREGADOS NAS VARIÁVEIS DA CLASSE
    // para um array
	public function getDados(){
		$dados = array();
		array_push($dados, $this->getId());
		array_push($dados, $this->getHistoricoId());
		array_push($dados, $this->getCompradorId());
		array_push($dados, $this->getVendedorId());
		array_push($dados, $this->getTexto());
		array_push($dados, $this->getEfetuada());
		return $dados;
	}
//---------------------------------------------------------------------------------------------------------------
    public function carregaDados($id){ 
        $res = $this->con->uniConsulta("SELECT * FROM recomendacoes WHERE id = '$id'");    
        $this->setId($res->id);
        $this->setHistoricoId($res->historico_id);
        $this->setCompradorId($res->comprador_id);
        $this->setVendedorId($res->vendedor_id); 
        $this->setTexto($res->texto); 
        $this->setEfetuada($res->efetuada);  
    }	
//---------------------------------------------------------------------------------------------------------------
	public function abreRecomendacao($historicoID, $compradorID, $vendedorID){
		$query = "INSERT INTO recomendacoes (historico_id, comprador_id, vendedor_id) VALUES ($historicoID, $compradorID, $vendedorID)";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
	
	
}
?>
