-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 13-Ago-2022 às 13:10
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
-- Banco de dados: `brapci`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `rdf_form_groups`
--

DROP TABLE IF EXISTS `rdf_form_groups`;
CREATE TABLE IF NOT EXISTS `rdf_form_groups` (
  `id_gr` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gr_name` char(100) COLLATE utf8_bin DEFAULT NULL,
  `gr_ord` int(11) NOT NULL DEFAULT '99',
  UNIQUE KEY `id_gr` (`id_gr`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `rdf_form_groups`
--

INSERT INTO `rdf_form_groups` (`id_gr`, `gr_name`, `gr_ord`) VALUES
(1, 'RESPONSABILITY', 10),
(2, 'IDENTIFY', 1),
(3, 'TITLE', 5),
(4, 'EDITION', 14),
(5, 'PUBLICATION', 13),
(6, 'PHYSICAL DESCRIPTION', 15),
(7, 'SERIES', 30),
(8, 'NOTES', 100),
(9, 'STANDARDIZED NUMBERS', 3),
(10, 'SUPPLEMENTARY', 90),
(11, 'SUMMARY', 95),
(12, 'CHAPTERS', 80),
(13, 'CLASSIFICATION', 45),
(14, 'SUBJECT', 50);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
