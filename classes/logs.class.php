<?php
header('Content-Type: text/html; charset=UTF-8');
class logs{
    private $id;
	private $usuario_id;
	private $usuario_login;
	private $data_hora;
	private $acao;
	private $con;

	public function __construct(){
		include_once 'conexao.class.php';
		$this->con = new conexao();
		$this->con->abreConexao();
	}
    
    private function setId($valor){ $this->id = $valor; }
    public function getId(){ return $this->id; }
    
    private function setUsuarioId($valor){ $this->usuario_id = $valor; }
    public function getUsuarioId(){ return $this->usuario_id; }
    
    private function setUsuarioLogin($valor){ $this->usuario_login = $valor; }
    public function getUsuarioLogin(){ return $this->usuario_login; }
    
	private function setDataHora($valor){ $this->data_hora = $valor; }
    public function getDataHora(){ return $this->data_hora; }
    
    private function setAcao($valor){ $this->acao = $valor; }
    public function getAcao(){ return $this->acao; }
    
//---------------------------------------------------------------------------------------------------------------
    public function carregaDados($log_id){ 
        $res = $this->con->uniConsulta("SELECT * FROM logs WHERE id = '$log_id'");
        
        $this->setId($res->jogo_id);
        $this->setUsuarioId($res->usuario_id);
        $this->setUsuarioLogin($res->usuario_login);
        $this->setDataHora($res->data_hora);
        $this->setAcao($res->acao);
    }
//---------------------------------------------------------------------------------------------------------------   
	public function dateTimeOnline(){
		setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
		date_default_timezone_set('America/Sao_Paulo'	);
		return date(DATE_RFC822);
	}
//---------------------------------------------------------------------------------------------------------------  
	public function insereLog($dados){
		$query = "INSERT INTO logs (usuario_id, usuario_login, data_hora, acao) VALUES (".$dados[0].",'".$dados[1]."', '".$dados[2]."', '".$dados[3]."')";
		try{ $this->con->executa($query); } catch(Exception $e){ return $e.message; }
	}
//---------------------------------------------------------------------------------------------------------------  	
	public function getAllLogs($tipo, $valor){
		$valor = trim($valor);
		switch($tipo){
			case 0:
				$query = "SELECT * FROM logs ORDER BY log_id desc";
				break;
			case 1:
				$query = "SELECT * FROM logs WHERE usuario_login = '$valor' ORDER BY log_id desc";
				break;
			case 2:
				$query = "SELECT * FROM logs ORDER BY log_id desc LIMIT 0, $valor";
				break;
			default:
				break;
		}	
		try{ $ret = $this->con->multiConsulta($query); } catch(Exception $e){ return $e.message; }
		
		return $ret;
	}
//---------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------------
}
?>
