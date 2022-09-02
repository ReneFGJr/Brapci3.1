-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 02-Set-2022 às 20:28
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
-- Banco de dados: `brapci_chatbot`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `vc_link`
--

DROP TABLE IF EXISTS `vc_link`;
CREATE TABLE IF NOT EXISTS `vc_link` (
  `id_lk` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lk_word_0` int(11) DEFAULT NULL,
  `lk_word_1` int(11) DEFAULT NULL,
  `lk_word_2` int(11) DEFAULT NULL,
  `lk_word_3` int(11) DEFAULT NULL,
  `lk_word_4` int(11) DEFAULT NULL,
  `lk_word_5` int(11) DEFAULT NULL,
  `lk_word_6` int(11) DEFAULT NULL,
  `lk_word_7` int(11) DEFAULT NULL,
  `lk_word_8` int(11) DEFAULT NULL,
  `lk_word_9` int(11) DEFAULT NULL,
  UNIQUE KEY `id_lk` (`id_lk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
