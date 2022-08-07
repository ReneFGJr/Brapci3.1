-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 07-Ago-2022 às 13:42
-- Versão do servidor: 8.0.28-0ubuntu0.20.04.3
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
-- Banco de dados: `brapci_click`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `download`
--

CREATE TABLE `download` (
  `id_dw` int NOT NULL,
  `dw_rdf` int NOT NULL DEFAULT '0',
  `dw_ip` char(20) NOT NULL,
  `dw_download` int NOT NULL DEFAULT '0',
  `dw_type` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `download`
--

INSERT INTO `download` (`id_dw`, `dw_rdf`, `dw_ip`, `dw_download`, `dw_type`, `created_at`) VALUES
(1, 185809, '143.54.112.77', 0, 1, '2022-02-23 19:17:32'),
(2, 189134, '143.54.145.69', 0, 1, '2022-02-24 11:06:33'),
(3, 174839, '143.54.112.77', 0, 1, '2022-02-24 12:22:17'),
(4, 174905, '143.54.112.77', 0, 1, '2022-02-24 12:22:46'),
(5, 174839, '143.54.112.77', 0, 1, '2022-02-24 12:25:14'),
(6, 174307, '143.54.112.77', 0, 1, '2022-02-24 12:25:44'),
(7, 174375, '143.54.112.77', 0, 1, '2022-02-24 12:26:26'),
(8, 174270, '143.54.112.77', 0, 1, '2022-02-24 12:27:21'),
(9, 174270, '143.54.112.77', 0, 1, '2022-02-24 12:27:23'),
(10, 174284, '143.54.112.77', 0, 1, '2022-02-24 12:27:45'),
(11, 174340, '143.54.112.77', 0, 1, '2022-02-24 12:28:12'),
(12, 177168, '143.54.112.77', 0, 1, '2022-02-24 12:29:40'),
(13, 177185, '143.54.112.77', 0, 1, '2022-02-24 12:30:04'),
(14, 177227, '143.54.112.77', 0, 1, '2022-02-24 12:30:31'),
(15, 177629, '143.54.112.77', 0, 1, '2022-02-24 12:30:56'),
(16, 177766, '143.54.112.77', 0, 1, '2022-02-24 12:31:18'),
(17, 177796, '143.54.112.77', 0, 1, '2022-02-24 12:31:37'),
(18, 171665, '143.54.112.77', 0, 1, '2022-02-24 12:32:24'),
(19, 172389, '143.54.112.77', 0, 1, '2022-02-24 12:32:51'),
(20, 172389, '143.54.112.77', 0, 1, '2022-02-24 12:33:05'),
(21, 172389, '143.54.112.77', 0, 1, '2022-02-24 12:34:10'),
(22, 193989, '143.54.112.77', 0, 1, '2022-03-15 19:39:03'),
(23, 123163, '143.54.112.77', 0, 1, '2022-03-17 12:30:07');

-- --------------------------------------------------------

--
-- Estrutura da tabela `users_log`
--

CREATE TABLE `users_log` (
  `id_ul` bigint UNSIGNED NOT NULL,
  `ul_user` int NOT NULL,
  `ul_access` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ul_ip` char(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `users_log`
--

INSERT INTO `users_log` (`id_ul`, `ul_user`, `ul_access`, `ul_ip`) VALUES
(1, 1, '2022-03-14 12:40:01', '143.54.112.77'),
(2, 1, '2022-03-14 12:40:02', '143.54.112.77'),
(3, 1, '2022-03-15 17:06:54', '143.54.112.77'),
(4, 1, '2022-03-15 17:06:55', '143.54.112.77'),
(5, 1, '2022-03-15 19:44:57', '143.54.112.77'),
(6, 1, '2022-03-16 14:29:50', '143.54.112.77'),
(7, 1, '2022-03-16 14:30:30', '143.54.112.77'),
(8, 1, '2022-03-16 14:30:30', '143.54.112.77'),
(9, 1, '2022-03-17 11:54:29', '143.54.112.77'),
(10, 1, '2022-03-17 11:54:30', '143.54.112.77'),
(11, 1, '2022-03-17 12:47:20', '143.54.112.77'),
(12, 1, '2022-03-18 14:23:38', '143.54.112.77'),
(13, 1, '2022-03-18 15:18:20', '143.54.114.150'),
(14, 1, '2022-03-18 15:44:05', '143.54.114.150'),
(15, 1, '2022-03-18 17:02:30', '143.54.114.150'),
(16, 1, '2022-03-20 22:50:27', '143.54.114.150'),
(17, 1, '2022-03-29 12:20:05', '143.54.112.77'),
(18, 1, '2022-03-29 19:38:48', '143.54.112.77'),
(19, 6, '2022-06-28 19:12:33', '143.54.112.77');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `download`
--
ALTER TABLE `download`
  ADD PRIMARY KEY (`id_dw`,`dw_rdf`);

--
-- Índices para tabela `users_log`
--
ALTER TABLE `users_log`
  ADD UNIQUE KEY `id_ul` (`id_ul`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `download`
--
ALTER TABLE `download`
  MODIFY `id_dw` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `users_log`
--
ALTER TABLE `users_log`
  MODIFY `id_ul` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
