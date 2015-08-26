<?php
class compartilhamentos{
	private $id;
	private $nome;
	private $email;
	private $orig1;
	private $orig2;
	private $orig3;
	private $valor;
	private $valor_convertido;
	private $fator_conversao;
	private $moeda_id;
	private $data;
	private $ativo;
	private $fechado;
	private $criador_id;
	//HISTÓRICOS
	private $historico_id;
	private $vaga; //O1, O2, O3
	private $valor_pago;
	private $data_venda;
	private $senha_alterada;
	
	private $con;
	
	public function __construct(){
		include_once 'conexao.class.php';
		$this->con = new conexao();
		$this->con->abreConexao();
	}
	
	public function setId($valor){ $this->id = $valor; }
	public function getId(){ return $this->id; }
	public function setNome($valor){ $this->nome = $valor; }
	public function getNome(){ return $this->nome; }
	public function setEmail($valor){ $this->email = $valor; }
	public function getEmail(){ return $this->email; }
	public function setOrig1($valor){ $this->orig1 = $valor; }
	public function getOrig1(){ return $this->orig1; }
	public function setOrig2($valor){ $this->orig2 = $valor; }
	public function getOrig2(){ return $this->orig2; }
	public function setOrig3($valor){ $this->orig3 = $valor; }
	public function getOrig3(){ return $this->orig3; }
	public function setValor($valor){ $this->valor = $valor; }
	public function getValor(){ return $this->valor; }
	public function setValorConvertido($valor){ $this->valor_convertido = $valor; }
	public function getValorConvertido(){ return $this->valor_convertido; }
	public function setFatorConversao($valor){ $this->fator_conversao = $valor; }
	public function getFatorConversao(){ return $this->fator_conversao; }
	public function setMoedaId($valor){ $this->moeda_id = $valor; }
	public function getMoedaId(){ return $this->moeda_id; }
	public function setData($valor){ $this->data = $valor; }
	public function getData(){ return $this->data; }
	public function setAtivo($valor){ $this->ativo = $valor; }
	public function getAtivo(){ return $this->ativo; }
	public function setFechado($valor){ $this->fechado = $valor; }
	public function getFechado(){ return $this->fechado; }
	public function setCriadorId($valor){ $this->criador_id = $valor; }
	public function getCriadorId(){ return $this->criador_id; }
	//HIstóricos
	public function setHistoricoId($valor){ $this->historico_id = $valor; }
	public function getHistoricoId(){ return $this->historico_id; }
	public function setVaga($valor){ $this->vaga = $valor; }
	public function getVaga(){ return $this->vaga; }
	public function setValorPago($valor){ $this->valor_pago = $valor; }
	public function getValorPago(){ return $this->valor_pago; }
	public function setDataVenda($valor){ $this->data_venda = $valor; }
	public function getDataVenda(){ return $this->data_venda; }
	public function setSenhaAlterada($valor){ $this->senha_alterada = $valor; }
	public function getSenhaAlterada(){ return $this->senha_alterada; }
//---------------------------------------------------------------------------------------------------------------
	public function carregaDados($id){
		$query = "SELECT * FROM compartilhamentos WHERE id = $id";
		try{ $d = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro no carregamento."); }
		$this->setId($d->id);
		$this->setEmail($d->email);
		$this->setNome($d->nome);
		$this->setOrig1($d->original1_id);
		$this->setOrig2($d->original2_id);
		$this->setOrig3($d->original3_id);
		$this->setValor($d->valor_compra);
		$this->setValorConvertido($d->valor_compra_convertido);
		$this->setFatorConversao($d->fator_conversao);
		$this->setMoedaId($d->moeda_id);
		$this->setData($d->data_compra);
		$this->setAtivo($d->ativo);
		$this->setFechado($d->fechado);
		$this->setCriadorId($d->criador_id);
	}
//---------------------------------------------------------------------------------------------------------------
	public function carregaDadosHistoricos($compartilhamento_id, $numVaga){
		$query = "SELECT * FROM historicos WHERE compartilhamento_id = $compartilhamento_id AND vaga = '$numVaga'";
		try{ $d = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro no carregamento."); }
		$this->setHistoricoId($d->id);
		$this->setVaga($d->vaga);
		$this->setValorPago($d->valor_pago);
		$this->setDataVenda($d->data_venda);
		$this->setSenhaAlterada($d->senha_alterada);
	}
//---------------------------------------------------------------------------------------------------------------
	public function getDados(){
		$dados = array("id"=>$this->getId(), "email"=>$this->getEmail(), "nome"=>$this->getNome(), "orig1"=>$this->getOrig1(), 
			"orig2"=>$this->getOrig2(), "orig3"=>$this->getOrig3(), "valor"=>$this->getValor(), "valorConvertido"=>$this->getValorConvertido(), 
			"fatorConversao"=>$this->getFatorConversao(), "moedaId"=>$this->getMoedaId(), "data"=>$this->getData(), 
			"ativo"=>$this->getAtivo(), "fechado"=>$this->getFechado(), "criadorId"=>$this->getCriadorId());
        return $dados;
	}
//---------------------------------------------------------------------------------------------------------------
	public function getDadosFechamento(){
		$dados = array("id"=>$this->getId(), "email"=>$this->getEmail(), "nome"=>$this->getNome(), "orig1"=>$this->getOrig1(), 
			"orig2"=>$this->getOrig2(), "orig3"=>$this->getOrig3(), "moedaId"=>$this->getMoedaId());
        return $dados;
	}
//---------------------------------------------------------------------------------------------------------------
	public function recupera_dados_moedas($moeda_id){
		$query = "SELECT * FROM moedas WHERE id = $moeda_id";
		try { $res = $this->con->uniConsulta($query); } catch(Exception $e) { return $e.message; }
		
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function recupera_moedas(){ 
		$query = "SELECT * FROM moedas ORDER BY nome";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function insereGrupo($campos, $valores){
		$campos = implode(",", $campos);
		$valores = implode(",",$valores);
		//return $valores;
		$query = "INSERT INTO compartilhamentos ($campos) VALUES ($valores)";
		//return $query;
		try{ $res = $this->con->executa($query, 1); } catch(Exception $e) { return $e.message; }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gravaVagas($idGrupo, $o1, $o2, $o3, $dados){
		$valor1 = !empty($dados['valor1']) ? $dados['valor1'] : "NULL";
		$valor2 = !empty($dados['valor2']) ? $dados['valor2'] : "NULL";
		$valor3 = !empty($dados['valor3']) ? $dados['valor3'] : "NULL";
		$soma = $valor1+$valor2+$valor3;
		//ORIGINAL 1
		$query = "INSERT INTO historicos (compartilhamento_id, comprador_id, vaga, valor_pago) VALUES ($idGrupo, $o1, '1', $valor1)";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
		
		//ORIGINAL 2
		$query = "INSERT INTO historicos (compartilhamento_id, comprador_id, vaga, valor_pago) VALUES ($idGrupo, $o2, '2', $valor2)";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
	
		//ORIGINAL 3 (FANTASMA)
		$query = "INSERT INTO historicos (compartilhamento_id, comprador_id, vaga, valor_pago) VALUES ($idGrupo, $o3, '3', $valor3)";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
		
		return $soma;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gravaDadosAdicionais($idGrupo, $valor, $valor_convertido, $fator_conversao, $data){
		$query = "UPDATE compartilhamentos SET valor_compra = $valor, valor_compra_convertido = $valor_convertido, fator_conversao = $fator_conversao, data_compra = '$data'  
		WHERE id = $idGrupo";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
		
		$query = "UPDATE historicos SET data_venda = '$data' WHERE compartilhamento_id = $idGrupo";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
	}
//---------------------------------------------------------------------------------------------------------------
	public function getDadosPorUsuario($usuarioID){
		$query = "SELECT c.* , u.nome as criador, u.login FROM compartilhamentos c, usuarios u
			WHERE (c.criador_id = u.id) AND ((original1_id =$usuarioID) OR (original2_id =$usuarioID) OR (original3_id =$usuarioID)) ORDER BY c.id DESC";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function getDadosHistoricoInicial($idGrupo){
		//Dados históricos da criação do grupo
		$query = "SELECT h.*, u.nome, u.login FROM historicos h, usuarios u 
			WHERE (h.comprador_id = u.id) AND (h.vendedor_id = 0) AND (h.compartilhamento_id = $idGrupo) ORDER BY h.id";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function getDadosHistorico($idGrupo){
		$query = "SELECT h.*, u1.nome as nome_comprador, u1.login as login_comprador, u2.nome as nome_vendedor, u2.login as login_vendedor FROM historicos h, usuarios u1, usuarios u2 
			WHERE (h.comprador_id = u1.id) AND (h.vendedor_id = u2.id) AND (h.compartilhamento_id = $idGrupo) AND (h.vendedor_id <> 0) ORDER BY h.id";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	//verifica se o Usuario eh dono da vaga informada. Seguranca contra fraude
	public function is_thisGroup($idUsuario, $idGrupo, $vaga){
		if($vaga == 1) $vaga = "original1_id";
		else if ($vaga == 2)  $vaga = "original2_id";
		else $vaga = "original3_id";
		
		//verifica se o jogo está sendo repassado a partir de uma conta ABERTA (usuario = 0).
		//Nesse caso não acusa problema
		$query = "SELECT $vaga as dono FROM compartilhamentos WHERE id = $idGrupo";
		try { $res = $this->con->uniConsulta($query); } catch(Exception $e) { return $e.message; }
		$dono = $res->dono;
		if ($dono == 0)
			return TRUE;
		
		$query = "SELECT * from compartilhamentos WHERE id = $idGrupo AND $vaga = $idUsuario";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		if($res->num_rows == 0)
			return FALSE; //fraude. Valor alterado via HTML
		else
			return TRUE;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gravaRepasse($grupoID, $vendedorID, $compradorID, $vaga, $valor_pago, $data, $senha_alterada = 0){
		if($vaga == 1) $vagaNome = "original1_id";
		else if ($vaga == 2)  $vagaNome = "original2_id";
		else $vagaNome = "original3_id";

		//verifica se o jogo está sendo repassado a partir de uma conta ABERTA (usuario = 0).
		//NEsse caso não cria registro de HISTÓRICO, somente altera o existente
		$query = "SELECT $vagaNome as dono FROM compartilhamentos WHERE id = $grupoID";
		try { $res = $this->con->uniConsulta($query); } catch(Exception $e) { return $e.message; }
		$dono = $res->dono;
		
		//grava alteração no registro de compartilhamento inserindo novo dono na vaga correspondente
		$query = "UPDATE compartilhamentos SET $vagaNome = $compradorID WHERE id = $grupoID";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
		
		if ($dono == 0){
			$query = "UPDATE historicos SET comprador_id = $compradorID, valor_pago = $valor_pago, data_venda = '$data', senha_alterada = $senha_alterada, a_venda = 0 
			WHERE id in (
			      SELECT * FROM (
				     SELECT id 
				     FROM historicos 
				     WHERE compartilhamento_id = $grupoID AND vaga = '$vaga'
				     ORDER BY id DESC limit 0, 1
			      ) 
			      as t);";
			try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
			return TRUE;
		}	
		//grava alteração no registro de compartilhamento inserindo novo dono na vaga correspondente
		//$query = "UPDATE compartilhamentos SET $vagaNome = $compradorID WHERE id = $grupoID";
		//try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
		
		//coloca registros anteriores de mesmo grupo e mesma vaga com valor de venda = 0;
		$query = "UPDATE historicos SET a_venda = 0 WHERE compartilhamento_id = $grupoID AND vaga = '$vaga'";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
		
		//insere novo Histórico
		$query = "INSERT INTO historicos (compartilhamento_id, vendedor_id, comprador_id, vaga, valor_pago, data_venda, senha_alterada) VALUES ($grupoID, $vendedorID, $compradorID, '$vaga', $valor_pago, '$data', $senha_alterada)";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
		
		return TRUE;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gravaDisponibilidadeVaga($idGrupo, $vaga, $valor, $usuarioID){
		if (trim($valor) == "") $valor = "NULL";
 		$query = "UPDATE historicos SET a_venda = 1, valor_venda = $valor 
			WHERE id in (
			      SELECT * FROM (
				     SELECT id 
				     FROM historicos 
				     WHERE (compartilhamento_id = $idGrupo) AND (vaga = '$vaga') AND ((comprador_id = $usuarioID) OR (comprador_id = 0))
				     ORDER BY id DESC limit 0, 1
			      ) 
			      as t);";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
			
	}
//---------------------------------------------------------------------------------------------------------------
	public function excluiUsuarioVaga($idGrupo, $vaga, $usuarioID){
		if($vaga == 1) $vagaNome = "original1_id";
		else if ($vaga == 2)  $vagaNome = "original2_id";
		else $vagaNome = "original3_id";
		
		// Atualiza grupo
		$query = "UPDATE compartilhamentos SET $vagaNome = 0 WHERE id = $idGrupo";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
		
		// Atualiza histórico
		$query = "UPDATE historicos SET comprador_id = 0, valor_pago = NULL, data_venda = NULL, a_venda = 0, valor_venda = NULL 
			WHERE id in (
			      SELECT * FROM (
				     SELECT id 
				     FROM historicos 
				     WHERE (compartilhamento_id = $idGrupo) AND (vaga = '$vaga') AND (comprador_id = $usuarioID) 
				     ORDER BY id DESC limit 0, 1
			      ) 
			      as t);";
		try{ $this->con->executa($query); } catch(Exception $e) { return $e.message; }
		
		return 1;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gravaGrupoFechamento($id, $dados){
		foreach ($dados as $value) {
			$parte = explode("%=%", $value);
			$parte[1] = addslashes(utf8_encode($parte[1]));
			$query = "UPDATE compartilhamentos SET $parte[0] = '$parte[1]' WHERE id = $id";
			try{ $this->con->executa($query); } catch(Exception $e) { die("Erro na solicitação com banco de dados."); }
		}
		//grava FECHADO = 1
		$query = "UPDATE compartilhamentos SET fechado = 1 WHERE id = $id";
		try{ $this->con->executa($query); } catch(Exception $e) { die("Erro na solicitação com banco de dados."); }
	}
//---------------------------------------------------------------------------------------
	public function gravaHistoricoFechamento($id, $dados){
		$valorTotal = 0;
		foreach ($dados as $value) {
			$parte = explode("%=%", $value);
			if(strstr($parte[0], "valor")){
				$vaga = substr($parte[0], -1, 1);
				$query = "UPDATE historicos SET valor_pago = $parte[1] WHERE compartilhamento_id = $id AND vaga = '$vaga'";
				$valorTotal += $parte[1];
			} else if (strstr($parte[0], "senha"))
				$query = "UPDATE historicos SET $parte[0] = $parte[1] WHERE compartilhamento_id = $id";

			try{ $this->con->executa($query); } catch(Exception $e) { die("Erro na solicitação com banco de dados."); }
		}

		return $valorTotal;
	}
//---------------------------------------------------------------------------------------
	public function converteMoeda($PaisMoeda){
		$url = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22".$PaisMoeda."BRL%22)&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
		$xml = simplexml_load_file($url);
		$fator = number_format(floatval($xml->results->rate->Rate), 2);
		return $fator;
	}
//---------------------------------------------------------------------------------------------------------------
	public function buscaVagasClassificados($dados, $tipoValor){
		//return "a-> ".$dados["valor1"];
		if(isset($dados["valor1"])){
			if($tipoValor == 1) $where = "h.valor_venda >= ".floatval($dados['valor1'])." AND h.valor_venda <= ".floatval($dados['valor2']);
			else if($tipoValor == 2) $where = "h.valor_venda > ".floatval($dados['valor1']);
			else $where = "h.valor_venda < ".floatval($dados['valor1']);
		} else $where = "";
		
		if($dados["fechado"] == '1') { 
			$where = $this->checaWhere($where);
			$where .= "c.fechado = 1"; 
		} 
		if(isset($dados["comprador_id"])){
			$where = $this->checaWhere($where);
			$where .= "h.comprador_id = ".$dados["comprador_id"];
		}
		//return $where;
		if(isset($dados["jogo_id"])){
			$where = $this->checaWhere($where);
			$where .= "jc.jogo_id = ".$dados["jogo_id"];
		}
		
		if(isset($dados["vaga"])){
			$vagas = explode("-", $dados["vaga"]);
			$whereVaga = "("; //essa variavel faz o 'OR', se for preciso, dentro das vagas. Ex.: Orig1 OR orig2
			$where = $this->checaWhere($where);
			array_pop($vagas); //elimina último elemento do array, que está vazio
			foreach ($vagas as $vaga){
				if ($whereVaga != "(")
					$whereVaga .= " OR ";
				$whereVaga .= "h.vaga = '$vaga'";
			}
			$whereVaga .= ")";
			$where .= $whereVaga; //concatena a query original com a query das vagas
		}
		
		$where = $this->checaWhere($where);
		//return $where;
		$query = "SELECT c.id as idGrupo, c.original1_id, c.original2_id, c.original3_id, DATE_FORMAT(c.data_compra,'%d/%m/%Y') as dataCompra, c.fechado, c.criador_id, u4.login as loginCriador, 
				h.comprador_id, h.vaga, h.data_venda, h.valor_venda, j.id as idJogo, j.nome as nomeJogo, u1.login as login1, u2.login as login2, u3.login as login3  
				FROM compartilhamentos c, historicos h, jogos_compartilhados jc, jogos j, usuarios u1, usuarios u2, usuarios u3, usuarios u4 
				WHERE $where (jc.compartilhamento_id = c.id) AND (h.compartilhamento_id = c.id) AND (jc.jogo_id = j.id) AND (h.a_venda = 1) 
				AND (u1.id = c.original1_id)  AND (u2.id = c.original2_id) AND (u3.id = c.original3_id) AND (u4.id = c.criador_id) GROUP BY c.id";
		//return $query;
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		//return $query;
		return $res;

	}
//---------------------------------------------------------------------------------------------------------------
	private function checaWhere($where){
		if ($where != "")
			$where .= " AND ";
		
		return $where;
	}
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------
}
?>
