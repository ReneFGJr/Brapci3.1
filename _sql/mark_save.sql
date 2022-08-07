-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 07-Ago-2022 às 13:58
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
-- Banco de dados: `brapci_click`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `mark_save`
--

DROP TABLE IF EXISTS `mark_save`;
CREATE TABLE IF NOT EXISTS `mark_save` (
  `id_mk` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mk_name` text COLLATE utf8_bin NOT NULL,
  `mk_user` int(11) NOT NULL,
  `mk_selected` longtext COLLATE utf8_bin NOT NULL,
  `mk_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mk_update_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `id_mk` (`id_mk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
