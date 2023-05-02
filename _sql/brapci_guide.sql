-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 02-Maio-2023 às 20:42
-- Versão do servidor: 8.0.31
-- versão do PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `brapci_guide`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `guide`
--

DROP TABLE IF EXISTS `guide`;
CREATE TABLE IF NOT EXISTS `guide` (
  `id_g` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `g_name` char(150) NOT NULL,
  `g_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_g` (`id_g`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `guide`
--

INSERT INTO `guide` (`id_g`, `g_name`, `g_active`, `created_at`) VALUES
(1, 'Guia LattesData', 1, '2023-05-02 11:04:22');

-- --------------------------------------------------------

--
-- Estrutura da tabela `guide_content`
--

DROP TABLE IF EXISTS `guide_content`;
CREATE TABLE IF NOT EXISTS `guide_content` (
  `id_gc` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `gc_guide` int NOT NULL,
  `gc_type` char(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `gc_title` char(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `gc_content` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `gc_order` int NOT NULL,
  `gc_subsection` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_gc` (`id_gc`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `guide_content`
--

INSERT INTO `guide_content` (`id_gc`, `gc_guide`, `gc_type`, `gc_title`, `gc_content`, `gc_order`, `gc_subsection`, `created_at`) VALUES
(1, 1, 'H1', 'Acessando o LattesData', '', 3, 0, '2023-05-02 13:35:23'),
(4, 1, 'P', '', 'O repositório LattesData foi concebido no âmbito do Compromisso 3 do 4º Plano de Ação Nacional em Governo Aberto, que visava estabelecer mecanismos de governança de dados científicos para o avanço da Ciência Aberta no Brasil.  O LattesData é uma expansão da Plataforma Lattes que realiza o depósito e disponibilização de conjunto de dados de pesquisa de projetos fomentados pelo CNPq, permitindo compartilhamento e reuso pela comunidade científica, a preservação de dados de pesquisa e sua acessibilidade no longo prazo, além de múltiplas oportunidades de inovação.', 2, 7, '2023-05-02 17:42:02'),
(5, 1, 'P', '', 'Você pode acessar o LattesData através do endereço https://lattesdata.cnpq.br/', 6, 7, '2023-05-02 17:42:21'),
(6, 1, 'IMG', 'dataverse_login.png', '', 6, 7, '2023-05-02 18:15:16'),
(7, 1, 'H2', 'Primeiro Acesso', '', 4, 1, '2023-05-02 18:59:24'),
(8, 1, 'H2', 'Criando seu primeiro conjunto de dados', '', 5, 1, '2023-05-02 19:34:31'),
(9, 1, 'H1', 'Apresentação (Conheça o LattesData)', '', 2, 0, '2023-05-02 20:15:22'),
(10, 1, 'H1', 'Criação de conjunto de dados', '', 6, 0, '2023-05-02 20:30:28'),
(11, 1, 'H2', 'Editando os metadados do conjunto de dados', '', 7, 10, '2023-05-02 20:30:48'),
(12, 1, 'H2', 'Enviando arquivos de dados', '', 8, 10, '2023-05-02 20:31:54'),
(13, 1, 'H2', 'Editar termos', '', 10, 10, '2023-05-02 20:32:30'),
(14, 1, 'H1', 'Publicação do conjunto de dados', '', 20, 0, '2023-05-02 20:33:33'),
(15, 1, 'H1', 'Publicação do conjunto de dados', '', 19, 0, '2023-05-02 20:33:49');

-- --------------------------------------------------------

--
-- Estrutura da tabela `guide_content_type`
--

DROP TABLE IF EXISTS `guide_content_type`;
CREATE TABLE IF NOT EXISTS `guide_content_type` (
  `id_type` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_description` char(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `type_cod` char(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `type_code` text NOT NULL,
  `type_header` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_type` (`id_type`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `guide_content_type`
--

INSERT INTO `guide_content_type` (`id_type`, `type_description`, `type_cod`, `type_code`, `type_header`, `created_at`) VALUES
(1, 'Titulo (1)', 'H1', '<h1>$text</h1>', 1, '2023-05-02 11:34:11'),
(2, 'Titulo (2)', 'H2', '<h2>$text</h2>', 1, '2023-05-02 11:34:27'),
(3, 'Titulo (3)', 'H3', '<h3>$text</h3>', 1, '2023-05-02 11:34:41'),
(4, 'Parágrafo', 'P', '<p>$text</p>', 0, '2023-05-02 11:35:08'),
(5, 'Imagem (Full)', 'IMG', '<img src=\"$content\" class=\"img-full\">', 0, '2023-05-02 18:14:44');

-- --------------------------------------------------------

--
-- Estrutura da tabela `plano`
--

DROP TABLE IF EXISTS `plano`;
CREATE TABLE IF NOT EXISTS `plano` (
  `id_pl` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `pl_nome` varchar(200) NOT NULL,
  `pl_img` char(100) NOT NULL,
  `pl_content` text NOT NULL,
  UNIQUE KEY `id_pl` (`id_pl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura da tabela `trilha`
--

DROP TABLE IF EXISTS `trilha`;
CREATE TABLE IF NOT EXISTS `trilha` (
  `id_tr` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tr_trilha` tinytext NOT NULL,
  `tr_ativo` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_tr` (`id_tr`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `trilha`
--

INSERT INTO `trilha` (`id_tr`, `tr_trilha`, `tr_ativo`, `created_at`) VALUES
(1, 'Ciência Aberta', 1, '2023-04-18 14:57:09');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
