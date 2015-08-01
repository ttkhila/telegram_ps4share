-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 21-Jul-2015 às 23:22
-- Versão do servidor: 5.6.15-log
-- PHP Version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_telegram_share`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Extraindo dados da tabela `compartilhamentos`
--

INSERT INTO `compartilhamentos` (`id`, `nome`, `email`, `original1_id`, `original2_id`, `original3_id`, `valor_compra`, `valor_compra_convertido`, `fator_conversao`, `moeda_id`, `data_compra`, `ativo`, `fechado`, `criador_id`) VALUES
(1, 'novo novo', 'e_rocha78@yahoo.com.br', 1, 3, 4, 85, 266.9, 3.14, 3, '2015-07-16', 1, 1, 1),
(2, 'teste firefox 1', 'e_roch@kdw.om', 3, 0, 1, NULL, NULL, NULL, 4, NULL, 1, 0, 3),
(3, 'teste 2 firefox', 'e_rocha78@yahoo.com.br', 3, 4, 2, 15, 15, 3.14, 1, '2015-07-16', 1, 1, 1),
(4, 'Grupo sem ttkhila', 'semttkhila@ttk.com', 4, 2, 0, NULL, NULL, NULL, 1, NULL, 1, 0, 2),
(6, 'Grupo diverso', '', 1, 3, 0, NULL, NULL, NULL, 2, NULL, 1, 0, 1),
(7, 'Agora eu tenho grupo (JoÃƒÂ£o)', 'joao@caminhao.com', 1, 4, 5, 140.05, 140.05, 3.14, 1, '2015-07-21', 1, 1, 5),
(8, 'eqwewqeqweqw', '', 0, 1, 6, NULL, NULL, NULL, 1, NULL, 1, 0, 6),
(9, 'Novo grupo muito foda', 'e_rocha78@yahoo.com.br', 3, 1, 4, 20, 60, 3.47, 4, '2015-07-21', 1, 1, 1),
(10, 'mais um teste', 'e_rocha78@yahoo.com.br', 1, 7, 3, 60, 146.75, 2.45, 3, '2015-07-21', 1, 1, 1),
(11, 'Jogos em euros', 'e_rocha78@yahoo.com.br', 5, 3, 7, 117.45, 407.01, 3.47, 4, '2015-07-21', 1, 1, 3),
(12, 'libra', 'e_rocha78@yahoo.com.br', 3, 0, 0, 44, 216.86, 4.93, 5, '2015-07-21', 1, 1, 3),
(13, 'testeÃ¢Â‚Â¬libraÃ‚Â£', '', 3, 0, 0, NULL, NULL, NULL, 2, NULL, 1, 0, 3),
(14, 'Teste definitivo de Libras', 'e_rocha78@yahoo.com.br', 5, 3, 1, 56.48, 278.68, 4.93, 5, '2015-07-21', 1, 1, 1),
(15, 'dwqdqwdqwdwq', 'e_rocha78@yahoo.com.br', 1, 2, 5, 7, 7, 1, 1, '2015-07-21', 1, 1, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Na criação da conta, o histórico é criado para as 3 vagas' AUTO_INCREMENT=43 ;

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
(12, 3, 1, 4, '2', 10, '2015-07-17', 1),
(13, 6, 0, 1, '1', 40, NULL, 0),
(14, 6, 0, 3, '2', 20, NULL, 0),
(15, 6, 0, 0, '3', NULL, NULL, 0),
(16, 7, 0, 1, '1', 80.5, '2015-07-21', 0),
(17, 7, 0, 4, '2', 37.45, '2015-07-21', 0),
(18, 7, 0, 5, '3', 22.1, '2015-07-21', 0),
(19, 8, 0, 0, '1', 3432, NULL, 0),
(20, 8, 0, 1, '2', NULL, NULL, 0),
(21, 8, 0, 6, '3', NULL, NULL, 0),
(22, 9, 0, 3, '1', 10, '2015-07-21', 0),
(23, 9, 0, 1, '2', 5, '2015-07-21', 0),
(24, 9, 0, 4, '3', 5, '2015-07-21', 0),
(25, 10, 0, 1, '1', 30, '2015-07-21', 0),
(26, 10, 0, 7, '2', 20, '2015-07-21', 0),
(27, 10, 0, 3, '3', 10, '2015-07-21', 0),
(28, 11, 0, 5, '1', 55, '2015-07-21', 0),
(29, 11, 0, 3, '2', 38, '2015-07-21', 0),
(30, 11, 0, 7, '3', 24.45, '2015-07-21', 0),
(31, 12, 0, 3, '1', 44, '2015-07-21', 0),
(32, 12, 0, 0, '2', NULL, '2015-07-21', 0),
(33, 12, 0, 0, '3', NULL, '2015-07-21', 0),
(34, 13, 0, 3, '1', NULL, NULL, 0),
(35, 13, 0, 0, '2', NULL, NULL, 0),
(36, 13, 0, 0, '3', NULL, NULL, 0),
(37, 14, 0, 5, '1', 27.25, '2015-07-21', 0),
(38, 14, 0, 3, '2', 21.03, '2015-07-21', 0),
(39, 14, 0, 1, '3', 8.2, '2015-07-21', 0),
(40, 15, 0, 1, '1', 1, '2015-07-21', 0),
(41, 15, 0, 2, '2', 3, '2015-07-21', 0),
(42, 15, 0, 5, '3', 3, '2015-07-21', 0);

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
(4, 18),
(6, 2),
(6, 4),
(6, 5),
(6, 10),
(6, 15),
(7, 3),
(7, 6),
(7, 22),
(8, 8),
(9, 2),
(10, 19),
(10, 22),
(11, 2),
(11, 5),
(11, 8),
(11, 9),
(11, 19),
(11, 22),
(12, 21),
(13, 7),
(14, 1),
(14, 5),
(14, 8),
(14, 9),
(14, 10),
(14, 17),
(15, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `moedas`
--

CREATE TABLE IF NOT EXISTS `moedas` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `pais` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `simbolo` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Extraindo dados da tabela `moedas`
--

INSERT INTO `moedas` (`id`, `nome`, `pais`, `simbolo`) VALUES
(1, 'Real', 'BRL', 'R$'),
(2, 'DÃƒÂ³lar', 'USD', '$'),
(3, 'DÃƒÂ³lar Canadense', 'CAD', 'C$'),
(4, 'Euro', 'EUR', 'â‚¬'),
(5, 'Libra Esterlina', 'GBP', 'Â£');

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
  `login` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `telefone` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `senha` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `primeiro_acesso` tinyint(1) NOT NULL DEFAULT '1',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `pontos` int(11) NOT NULL DEFAULT '0' COMMENT 'Pontuação de recomendações',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `login`, `email`, `telefone`, `senha`, `primeiro_acesso`, `ativo`, `pontos`) VALUES
(1, 'EstevÃƒÂ£o Rocha Bom', 'ttkhila', 'e_rocha78@yahoo.com.br', '6183350896', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0),
(2, 'Rodolfo Cintra', 'rozinho_1', 'rozinho@psn.com', '11999582510', '89794b621a313bb59eed0d9f0f4e8205', 0, 1, 0),
(3, 'Alisson Oliveira', 'TeGamerTwo', 'rozinho@psn.com', '3298453652', '89794b621a313bb59eed0d9f0f4e8205', 0, 1, 0),
(4, 'Ricardo Matos', 'RicMatosBR', 'ric@uol.com.br', '1199854010', '89794b621a313bb59eed0d9f0f4e8205', 0, 1, 0),
(5, 'JoÃƒÂ£o', 'R_Jonnys', 'jonnys@uol.com.br', '11999530058', '89794b621a313bb59eed0d9f0f4e8205', 0, 1, 0),
(6, 'Glauber', 'AirKingBR', 'airking@br.com', '', '89794b621a313bb59eed0d9f0f4e8205', 0, 1, 0),
(7, 'Flavio', 'Mrgutierres82', '', '', '89794b621a313bb59eed0d9f0f4e8205', 0, 1, 0),
(8, 'Rafael Avolio', 'rafael_avolio', 'e_rocha78@yahoo.com.br', '', '89794b621a313bb59eed0d9f0f4e8205', 0, 1, 0),
(9, 'Roni', 'Roniross', '', '', '89794b621a313bb59eed0d9f0f4e8205', 0, 1, 0),
(10, 'Fernando Salvador', 'gargula1981', '', '', '89794b621a313bb59eed0d9f0f4e8205', 0, 1, 0),
(11, 'Willian', 'bgood_inn', '', '', '89794b621a313bb59eed0d9f0f4e8205', 0, 1, 0);

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
