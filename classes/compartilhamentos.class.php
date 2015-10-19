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
	private $compartilhamento_id;
	private $comprador_id;
	private $vendedor_id;
	private $vaga; //O1, O2, O3
	private $valor_pago;
	private $data_venda;
	private $senha_alterada;
	private $valor_venda;
	private $a_venda;
	
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
	public function setCompartilhamentoId($valor){ $this->compartilhamento_id = $valor; }
	public function getCompartilhamentoId(){ return $this->compartilhamento_id; }
	public function setVaga($valor){ $this->vaga = $valor; }
	public function getVaga(){ return $this->vaga; }
	public function setCompradorId($valor){ $this->comprador_id = $valor; }
	public function getCompradorId(){ return $this->comprador_id; }
	public function setVendedorId($valor){ $this->vendedor_id = $valor; }
	public function getVendedorId(){ return $this->vendedor_id; }
	public function setValorPago($valor){ $this->valor_pago = $valor; }
	public function getValorPago(){ return $this->valor_pago; }
	public function setDataVenda($valor){ $this->data_venda = $valor; }
	public function getDataVenda(){ return $this->data_venda; }
	public function setSenhaAlterada($valor){ $this->senha_alterada = $valor; }
	public function getSenhaAlterada(){ return $this->senha_alterada; }
	public function setValorVenda($valor){ $this->valor_venda = $valor; }
	public function getValorVenda(){ return $this->valor_venda; }
	public function setAVenda($valor){ $this->a_venda = $valor; }
	public function getAVenda(){ return $this->a_venda; }
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
	public function carregaDadosHistoricos($id, $numVaga=0){
		if($numVaga == 0 ) $query = "SELECT * FROM historicos WHERE id = $id";
		else $query = "SELECT * FROM historicos WHERE compartilhamento_id = $id AND vaga = '$numVaga' ORDER BY id";
		try{ $d = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro no carregamento."); }
		$this->setHistoricoId($d->id);
		$this->setCompartilhamentoId($d->compartilhamento_id);
		$this->setVaga($d->vaga);
		$this->setCompradorId($d->comprador_id);
		$this->setVendedorId($d->vendedor_id);
		$this->setValorPago($d->valor_pago);
		$this->setDataVenda($d->data_venda);
		$this->setSenhaAlterada($d->senha_alterada);
		$this->setValorVenda($d->valor_venda);
		$this->setAVenda($d->a_venda);
	}
//---------------------------------------------------------------------------------------------------------------
	public function carregaUltimoHistorico($compartilhamento_id, $numVaga){
		$query = "SELECT * FROM historicos WHERE compartilhamento_id = $compartilhamento_id AND vaga = '$numVaga' ORDER BY id DESC";
		try{ $d = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro no carregamento."); }
		$this->setHistoricoId($d->id);
		$this->setVaga($d->vaga);
		$this->setValorPago($d->valor_pago);
		$this->setDataVenda($d->data_venda);
		$this->setSenhaAlterada($d->senha_alterada);
		$this->setValorVenda($d->valor_venda);
		$this->setAVenda($d->a_venda);
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
	public function gravaNomeGrupo($idGrupo, $nome){
		$query = "UPDATE compartilhamentos SET nome = '$nome' WHERE id = $idGrupo";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------
	public function getDadosPorUsuario($usuarioID){
		$query = "SELECT c.* , u.nome as criador, u.login FROM compartilhamentos c, usuarios u
			WHERE (c.criador_id = u.id) AND (c.ativo = 1) AND ((original1_id =$usuarioID) OR (original2_id =$usuarioID) OR (original3_id =$usuarioID)) ORDER BY c.id DESC";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { die($e.message); }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function checaDuplicidadeGrupo($email){
		$query = "SELECT id FROM compartilhamentos WHERE email = '$email'";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { die($e.message); }
		if ($res->num_rows > 0) return false;
		
		return true;
	}
//---------------------------------------------------------------------------------------------------------------
	public function getGruposAntigos($usuarioID){
		$query = "SELECT h.id, c.nome FROM compartilhamentos c, historicos h 
			WHERE (c.id = h.compartilhamento_id) AND (h.vendedor_id = $usuarioID) 
			ORDER BY h.id DESC";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { die($e.message); }
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
		$vaga = $this->getNomeVaga($vaga);
		
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
		$vagaNome = $this->getNomeVaga($vaga);

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
	public function is_thisHistory($idHist, $idUsuario){
		$query = "SELECT h.id FROM historicos h, compartilhamentos c 
			WHERE (h.id = $idHist) AND (h.compartilhamento_id = c.id) AND 
			((h.comprador_id = $idUsuario) OR 
			((h.comprador_id = 0) AND (c.criador_id = $idUsuario) AND (c.criador_id = c.original1_id)) OR 
			((h.comprador_id = 0) AND (c.original1_id = $idUsuario) AND (c.criador_id <> c.original1_id)) OR 
			((h.comprador_id = 0) AND (c.original2_id = $idUsuario) AND (c.original1_id = 0)))";
		//echo json_encode($query);exit; 
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		if($res->num_rows == 0) //não há registro. Indício de fraude
			return false;
		
		return true;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gravaAlteracaoValorVenda($idHist, $valor){
		if (trim($valor) == "") $valor = "NULL";
		$query = "UPDATE historicos SET valor_venda = $valor WHERE id = $idHist";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------
	public function excluiVenda($idHist){
		$query = "UPDATE historicos SET a_venda = 0, valor_venda = NULL WHERE id = $idHist";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------
	public function excluiUsuarioVaga($idGrupo, $vaga, $usuarioID){
		$vagaNome = $this->getNomeVaga($vaga);
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
			$parte[1] = addslashes($parte[1]);
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
		//return "123456789";
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
				WHERE $where (jc.compartilhamento_id = c.id) AND (h.compartilhamento_id = c.id) AND (jc.jogo_id = j.id) AND (h.a_venda = 1) AND (c.ativo = 1) 
				AND (u1.id = c.original1_id)  AND (u2.id = c.original2_id) AND (u3.id = c.original3_id) AND (u4.id = c.criador_id) GROUP BY c.id ORDER BY j.nome";
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
	public function buscaGruposAdm($dados){
		$where = "";
		if($dados["fechado"] == '1') { 
			$where = $this->checaWhere($where);
			$where .= "c.fechado = 1"; 
		} 

		if(isset($dados["comprador_id"])){
			$where = $this->checaWhere($where);
			$where .= "(c.original1_id = ".$dados["comprador_id"]." OR c.original2_id = ".$dados["comprador_id"]." OR c.original3_id = ".$dados["comprador_id"].")";
		}
		//return $where;
		if(isset($dados["jogo_id"])){
			$where = $this->checaWhere($where);
			$where .= "jc.jogo_id = ".$dados["jogo_id"];
		}

		if(isset($dados["nome"])){
			$where = $this->checaWhere($where);
			$dados["nome"] = addslashes($dados["nome"]);
			$where .= "c.nome = ".$dados["nome"];
		}
		
		$where = $this->checaWhere($where);
		//return $where;
		$query = "SELECT c.id as idGrupo, c.nome as nomeGrupo, c.original1_id, c.original2_id, c.original3_id, DATE_FORMAT(c.data_compra,'%d/%m/%Y') as dataCompra, c.fechado, c.criador_id, u4.login as loginCriador, 
				h.comprador_id, h.vaga, h.data_venda, h.valor_venda, j.id as idJogo, j.nome as nomeJogo, u1.login as login1, u2.login as login2, u3.login as login3  
				FROM compartilhamentos c, historicos h, jogos_compartilhados jc, jogos j, usuarios u1, usuarios u2, usuarios u3, usuarios u4 
				WHERE $where (jc.compartilhamento_id = c.id) AND (h.compartilhamento_id = c.id) AND (jc.jogo_id = j.id) 
				AND (u1.id = c.original1_id)  AND (u2.id = c.original2_id) AND (u3.id = c.original3_id) AND (u4.id = c.criador_id) AND (c.ativo = 1) GROUP BY c.id ORDER BY j.nome";
		//return $query;
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		//return $query;
		return $res;

	}

//---------------------------------------------------------------------------------------------------------------
	public function getVendasAbertasPorUsuario($idUsuario){
		$query = "SELECT h.*, j.nome as nomeJogo
			FROM historicos h, compartilhamentos c, jogos j, jogos_compartilhados jc
			WHERE (h.a_venda = 1) AND (c.id = h.compartilhamento_id) AND (j.id = jc.jogo_id) AND (c.id = jc.compartilhamento_id) AND  (c.ativo = 1) AND 
				((h.comprador_id = $idUsuario) OR 
				((h.comprador_id = 0) AND (c.criador_id = $idUsuario) AND (c.criador_id = c.original1_id)) OR 
				((h.comprador_id = 0) AND (c.original1_id = $idUsuario) AND (c.criador_id <> c.original1_id)) OR 
				((h.comprador_id = 0) AND (c.original2_id = $idUsuario) AND (c.original1_id = 0)))
			GROUP BY h.id";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function getNomeVaga($numVaga, $tipo = 0){
		if($numVaga == 1) {
			if ($tipo == 1) $vagaNome = "Original 1";
			else $vagaNome = "original1_id";
		}
		else if ($numVaga == 2) { 
			if ($tipo == 1) $vagaNome = "Original 2";
			else $vagaNome = "original2_id"; 
		}
		else { 
			if ($tipo == 1) $vagaNome = "Fantasma";
			else $vagaNome = "original3_id"; 
		}
		
		return $vagaNome;
	}
//---------------------------------------------------------------------------------------------------------------
	// retorna todos os históricos de um determinado GRUPO ($idGrupo)
	public function getHistoricos($idGrupo){
		$query = "SELECT * FROM historicos WHERE compartilhamento_id = $idGrupo";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------	
	public function excluiHistoricos($idGrupo){
		$query = "DELETE FROM historicos WHERE compartilhamento_id = $idGrupo";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------
	public function excluiGrupo($idGrupo){
		$query = "DELETE FROM compartilhamentos WHERE id = $idGrupo";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------
	public function inativaGrupo($idGrupo){
		$query = "UPDATE compartilhamentos SET ativo = 0 WHERE id = $idGrupo";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------
	public function reativaGrupo($idGrupo){
		$query = "UPDATE compartilhamentos SET ativo = 1 WHERE id = $idGrupo";
		try{ $this->con->executa($query); } catch(Exception $e) { die($e.message); }
	}
//---------------------------------------------------------------------------------------------------------------
	public function getGruposInativos(){
		$query = "SELECT c.id as idGrupo, c.nome as nomeGrupo, c.original1_id, c.original2_id, c.original3_id, c.criador_id, 
			u4.login as loginCriador, u1.login as login1, u2.login as login2, u3.login as login3 
			FROM compartilhamentos c, usuarios u1, usuarios u2, usuarios u3, usuarios u4 
			WHERE (c.ativo = 0) AND (c.original1_id = u1.id) AND (c.original2_id = u2.id) AND (c.original3_id = u3.id) AND (c.criador_id = u4.id) ORDER BY c.nome";
		try { $res = $this->con->multiConsulta($query); } catch(Exception $e) { return $e.message; }
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
/***************************************************
 ***************  ESTATÍSTICAS *********************
 ***************************************************/
	 public function gruposTotaisUsuario($idUsuario){
		$query = "SELECT count(*) as qtd, sum(h.valor_pago) as valorTotal, sum(h.a_venda) as qtdVenda FROM historicos h, compartilhamentos c WHERE (c.ativo = 1) AND (c.id = h.compartilhamento_id) AND (h.comprador_id = $idUsuario)";
		try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação da quantidade de grupos total do usuário."); }
		
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gruposTotaisUsuarioPorVaga($idUsuario){
		for($i=1; $i<=3; $i++){
			$query = "SELECT count(*) as qtd FROM historicos h, compartilhamentos c WHERE (c.ativo = 1) AND (c.id = h.compartilhamento_id) AND (h.comprador_id = $idUsuario) AND (h.vaga = '$i')";
			try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação da quantidade de grupos total do usuário por vaga."); }
			$vaga[$i] = $res->qtd;
		}
		return $vaga;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gruposCriadosUsuario($idUsuario){
		$query = "SELECT count(*) as qtd FROM compartilhamentos WHERE (ativo = 1) AND (criador_id = $idUsuario)";
		try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação da quantidade de grupos total do usuário."); }
		
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	 public function gruposAtivos($idUsuario){
		$query = "SELECT count(*) as qtd FROM compartilhamentos WHERE (ativo = 1) AND (original1_id = $idUsuario OR original2_id = $idUsuario OR original3_id = $idUsuario)";
		try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação da quantidade de grupos ativos do usuário."); }
		
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gruposAtivosPorVaga($idUsuario){
		for($i=1; $i<=3; $i++){
			$query = "SELECT count(*) as qtd FROM compartilhamentos WHERE ativo = 1 AND original".$i."_id = $idUsuario";
			//die($query);
			try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação da quantidade de grupos ativos do usuário por vaga."); }
			$vaga[$i] = $res->qtd;
		}
		return $vaga;
	}
//---------------------------------------------------------------------------------------------------------------
	public function montanteArrecadado($idUsuario){
		$query = "SELECT sum(h.valor_pago) as valorTotal FROM historicos h, compartilhamentos c WHERE (c.ativo = 1) AND (c.id = h.compartilhamento_id) AND (vendedor_id = $idUsuario)";
		try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação da soma dos valores das contas vendidas pelo usuário."); }
		
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function montanteArrecadadoGlobal(){
		$query = "SELECT sum(h.valor_pago) as valorTotal, sum(h.a_venda) as qtdVenda FROM historicos h, compartilhamentos c WHERE (c.ativo = 1) AND (c.id = h.compartilhamento_id)";
		try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação do montante de vendas total global."); }
		
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function gruposTotaisGlobal(){
		$query = "SELECT count(*) as qtd FROM compartilhamentos WHERE ativo = 1";
		try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação do total de contas criadas - Global."); }
		
		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function totalRepassesGlobal(){
		$query = "SELECT count(*) as qtd FROM historicos h, compartilhamentos c WHERE (c.ativo = 1) AND (c.id = h.compartilhamento_id) AND (h.vendedor_id <> 0)";
		try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação da quantidade de repasses total global."); }

		return $res;
	}
//---------------------------------------------------------------------------------------------------------------
	public function totalRepassesGlobalPorVaga(){
		for($i=1; $i<=3; $i++){
			$query = "SELECT count(*) as qtd FROM historicos h, compartilhamentos c WHERE (c.ativo = 1) AND (c.id = h.compartilhamento_id) AND (vendedor_id <> 0) AND (vaga = '$i')";
			try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação da quantidade de repasses global por vaga."); }
			$vaga[$i] = $res->qtd;
		}
		return $vaga;
	}
//---------------------------------------------------------------------------------------------------------------
	public function moedaPreferida(){
		$query = "SELECT count(*) as qtd, m.* FROM compartilhamentos c, moedas m WHERE (c.moeda_id = m.id) AND (c.ativo = 1) GROUP BY (moeda_id) ORDER BY qtd desc";
		try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação de moeda preferida."); }

		return $res;
	}
//---------------------------------------------------------------------------------------------------------------	
	public function jogoPreferido(){
		$query = "SELECT count(*) as qtd, j.nome as nomeJogo, p.nome_abrev as plataforma FROM jogos_compartilhados jc, jogos j, plataformas p, compartilhamentos c 
			WHERE (c.ativo = 1) AND (c.id = jc.compartilhamento_id) AND (jc.jogo_id = j.id) AND (j.plataforma_id = p.id) 
			GROUP BY jc.jogo_id ORDER BY qtd desc";
		try{ $res = $this->con->uniConsulta($query); } catch(Exception $e) { die("Erro na solicitação de jogo mais compartilhado."); }

		return $res;
	}
	
	
}
?>
