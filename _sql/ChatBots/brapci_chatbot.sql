-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 02-Set-2022 às 14:53
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
-- Banco de dados: `brapci_chatbot`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `answers`
--

CREATE TABLE `answers` (
  `id_aw` bigint UNSIGNED NOT NULL,
  `aw_question` char(150) COLLATE utf8_bin NOT NULL,
  `aw_answer` text COLLATE utf8_bin NOT NULL,
  `aw_method` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `messages`
--

CREATE TABLE `messages` (
  `id_m` bigint UNSIGNED NOT NULL,
  `m_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `m_ip` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Extraindo dados da tabela `messages`
--

INSERT INTO `messages` (`id_m`, `m_message`, `m_ip`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Bom dia', '143.54.112.77', '2022-08-31 20:46:20', NULL, NULL),
(2, 'Ola', '189.6.254.199', '2022-08-31 22:09:48', NULL, NULL),
(3, 'Qual seu nome?', '189.6.254.199', '2022-08-31 22:10:09', NULL, NULL),
(4, 'Hello', '200.173.208.184', '2022-09-01 17:09:31', NULL, NULL),
(5, 'Qual e seu nome', '200.173.208.184', '2022-09-01 17:09:48', NULL, NULL),
(6, 'Hello', '200.173.208.184', '2022-09-01 17:09:56', NULL, NULL),
(7, 'Ciência da informacao', '200.173.208.184', '2022-09-01 17:10:27', NULL, NULL),
(8, 'Rene', '200.173.208.184', '2022-09-01 17:10:37', NULL, NULL),
(9, 'Terminologia', '143.54.112.77', '2022-09-01 19:28:04', NULL, NULL),
(10, 'Gostaria de saber sobre o tempo hoje', '143.54.112.77', '2022-09-01 19:30:30', NULL, NULL),
(11, 'Quero todos os textos da profa. Rita Laipelt', '143.54.112.77', '2022-09-01 19:31:20', NULL, NULL),
(12, 'Ola', '189.6.254.199', '2022-09-02 01:08:27', NULL, NULL),
(13, 'Bom dia', '187.71.142.252', '2022-09-02 11:46:54', NULL, NULL),
(14, 'Repositório de dados', '143.54.112.65', '2022-09-02 13:50:56', NULL, NULL),
(15, 'Ansiedade informacional', '143.54.112.65', '2022-09-02 13:51:22', NULL, NULL),
(16, 'Brapci', '143.54.112.65', '2022-09-02 13:51:32', NULL, NULL),
(17, 'Twitter', '143.54.112.65', '2022-09-02 13:51:43', NULL, NULL),
(18, 'Chatbot', '143.54.112.65', '2022-09-02 13:51:51', NULL, NULL),
(19, 'Pós-graduação', '143.54.112.65', '2022-09-02 14:48:31', NULL, NULL),
(20, 'Inteligência', '143.54.112.65', '2022-09-02 14:48:42', NULL, NULL),
(21, 'saúde', '143.54.112.65', '2022-09-02 14:48:46', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `method`
--

CREATE TABLE `method` (
  `id_m` bigint UNSIGNED NOT NULL,
  `m_method` text COLLATE utf8_bin NOT NULL,
  `m_code` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `method`
--

INSERT INTO `method` (`id_m`, `m_method`, `m_code`) VALUES
(1, 'Assuntos', ''),
(2, 'AboutMe', ''),
(3, 'DateTime', '');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `answers`
--
ALTER TABLE `answers`
  ADD UNIQUE KEY `id_aw` (`id_aw`);

--
-- Índices para tabela `messages`
--
ALTER TABLE `messages`
  ADD UNIQUE KEY `id_m` (`id_m`);

--
-- Índices para tabela `method`
--
ALTER TABLE `method`
  ADD UNIQUE KEY `id_m` (`id_m`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `answers`
--
ALTER TABLE `answers`
  MODIFY `id_aw` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `messages`
--
ALTER TABLE `messages`
  MODIFY `id_m` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `method`
--
ALTER TABLE `method`
  MODIFY `id_m` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
