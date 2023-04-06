-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 06-Abr-2023 às 12:11
-- Versão do servidor: 8.0.31-0ubuntu0.20.04.2
-- versão do PHP: 7.4.3

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
-- Estrutura da tabela `cms`
--

CREATE TABLE `cms` (
  `id_cms` bigint UNSIGNED NOT NULL,
  `cms_ref` varchar(10) NOT NULL,
  `cms_pos` int NOT NULL,
  `cms_text` longtext NOT NULL,
  `cms_lang` char(5) NOT NULL
) ENGINE=InnoDB;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `cms`
--
ALTER TABLE `cms`
  ADD UNIQUE KEY `id_cms` (`id_cms`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cms`
--
ALTER TABLE `cms`
  MODIFY `id_cms` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
