<?php
	session_start();
	if(!isset($_SESSION['login']))
		header('Location: aviso.php?a=2');
	else {
		include_once 'classes/usuarios.class.php';
		$u = new usuarios();
		$adm = $u->is_adm($_SESSION['ID']);
		if(!$adm) header('Location: index.php');
	}
	$grupoID = $_GET['grupo'];
	include_once 'classes/compartilhamentos.class.php';
	include 'funcoes.php';
	$c = new compartilhamentos();
	$c->carregaDados($grupoID);
	$dados1 = $c->getDadosHistoricoInicial($grupoID);
	$dados = $c->getDadosHistorico($grupoID);
	
	$saida1 = "<div class='row'>";
	$cont = 0;
	while($d = $dados1->fetch_object()){ //dados da criação da conta
		if($cont == 0){
			$phpdate = strtotime($d->data_venda);
			$data_venda = date( 'd-m-Y', $phpdate );
			$saida1 .= "<span class='col-md-offset-1 col-md-2'>$data_venda (criação da conta)</span>";
		}
		
		if ($d->vaga == '1') $classe = 'alert alert-success';
		else if ($d->vaga == '2') $classe = 'alert alert-info';
		else $classe = 'alert alert-warning';
		
		if($d->comprador_id == 0) $saida1 .= "<span class='col-md-3 $classe'>Vaga em aberto</span>"; //vaga não foi vendida no fechamento do grupo
		else { 
			if($d->banido == 1) //usuário banido
				$saida1 .= "<span class='col-md-3 $classe'>".stripslashes($d->login)." (".stripslashes($d->nome).") <sup class='sm-ban'>*</sup></span>";
			else
				$saida1 .= "<span class='col-md-3 $classe'>".stripslashes($d->login)." (".stripslashes($d->nome).")</span>";
		}
		$cont ++;
	}
	$saida1 .= "</div><hr>";
	
	$saida = "";
	if($dados->num_rows > 0){ //a conta já foi repassada ao menos uma vez depois da criação
		while($d = $dados->fetch_object()){ //dados do histórico da conta já repassada
			//if($d->senha_alterada == 1) $img = "<center><img src='img/senha_alterada.jpg' title='alterou senha' /></center>"; else $img = "";
			if($d->banido_comprador == 1) $banido = " <sup class='sm-ban'>*</sup>"; else $banido = "";
			$phpdate = strtotime($d->data_venda);
			$data_venda = date( 'd-m-Y', $phpdate );
			$saida .= "<div class='row'>
				<span class='col-md-offset-1 col-md-2'>$data_venda</span>";
			if($d->vaga == '1') { //Original 1
				if($d->senha_alterada == 1) {
					$saida .= "<span class='col-md-3 label label-danger'><center>Senha alterada</center></span><span class='col-md-3'>&nbsp;</span><span class='col-md-3'>&nbsp;</span>
						</div><div class='row'><span class='col-md-offset-1 col-md-2'>$data_venda</span>";
				}
				$classe = 'alert alert-success';
				$saida .= "<span class='col-md-3 $classe'>".stripslashes($d->login_comprador)." (".stripslashes($d->nome_comprador).")$banido</span><span class='col-md-3'>&nbsp;</span><span class='col-md-3'>&nbsp;</span>";
			} else if($d->vaga == '2') { //Original 2
				if($d->senha_alterada == 1) {
					$saida .= "<span class='col-md-3'>&nbsp;</span><span class='col-md-3 label label-danger'><center>Senha alterada</center></span><span class='col-md-3'>&nbsp;</span>
						</div><div class='row'><span class='col-md-offset-1 col-md-2'>$data_venda</span>";
				}
				$classe = 'alert alert-info';
				$saida .= "<span class='col-md-3'>&nbsp;</span><span class='col-md-3 $classe'>".stripslashes($d->login_comprador)." (".stripslashes($d->nome_comprador).")$banido</span><span class='col-md-3'>&nbsp;</span>";
			} else if($d->vaga == '3') { //Fantasma
				if($d->senha_alterada == 1) {
					$saida .= "<span class='col-md-3'>&nbsp;</span><span class='col-md-3'>&nbsp;</span><span class='col-md-3 label label-danger'><center>Senha alterada</center></span>
						</div><div class='row'><span class='col-md-offset-1 col-md-2'>$data_venda</span>";
				}
				$classe = 'alert alert-warning';
				$saida .= "<span class='col-md-3'>&nbsp;</span><span class='col-md-3'>&nbsp;</span><span class='col-md-3 $classe'>".stripslashes($d->login_comprador)." (".stripslashes($d->nome_comprador).")$banido</span>";
			}	
			$saida .= "</div><hr>";
		}
		$saida1 .= "";
	}
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
<script>
	$(function(){ 
		
	});
</script>
</head>
<body>
	<h3 class="page-header">Histórico de conta</h3>
	<div class="panel panel-primary">
		<div class='panel-heading'><center><?php echo stripslashes($c->getNome()); ?></center></div>
		<div class='panel-body'>
			<div class='row'>
				<span class='col-md-offset-1 col-md-2 alert alert-danger'><label>Data da transação</label></span>
				<span class='col-md-3 alert alert-success'><label>Original 1</label></span>
				<span class='col-md-3 alert alert-info'><label>Original 2</label></span>
				<span class='col-md-3  alert alert-warning'><label>Fantasma</label></span>
			</div>
			<?php echo $saida1; echo $saida; ?>
		</div>
		<div class='panel-footer'>
			<sup class='sm-ban'>*</sup> Usuário Banido
		</div>
	</div>
			
</body>
</html>
