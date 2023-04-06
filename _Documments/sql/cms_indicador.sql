-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 06-Abr-2023 às 12:33
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
-- Estrutura da tabela `cms_indicador`
--

CREATE TABLE `cms_indicador` (
  `id_cmsi` bigint UNSIGNED NOT NULL,
  `cmsi_indicador` char(30) NOT NULL,
  `cmsi_valor` text NOT NULL
) ENGINE=InnoDB;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `cms_indicador`
--
ALTER TABLE `cms_indicador`
  ADD UNIQUE KEY `id_cmsi` (`id_cmsi`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cms_indicador`
--
ALTER TABLE `cms_indicador`
  MODIFY `id_cmsi` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
