-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 02-Ago-2022 às 11:33
-- Versão do servidor: 5.7.36
-- versão do PHP: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `brapci_books`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `technical_processing`
--

DROP TABLE IF EXISTS `technical_processing`;
CREATE TABLE IF NOT EXISTS `technical_processing` (
  `id_tp` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tp_checksun` char(50) COLLATE utf8_bin NOT NULL,
  `tp_ip` char(20) COLLATE utf8_bin NOT NULL,
  `tp_isbn` char(16) COLLATE utf8_bin NOT NULL,
  `tp_up` char(100) COLLATE utf8_bin NOT NULL,
  `tp_file` char(100) COLLATE utf8_bin NOT NULL,
  `tp_user` int(11) NOT NULL,
  `tp_status` int(11) NOT NULL DEFAULT '0',
  `tp_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_tp` (`id_tp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
