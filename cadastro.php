<?php
	require_once 'classes/usuarios.class.php';
	if(!isset($_GET['cod'])) die ("URL Inválida!");
	
	$codigo = $_GET['cod'];
	$u = new usuarios();
	
	if(!$u->getIndicadoPorCodigo($codigo) || $codigo == "") die("Código Inválido");
	
	$dados = $u->getIndicadoPorCodigo($codigo);
	$indicado_por = $dados->indicado_por;
	$u->carregaDados($indicado_por);
	$loginIndicador = stripslashes($u->getLogin());
?>
<?php $topo = file_get_contents('topo.php'); echo $topo; //insere topo ?>
	<script type="text/javascript" src="js/lib/jquery.mask.min.js"/></script>
	<script>
		$(function(){ 
			$(".mskTel").mask("(00) 0000-00009");
		});	
	</script>
	</head>
	<body>
		<?php //$menu = file_get_contents('menu.php'); echo login($menu); //insere menu ?>
		<!-- Conteúdo Principal: Início -->
		<div class="container">
			<div class="row">
				<h2 class="page-header">Cadastro</h2>
				<div class="panel panel-primary">
					<div class="panel-heading">Preencha todos os campos abaixo</div>
					<div class="panel-body">
						<form id="frm-cadastro" role="form">
							<input type="hidden" name="hidCod" id="hidCod" value="<?php echo $codigo; ?>" />
							<div class="form-group">
								<label for="nome">Nome</label>
								<input type="text" class="form-control" name="nome" id="nome" maxlength="60" required="" value="<?php echo stripslashes($dados->nome); ?>" />
							</div>
							<div class="form-group">
								<label for="login">ID (PSN)</label>
								<input type="text" class="form-control" name="login" id="login" maxlength="16" placeholder="Digite sua ID PSN/Live" pattern="(^[\w-]{3,16})$" required="" />
							</div>
							<div class="form-group">
								<label for="email">E-mail</label><br />
								<span id='emailAT'><?php echo $dados->email; ?></span>
							</div>
							<div class="form-group">
								<label for="telefone">Celular</label>
								<input class="form-control mskTel" type="tel" name="telefone" id="telefone" pattern="\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}" required="" value="<?php echo $dados->telefone; ?>" />
								<script type="text/javascript">$("#telefone").mask("(00) 0000-00009");</script>
							</div>
							<div class="form-group">
								<label for="senha">Senha</label>
								<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" data-html="true" 
									title="Sua senha deve ter entre 6 e 10 caracteres alfanuméricos."></span>
								<input type="password" class="form-control" name="senha" id="senha" maxlength="10" pattern="(^[\w]{6,10})$" required="" placeholder="Digite uma senha " />
								<input type="password" class="form-control" name="senha2" id="senha2" maxlength="10" pattern="(^[\w]{6,10})$" required="" placeholder="Re-digite a senha" />
							</div>
							<div class="form-group">
								<input type="hidden" name="hidInd" id="hidInd" value="<?php echo $indicado_por; ?>" />
								<label>Indicado Por</label>
								<div class="lbl-indicado"><?php echo $loginIndicador; ?></div>
							</div>
							<div class="form-group">
								<label>Leia as regras do grupo abaixo para continuar</label>
								<div class="div-regras">
									<pre>
				<b>REGRAS GERAIS DO GRUPO DE COMPARTILHAMENTO PS4 E XBOX ONE NO TELEGRAM E WHATSAPP</b>
				
	<b>(1) Do cadastro ao sistema:</b>
		1.1 - Todos participantes do grupo (sem exceções) devem fornecer contato pessoal, isto é: Nome / telefone / e-mail / Psn ID e/ou Gamertag (oficial);
		1.2 Todos participantes deverão fornecer dados preenchendo o cadastro para ingresso no sistema.
		1.3 Todos participante deverão cadastrar as contas compartilhadas e alimentar sempre o sistema no caso de vendas ou trocas.
		1.4 Todas as formações de novos grupos e classificados deverão ser através do sistema, podendo negociar através de mensagem privada Telegram e Wathsapp e após a negociação concluída deverá informar o repasse no sistema.
		<b>AVISO: A utilização do sistema é obrigatória.</b>
		
	<b>(2) Dos grupos:</b>
		2.1 -   No Grupo de PARTILHAS serão permitidos somente anúncios de partilhas, vendas e trocas, lembrando que há obrigatoriedade de cadastrar nos classificados do sistema; *
		2.2 - Não são permitidos conteúdos pornográficos nos grupos desta moderação, principalmente nos grupos de PARTILHAS VENDAS e TROCAS; *
		2.3 - O Grupo PARTILHAS VENDAS E TROCAS deverá somente conter informações dos jogos em transação. Exemplo:
			<b>Para Ps4</b>
			Fifa 16 dividir 65% (original1) / 35% (original2)
			Orig1 - $40 - VAGO
			Orig2 - $20 - VAGO
			ou
			Vendo Orig1 de FIFA 16 - $40
			
			“MÉDIA” de cálculo contas compartilhadas PSN
			[quando não há fantasma (Fantasma fica sendo do original 1)]
			Original 1 65% = 40$
			Original 2 35% = 20$
			
			[Quando há fantasma]
			Original 1 50% = 30$
			Original 2 35% = 20$
			Fantasma 15% = 10$
			Cálculo independente de ser conta BR, USA ou qualquer outra Região.
			
			<b>Para Xbox One</b>
			Fifa 16 dividi-se 50% para cada.
			Primário - $30 - VAGO
			Secundário - $30 - VAGO
			ou
			Vendo primário de FIFA 16 - $30

			Obs.: As vendas podem ser valores menores do que foi pago, mas nunca maiores.
			
			Para perguntas sobre os jogos em negociação, deve-se usar o chat privado, com o objetivo de não poluir o grupo com mensagens que não sejam anúncios.
			Exemplo do que não usar no grupo PARTILHAS, VENDAS E TROCAS: “Esse jogo é bom?” “Roda na principal?”

		Obs.: As vendas podem ser valores menores do que foi pago, mas nunca maiores e todos games/contas a vendas devem estar cadastrados nos classificados do sistema.
		Para perguntas sobre os jogos em negociação, deve-se usar o chat privado, com o objetivo de não poluir o grupo com mensagens que não sejam anúncios.
		Exemplo do que não usar no grupo PARTILHAS, VENDAS E TROCAS: “Esse jogo é bom?” “Roda na principal?”
		
		2.4 - Novas indicações de usuários somente através do sistema e serão avaliadas previamente, e caso seja incluso, o indicador seráresponsável pela indicação, isto é, caso o indicado venha a descumprir alguma regra ou causar algum prejuízo, é de responsabilidade do membro que indicou ressarcir os valores para ambos os fins; (Prazo mínimo para indicação 2 meses a partir da primeira partilha no grupo)
		2.5 - É Proibido adicionar novos usuários nos grupos desta moderação;
		2.6 - É Proibido trocar ícone e nome dos grupos desta moderação; *
		2.7 - No campo NOME DE USUÁRIO do Telegram, orienta-se colocar o primeiro nome pessoal e PSN ID/Gamertag entre parênteses, segue exemplo abaixo: Ricardo (RicMatos)
		2.8 - Proibido desacatar quaisquer membros do conselho haja alguma advertência em mensagem privada através do Telegram ou Wathsapp sobre assuntos referente aos grupos. ***

	<b>(3) Das contas:</b>
		3.1 – Recomenda-se alterar a senha quando ocorrer o repasse da conta, de modo que somente os donos atuais das vagas de usuário 1 e o usuário 2 tenham acesso a mesma, desde que tenham conhecimento do outro participante da conta;  
		3.2 - Em hipótese alguma o usuário 1 e usuário 2 poderão alterar a senha se não se conhecerem; ***
		3.3 - Caso usuário 1 e/ou usuário 2 venda a conta, deve-se comunicar o outro em primeiro lugar e informar o contato do novo usuário; **
		3.4 -  Para desistência de um grupo já formado, o usuário desistente deverá se responsabilizar em substituir a vaga em que está deixando, caso não haja substituição, fica obrigado o desistente arcar com o pagamento da vaga, assim podendo negociar futuramente; ***
		3.5 - É proibida a vendas externas de contas criadas no grupo fechado, exemplo: vendas no Mercado Livre, UOL e outros meios que não seja dentro do nosso próprio grupo, ressalvo item 3.6; **
		3.6 - Vendas de contas externas serão somente aceitas caso ambas as partes usuário 1 e usuário 2 estejam de comum acordo, e caso esta conta seja vendida, ela não participará mais das regras desta moderação, mesmo que o outro participante na partilha não tenha vendido; (Banimento no caso de venda sem aviso e sem consentimento da outra parte) ***
		3.7 - É proibida a venda de contas/jogos de fora (UOL/ML e outros meios) dentro do grupo. **
		3.8 - Padronização na criação de contas dentro do grupo, Criador da conta deverá colocar iniciais do grupo "tlcw" no início do e-mail, seguido das 3 primeiras letras da sua Id na PSN ou Gamertag e 3 últimos números de seu celular, em seguida pode ser nome do jogo ou o que achar interessante.
		Exemplo: tlcw.ric115_witcher@outlook.com
		Assim saberemos pelo cadastro quem criou a conta e tem acesso ao e-mail da conta.
		NOTA. para o Caso do Xbox One, é necessário sempre incluir novos telefones de comum acordo para validação de movimentação na conta, validação via SMS.
		
	<b>(4) Do pagamento:</b>
		4.1 - Caso feche algum grupo de game já lançado, o pagante/comprador tem que receber o valor/Card em até 2 dias úteis depois da confirmação do novo usuário, salvo negociações entre usuários; **
		4.2 - Caso feche algum grupo de game pré-venda, o pagamento deverá ocorrer até 3 dias antes do lançamento, salvo negociações entre usuários; **
		4.3 - Caso não haja o pagamento, o indicado e o indicador serão responsabilizados;


	<b>(5) Das punições:</b>
		* Advertência. Em caso de reincidência, suspensão do grupo troca e vendas por 1 semana. Após isso, banimento do grupo.
		** Advertência. Em caso de reincidência, banimento do grupo.
		*** Banimento do grupo.
									</pre>
								</div>
								<br />
								<input id="chk_regras" type="checkbox"> <strong>Concordo com as regras e me comprometo a respeitá-las.</strong>
							</div>
							<p class="bg-danger" id="sp-erro-msg" style="display:none;"></p>
							<div class="form-group">
								<button class="btn btn-primary" type="submit">Enviar</button>
							</div>
						</form>
					</div>
				</div> 
			</div><!-- row -->
		</div><!-- container -->
	</body>
</html>
