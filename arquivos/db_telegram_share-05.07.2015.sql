-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 06-Jul-2015 às 02:35
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
  `original1_id` int(10) unsigned NOT NULL,
  `valor_original1` float NOT NULL,
  `original2_id` int(10) unsigned DEFAULT NULL,
  `valor_original2` float NOT NULL,
  `fantasma_id` int(10) unsigned DEFAULT NULL,
  `valor_fantasma` float NOT NULL,
  `valor_total` float DEFAULT NULL,
  `valor_total_convertido` float NOT NULL COMMENT 'Valor interno de preços em moeda estrangeira convertidos em reais para fins de estatisticas',
  `fator_conversao` float NOT NULL COMMENT 'Para valores convertidos, esse campo armazena o valor de 1 unidade da moeda selecionada em reais. Ex.: 3,02 (1 dolar em real)',
  `moeda_id` tinyint(3) unsigned DEFAULT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `historicos`
--

CREATE TABLE IF NOT EXISTS `historicos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `compartilhamento_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned NOT NULL,
  `vaga` set('O1','O2','F') COLLATE utf8_unicode_ci NOT NULL,
  `valor_pago` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `jogos`
--

CREATE TABLE IF NOT EXISTS `jogos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `plataforma_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Extraindo dados da tabela `jogos`
--

INSERT INTO `jogos` (`id`, `nome`, `plataforma_id`) VALUES
(1, 'GTA V', 1),
(2, 'Uncharted 4', 1),
(3, 'The Last of Us', 1),
(4, 'Alien: Isolation', 1),
(5, 'Bloodborne', 1),
(6, 'FIFA 16', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `jogos_compartilhados`
--

CREATE TABLE IF NOT EXISTS `jogos_compartilhados` (
  `compartilhamento_id` int(10) unsigned NOT NULL,
  `jogo_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`compartilhamento_id`,`jogo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Cada linha armazena um jogo compartilhado na mesma conta.';

-- --------------------------------------------------------

--
-- Estrutura da tabela `moedas`
--

CREATE TABLE IF NOT EXISTS `moedas` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `pais` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
  `psn` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
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

INSERT INTO `usuarios` (`id`, `nome`, `psn`, `email`, `telefone`, `senha`, `primeiro_acesso`, `ativo`, `pontos`) VALUES
(1, 'EstevÃƒÂ£o Rocha Bom', 'ttkhila', 'e_rocha78@yahoo.com.br', '6183350896', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0),
(2, 'Rodolfo Cintra', 'rozinho_1', 'rozinho@psn.com', '11999582510', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0),
(3, 'Alisson Oliveira', 'tegamertwo', 'rozinho@psn.com', '3298453652', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0),
(4, 'Ricardo Matos', 'ricMatos', 'ric@uol.com.br', '1199854010', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0),
(5, 'JoÃƒÂ£o', 'Z_Jonnys', 'jonnys@uol.com.br', '11999530058', '30fb914bd667fb368bb09c312bb62b8e', 0, 1, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
