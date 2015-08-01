<?php
class conexao{
	private $local;
	private $user;
	private $pass;
	private $db;
	private $con;
	private $res;
	
	public function __construct() {
		$this->local = 'localhost';
		$this->user = 'conselho';
		$this->pass = '2e4asu8yd';
		$this->db = 'ps4share_partilhas';
	}
	
	public function abreConexao(){
		$this->con = mysqli_connect($this->local, $this->user, $this->pass, $this->db) or die(mysqli_connect_error());
	}

	// para Selects com apenas 1 linha de resultado
	public function uniConsulta($sql){
		$sql = $this->con->query($sql) or die($this->con->error);
		$this->res = $sql->fetch_object();

		return $this->res;
	}

	// para Selects com mais de 1 linha de resultado
	public function multiConsulta($sql){
		$this->res = $this->con->query($sql) or die($this->con->error);
		return $this->res;
	}
	
	// para Deletes ou Updates
	//para receber de volta algum resultado, tipo o ID de um INSERT, passar o valor 1 para a fun��o
	public function executa($sql, $ret = 0){ 
		//$sql = addslashes($sql);
		$this->con->query($sql) or die($this->con->error);	

		if ($ret == 1)
			return $this->con->insert_id;
	}
	
	public function escape($string){
		return $this->con->real_escape_string($string);
	}
	
	// método destrutor. Fecha a conexão com o BD
	function __destruct() {
		$this->con->close();
	}

}
?>
