<?php
class jogos{
	private $id;
	private $nome;
	private $plataforma_id;
	private $con;
	
	public function __construct(){
		include_once 'conexao.class.php';
		$this->con = new conexao();
		$this->con->abreConexao();
	}
	
	public function setId($valor){ $this->id = $valor; }
	public function getId(){ return $this->id; }
	public function setNome($nome){$this->nome = $nome;}	
	public function getNome(){return $this->nome;}
	public function setPlataformaId($valor){ $this->plataforma_id = $valor; }
	public function getPlataformaId(){ return $this->plataforma_id; }
//---------------------------------------------------------------------------------------------------------------
	public function carregaDados($jogo_id){ 
        $res = $this->con->uniConsulta("SELECT * FROM jogos WHERE id = '$jogo_id'");    
        $this->setId($res->id);
        $this->setNome($res->nome);
        $this->setPlataformaId($res->plataforma_id); 
    }	
//---------------------------------------------------------------------------------------------------------------
	public function getAutocomplete($q, $flag){
		$q = $this->con->escape($q);
		if ($flag == 1)	
			$sql = "SELECT j.*, p.nome_abrev as plataforma FROM jogos j, plataformas p 
				where (j.plataforma_id = p.id) AND locate('$q', j.nome) > 0 order by locate('$q', j.nome) limit 10";
		else
			$sql = "SELECT j.*, p.nome_abrev as plataforma FROM jogos j, plataformas p 
				where (j.plataforma_id = p.id) AND locate('$q', j.nome) > 0 AND (j.ativo = 1) order by locate('$q', j.nome) limit 10";
		return $res = $this->con->multiConsulta( $sql );
	}
//---------------------------------------------------------------------------------------------------------------
	public function getJogos(){
		$query = "SELECT j.*, p.nome_abrev as plataforma FROM jogos j, plataformas p 
			WHERE (p.id = j.plataforma_id) AND (j.ativo = 1) ORDER BY j.nome";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	//jogos para listagem de grupos
	public function getJogosGrupo($idGrupo){ 
		//$arr = devolve jogos em formato de array.

		$query = "SELECT j.id as idJogo, j.nome as jogo, p.nome as plataforma, p.nome_abrev FROM jogos j, jogos_compartilhados jc, plataformas p  
			WHERE (j.id = jc.jogo_id) AND (p.id = j.plataforma_id) AND (jc.compartilhamento_id = $idGrupo)";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		/*
		if($arr != 0){
			$jogos = array();
			$cont=1;
			while($j = $res->fetch_object()){
				$jogos["jogo".$cont] = $j->idJogo;
				$cont++;
			}
			return $jogos;
		}
		*/
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gravaJogo($dados){
		//$dados = Ordem dos valores (NOME, PLATAFORMA)
		$nome = addslashes(utf8_encode($dados[0]));
		$plataforma = intval($dados[1]);
		$query = "INSERT INTO jogos (nome, plataforma_id) VALUES ('$nome', $plataforma)";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
	}
//---------------------------------------------------------------------------------------------------------------
	public function alteraJogo($dados){
		//$dados = Ordem dos valores (ID, NOME, PLATAFORMA)
		$id = intval($dados[0]);
		$nome = addslashes(utf8_encode($dados[1]));
		$plataforma = intval($dados[2]);
		$query = "UPDATE jogos SET nome = '$nome', plataforma_id = $plataforma WHERE id = $id";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
	}
//---------------------------------------------------------------------------------------------------------------
	public function gravaJogosCompartilhados($idGrupo, $dados){
		foreach($dados as $key => $value){
			//return $value;
			if(strstr($key, "jogo")){ // é ID de jogo
				if(!empty($value) && isset($value)){
					$query = "INSERT INTO jogos_compartilhados (compartilhamento_id, jogo_id) VALUES ($idGrupo, $value)";
					//return $query; 
					try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
				}
			}
		}
		return 1;
	}
//---------------------------------------------------------------------------------------------------------------  
	public function ativo_inativo_alterna($f){
		$query = "UPDATE jogos SET ativo = $f WHERE id = ".$this->getId();
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
		
		if($f == 1) return "Registro de jogo ATIVADO!";
		else return "Registro de jogo DESATIVADO!";
	}
//---------------------------------------------------------------------------------------------------------------
	public function getPlataformas(){
		$query = "SELECT * FROM plataformas ORDER BY nome";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		
		return $res;
	}
}
?>
