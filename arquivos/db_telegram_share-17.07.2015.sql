-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: 17/07/2015 às 13h09min
-- Versão do Servidor: 5.5.41
-- Versão do PHP: 5.5.22-1+deb.sury.org~precise+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `db_telegram_share`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `compartilhamentos`
--

CREATE TABLE IF NOT EXISTS `compartilhamentos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'nome que identifique o jogo ou jogos na conta',
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `original1_id` int(10) unsigned NOT NULL DEFAULT '0',
  `original2_id` int(10) unsigned NOT NULL DEFAULT '0',
  `original3_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FANTASMA',
  `valor_compra` float DEFAULT NULL,
  `valor_compra_convertido` float DEFAULT NULL COMMENT 'Valor interno de preços em moeda estrangeira convertidos em reais para fins de estatisticas',
  `fator_conversao` float DEFAULT NULL COMMENT 'Para valores convertidos, esse campo armazena o valor de 1 unidade da moeda selecionada em reais. Ex.: 3,02 (1 dolar em real)',
  `moeda_id` tinyint(3) unsigned DEFAULT NULL,
  `data_compra` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `fechado` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Um grupo só estará fechado quando o email, os participantes e os valores pagos estiverem informados e a opção condizente estiver sido selecionada.',
  `criador_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Extraindo dados da tabela `compartilhamentos`
--

INSERT INTO `compartilhamentos` (`id`, `nome`, `email`, `original1_id`, `original2_id`, `original3_id`, `valor_compra`, `valor_compra_convertido`, `fator_conversao`, `moeda_id`, `data_compra`, `ativo`, `fechado`, `criador_id`) VALUES
(1, 'novo novo', 'e_rocha78@yahoo.com.br', 1, 3, 4, 85, 266.9, 3.14, 3, '2015-07-16', 1, 1, 1),
(2, 'teste firefox 1', 'e_roch@kdw.om', 3, 0, 1, NULL, NULL, NULL, 4, NULL, 1, 0, 3),
(3, 'teste 2 firefox', 'e_rocha78@yahoo.com.br', 3, 4, 2, 15, 15, 3.14, 1, '2015-07-16', 1, 1, 1),
(4, 'Grupo sem ttkhila', 'semttkhila@ttk.com', 4, 2, 0, NULL, NULL, NULL, 1, NULL, 1, 0, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `historicos`
--

CREATE TABLE IF NOT EXISTS `historicos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `compartilhamento_id` int(10) unsigned NOT NULL,
  `vendedor_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Quando da criação da conta, ou seja, primeiro dono, vendedor_id será 0',
  `comprador_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Comprador=0 significa que no ato da criação da conta a vaga não foi preenchida',
  `vaga` set('1','2','3') COLLATE utf8_unicode_ci NOT NULL COMMENT 'O3 = Fantasma',
  `valor_pago` float DEFAULT NULL,
  `data_venda` date DEFAULT NULL,
  `senha_alterada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Na criação da conta, o histórico é criado para as 3 vagas' AUTO_INCREMENT=13 ;

--
-- Extraindo dados da tabela `historicos`
--

INSERT INTO `historicos` (`id`, `compartilhamento_id`, `vendedor_id`, `comprador_id`, `vaga`, `valor_pago`, `data_venda`, `senha_alterada`) VALUES
(1, 1, 0, 1, '1', 40, '2015-07-16', 0),
(2, 1, 0, 3, '2', 25, '2015-07-16', 0),
(3, 1, 0, 4, '3', 20, '2015-07-16', 0),
(4, 2, 0, 3, '1', NULL, NULL, 0),
(5, 2, 0, 0, '2', NULL, NULL, 0),
(6, 2, 0, 1, '3', NULL, NULL, 0),
(7, 3, 0, 3, '1', 7, '2015-07-16', 0),
(8, 3, 0, 1, '2', 5, '2015-07-16', 0),
(9, 3, 0, 2, '3', 3, '2015-07-16', 0),
(10, 4, 0, 4, '1', NULL, NULL, 0),
(11, 4, 0, 2, '2', 12.5, NULL, 0),
(12, 3, 1, 4, '2', 10, '2015-07-17', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `jogos`
--

CREATE TABLE IF NOT EXISTS `jogos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `plataforma_id` tinyint(3) unsigned NOT NULL,
  `ativo` tinyint(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=23 ;

--
-- Extraindo dados da tabela `jogos`
--

INSERT INTO `jogos` (`id`, `nome`, `plataforma_id`, `ativo`) VALUES
(1, 'GTA V', 1, 1),
(2, 'Uncharted 4', 1, 1),
(3, 'The Last of Us', 1, 1),
(4, 'Alien: Isolation', 1, 1),
(5, 'Bloodborne', 1, 1),
(6, 'FIFA 16', 1, 1),
(7, 'Rocket League', 1, 1),
(8, 'Never Alone', 1, 1),
(9, 'Dragon Age: Inquisition', 1, 1),
(10, 'No Mans Sky', 1, 1),
(11, 'God of War III', 1, 1),
(12, 'Call of Duty: Black Ops III', 0, 1),
(13, 'God of War III', 1, 0),
(14, 'Call of Duty: Black Ops III', 1, 1),
(15, 'Call of Duty:Modern Warfare II', 2, 1),
(16, 'Call of Duty:Modern Warfare', 2, 1),
(17, 'Forza Horizon 2', 2, 1),
(18, 'Uncharted: Golden Abyss', 3, 1),
(19, 'Ninokuni', 4, 1),
(20, 'Dead Space', 4, 1),
(21, 'Dead Space II', 4, 1),
(22, 'Virtual Tennis 4', 4, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `jogos_compartilhados`
--

CREATE TABLE IF NOT EXISTS `jogos_compartilhados` (
  `compartilhamento_id` int(10) unsigned NOT NULL,
  `jogo_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`compartilhamento_id`,`jogo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Cada linha armazena um jogo compartilhado na mesma conta.';

--
-- Extraindo dados da tabela `jogos_compartilhados`
--

INSERT INTO `jogos_compartilhados` (`compartilhamento_id`, `jogo_id`) VALUES
(1, 7),
(1, 9),
(1, 11),
(2, 5),
(2, 18),
(3, 3),
(3, 10),
(3, 21),
(4, 11),
(4, 18);

-- --------------------------------------------------------

--
-- Estrutura da tabela `moedas`
--

CREATE TABLE IF NOT EXISTS `moedas` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `pais` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Extraindo dados da tabela `moedas`
--

INSERT INTO `moedas` (`id`, `nome`, `pais`) VALUES
(1, 'Real', 'BRL'),
(2, 'DÃƒÂ³lar', 'USD'),
(3, 'DÃƒÂ³lar Canadense', 'CAD'),
(4, 'Euro', 'EUR'),
(5, 'Libra Esterlina', 'GBP');

-- --------------------------------------------------------

--
-- Estrutura da tabela `plataformas`
--

CREATE TABLE IF NOT EXISTS `plataformas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nome_abrev` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Extraindo dados da tabela `plataformas`
--

INSERT INTO `plataformas` (`id`, `nome`, `nome_abrev`) VALUES
(1, 'Playstation 4', 'PS4'),
(2, 'Xbox One', 'Xone'),
(3, 'Playstation Vita', 'PS Vita'),
(4, 'Playstation 3', 'PS3');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `login` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `telefone` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `senha` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `primeiro_acesso` tinyint(1) NOT NULL DEFAULT '1',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `pontos` int(11) NOT NULL DEFAULT '0' COMMENT 'Pontuação de recomendações',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `login`, `email`, `telefone`, `senha`, `primeiro_acesso`, `ativo`, `pontos`) VALUES
(1, 'EstevÃƒÂ£o Rocha Bom', 'ttkhila', 'e_rocha78@yahoo.com.br', '6183350896', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0),
(2, 'Rodolfo Cintra', 'rozinho_1', 'rozinho@psn.com', '11999582510', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0),
(3, 'Alisson Oliveira', 'tegamertwo', 'rozinho@psn.com', '3298453652', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0),
(4, 'Ricardo Matos', 'ricMatos', 'ric@uol.com.br', '1199854010', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0),
(5, 'JoÃƒÂ£o', 'Z_Jonnys', 'jonnys@uol.com.br', '11999530058', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios_externos`
--

CREATE TABLE IF NOT EXISTS `usuarios_externos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `telefone` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
