-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 03-Jul-2023 às 10:07
-- Versão do servidor: 5.7.36
-- versão do PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `find`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `id_bk` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bk_title` text COLLATE utf8_bin NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_bk` (`id_bk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `books_expression`
--

DROP TABLE IF EXISTS `books_expression`;
CREATE TABLE IF NOT EXISTS `books_expression` (
  `id_be` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `be_title` text COLLATE utf8_bin NOT NULL,
  `be_authors` text COLLATE utf8_bin NOT NULL,
  `be_year` char(4) COLLATE utf8_bin NOT NULL,
  `be_cover` char(100) COLLATE utf8_bin NOT NULL,
  `be_rdf` int(11) NOT NULL DEFAULT '0',
  `be_isbn13` char(13) COLLATE utf8_bin NOT NULL,
  `be_isbn10` char(10) COLLATE utf8_bin NOT NULL,
  `be_type` int(11) NOT NULL,
  `be_lang` int(11) NOT NULL,
  `be_status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_be` (`id_be`),
  KEY `be_rdf` (`be_rdf`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `books_literal`
--

DROP TABLE IF EXISTS `books_literal`;
CREATE TABLE IF NOT EXISTS `books_literal` (
  `id_l` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `l_name` text COLLATE utf8_bin NOT NULL,
  `l_lang` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_l` (`id_l`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `books_manifestation`
--

DROP TABLE IF EXISTS `books_manifestation`;
CREATE TABLE IF NOT EXISTS `books_manifestation` (
  `id_bm` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bm_book_expression` int(11) NOT NULL,
  `bm_propriety` int(11) NOT NULL,
  `bm_resource` int(11) NOT NULL,
  `bm_literal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_bm` (`id_bm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `book_library`
--

DROP TABLE IF EXISTS `book_library`;
CREATE TABLE IF NOT EXISTS `book_library` (
  `id_bl` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bl_library` int(11) DEFAULT NULL,
  `bl_expression` int(11) DEFAULT NULL,
  `bl_tombo` int(11) DEFAULT NULL,
  `bl_exemplar` int(11) NOT NULL,
  `bl_catalogador` int(11) DEFAULT NULL,
  `bl_status` int(11) DEFAULT NULL,
  `bl_emprestimo` timestamp NULL DEFAULT NULL,
  `bl_renovacao` timestamp NULL DEFAULT NULL,
  `bl_usuario` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_bl` (`id_bl`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `library`
--

DROP TABLE IF EXISTS `library`;
CREATE TABLE IF NOT EXISTS `library` (
  `id_lb` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lb_name` char(100) COLLATE utf8_bin NOT NULL,
  `lb_description` text COLLATE utf8_bin NOT NULL,
  `lb_address` text COLLATE utf8_bin NOT NULL,
  `lb_city` int(11) NOT NULL,
  `lb_logo` char(100) COLLATE utf8_bin NOT NULL,
  `lb_logo_mini` char(100) COLLATE utf8_bin NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_lb` (`id_lb`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `library`
--

INSERT INTO `library` (`id_lb`, `lb_name`, `lb_description`, `lb_address`, `lb_city`, `lb_logo`, `lb_logo_mini`, `created_at`) VALUES
(1, 'Biblioteca CEDAP', '', '', 1, 'img/logo/logo_1001_mini.jpg', 'img/logo_library.png', '2023-06-14 19:59:28'),
(2, 'Beabah! - Bibliotecas comunitárias RS', '', '', 1, 'img/logo/logo_1003_mini.jpg', 'img/logo/logo-beabah_mini.jpg', '2023-06-14 19:59:28');

-- --------------------------------------------------------

--
-- Estrutura da tabela `rdf_class`
--

DROP TABLE IF EXISTS `rdf_class`;
CREATE TABLE IF NOT EXISTS `rdf_class` (
  `id_c` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `c_class` varchar(200) NOT NULL,
  `c_prefix` int(11) NOT NULL DEFAULT '0',
  `c_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `c_class_main` int(11) NOT NULL DEFAULT '0',
  `c_type` char(1) NOT NULL,
  `c_order` int(11) NOT NULL DEFAULT '99',
  `c_pa` int(11) NOT NULL DEFAULT '0',
  `c_repetitive` int(11) NOT NULL DEFAULT '1',
  `c_vc` int(11) NOT NULL DEFAULT '0',
  `c_find` int(11) NOT NULL DEFAULT '0',
  `c_identify` int(11) NOT NULL DEFAULT '0',
  `c_contextualize` int(11) NOT NULL DEFAULT '0',
  `c_justify` int(11) NOT NULL DEFAULT '0',
  `c_url` char(100) NOT NULL,
  `c_url_update` date NOT NULL DEFAULT '1900-01-01',
  `c_equivalent` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id_c` (`id_c`),
  UNIQUE KEY `classes` (`c_class`(30),`c_prefix`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=205 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `rdf_class`
--

INSERT INTO `rdf_class` (`id_c`, `c_class`, `c_prefix`, `c_created`, `c_class_main`, `c_type`, `c_order`, `c_pa`, `c_repetitive`, `c_vc`, `c_find`, `c_identify`, `c_contextualize`, `c_justify`, `c_url`, `c_url_update`, `c_equivalent`) VALUES
(1, 'Agent', 10, '2017-11-03 14:33:53', 0, 'C', 10, 0, 1, 0, 1, 0, 0, 0, 'http://xmlns.com/foaf/0.1/#term_Agent', '1900-01-01', 0),
(2, 'Person', 10, '2017-11-03 14:33:53', 1, 'C', 11, 0, 1, 0, 1, 0, 0, 0, 'http://xmlns.com/foaf/0.1/#term_Person', '1900-01-01', 0),
(3, 'Family', 2, '2017-11-03 14:34:34', 1, 'C', 12, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(4, 'CorporateBody', 16, '2017-11-03 14:34:34', 1, 'C', 13, 0, 1, 0, 1, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/8', '2018-10-09', 0),
(5, 'prefLabel', 4, '2017-11-03 14:51:55', 0, 'P', 3, 1, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(6, 'altLabel', 4, '2017-11-03 14:52:07', 0, 'P', 25, 1, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(7, 'hasCutter', 2, '2017-11-03 16:44:41', 0, 'P', 28, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(8, 'hasBorn', 2, '2017-11-03 16:46:48', 0, 'P', 21, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(9, 'hasDie', 2, '2017-11-03 16:46:48', 0, 'P', 22, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(10, 'hasISBN', 2, '2017-11-03 17:08:28', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(11, 'hasISSN', 2, '2017-11-03 17:08:28', 0, 'P', 11, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(12, 'Date', 2, '2017-11-03 17:23:53', 0, 'C', 100, 0, 1, 0, 0, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/10', '2018-08-27', 0),
(13, 'Class', 1, '2017-11-03 17:45:14', 0, 'C', 0, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(14, 'sourceNote', 2, '2017-11-04 17:08:03', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(15, 'hiddenLabel', 4, '2017-11-04 17:22:52', 0, 'P', 26, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(16, 'Work', 2, '2017-11-04 19:48:58', 0, 'C', 3, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(17, 'hasTitle', 2, '2017-11-04 19:57:49', 0, 'P', 8, 1, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(18, 'hasSubtitle', 2, '2017-11-04 19:57:49', 0, 'P', 9, 1, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(19, 'hasAuthor', 2, '2017-11-04 20:34:16', 0, 'P', 16, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(20, 'hasOrganizator', 2, '2017-11-04 20:34:16', 0, 'P', 17, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(21, 'hasCover', 2, '2017-11-04 22:00:58', 0, 'P', 999, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(22, 'hasTitlePerson', 2, '2017-11-05 16:10:33', 0, 'P', 29, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(23, 'hasGender', 1, '2017-11-05 16:11:47', 0, 'P', 29, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(24, 'Gender', 2, '2017-11-05 16:11:47', 0, 'C', 99, 0, 1, 0, 1, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/63', '2019-11-25', 0),
(25, 'hasPlaceBirth', 2, '2017-11-05 16:13:16', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(26, 'hasPlaceDeath', 2, '2017-11-05 16:13:16', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(27, 'hasLiveCoutry', 2, '2017-11-05 16:47:28', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(28, 'hasAffiliation', 2, '2017-11-05 16:47:28', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(29, 'hasLanguagePerson', 2, '2017-11-05 16:49:26', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(30, 'hasPersonAtivity', 2, '2017-11-05 16:49:26', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(31, 'hasProfission', 2, '2017-11-05 16:50:12', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(32, 'hasPersonBiography', 2, '2017-11-05 16:50:12', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(33, 'Expression', 2, '2017-11-07 22:19:55', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(34, 'Manifestation', 16, '2017-11-07 22:19:55', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(35, 'Item', 2, '2017-11-07 22:20:34', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(36, 'Concept', 2, '2017-11-07 22:20:34', 0, 'C', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(37, 'isRealizedThrough', 2, '2017-11-07 22:25:33', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(38, 'hasFormExpression', 2, '2017-11-07 22:49:32', 0, 'P', 99, 0, 0, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(39, 'FormWork', 2, '2017-11-07 22:50:50', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/12', '2018-08-30', 0),
(40, 'hasDateFirstWork', 2, '2017-11-07 23:43:06', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(41, 'hasIllustrator', 2, '2017-11-08 00:43:03', 0, 'P', 18, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(42, 'Image', 2, '2017-11-08 00:56:26', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(43, 'hasPublicationVolume', 2, '2017-11-08 02:18:28', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(44, 'Linguage', 2, '2017-11-08 02:18:28', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/53', '2018-08-27', 0),
(45, 'Place', 16, '2017-11-08 15:53:34', 0, 'C', 99, 0, 1, 0, 1, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/55', '2018-09-18', 0),
(91, 'lat', 12, '2018-01-22 20:48:13', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '2017-01-01', 0),
(46, 'hasTypePlace', 2, '2017-11-08 15:55:34', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(47, 'PlaceType', 2, '2017-11-08 15:56:37', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(48, 'TypeCorporateBody', 2, '2017-11-11 16:50:43', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/54', '2018-08-27', 0),
(49, 'hasCorporateBodyType', 2, '2017-11-11 16:50:43', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(50, 'TypeOfAcquisition', 2, '2017-11-11 18:54:50', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/52', '2018-08-27', 0),
(51, 'hasAcquisitionBy', 2, '2017-11-11 18:54:50', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(52, 'hasIdRegister', 2, '2017-11-11 22:32:06', 0, 'P', 10, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(53, 'isAppellationOfWork', 2, '2017-11-11 22:40:01', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(54, 'isAppellationOfExpression', 2, '2017-11-12 00:03:17', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(55, 'isAppellationOfManifestation', 2, '2017-11-12 00:09:54', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(56, 'Edition', 2, '2017-11-12 02:21:42', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/13', '2018-08-30', 0),
(57, 'isEdition', 2, '2017-11-12 02:21:42', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(58, 'isPlaceOfPublication', 2, '2017-11-12 02:26:13', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(59, 'dateOfPublication', 2, '2017-11-12 02:31:21', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(60, 'isPublisher', 2, '2017-11-12 02:31:21', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(61, 'FormOfCarrier', 2, '2017-11-12 02:40:52', 0, 'C', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(62, 'isFormOfCarrier', 2, '2017-11-12 02:40:52', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(63, 'ISBN', 2, '2017-11-12 02:48:32', 0, 'C', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(64, 'CDU', 2, '2017-11-12 10:47:53', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '2018-02-14', 0),
(131, 'SubjectThesa', 2, '2018-08-30 09:35:46', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/64', '2018-09-04', 0),
(65, 'hasClassificationCDU', 2, '2017-11-12 10:47:53', 0, 'P', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(66, 'CDD', 2, '2017-11-12 12:01:40', 0, 'c', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(67, 'hasClassificationCDD', 2, '2017-11-12 12:01:40', 0, 'p', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(68, 'hasTranslator', 2, '2017-11-12 14:10:12', 0, 'P', 21, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(69, 'hasImageDescription', 2, '2017-11-13 09:54:27', 0, 'P', 70, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(70, 'hasImageSize', 2, '2017-11-13 09:54:27', 0, 'P', 90, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(71, 'hasImageChecksum', 2, '2017-11-13 09:54:47', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(72, 'Number', 2, '2017-11-13 10:02:34', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 1, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/62', '2018-08-30', 0),
(73, 'Checksum', 2, '2017-11-13 10:06:27', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(74, 'FileType', 2, '2017-11-13 10:11:51', 0, 'C', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(75, 'hasFileType', 2, '2017-11-13 10:11:51', 0, 'P', 40, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(76, 'hasFileStorage', 2, '2017-11-13 10:14:42', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(77, 'FileStorage', 2, '2017-11-13 10:15:20', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(78, 'hasImageWidth', 2, '2017-11-13 10:43:02', 0, 'P', 80, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(79, 'hasImageHeight', 2, '2017-11-13 10:43:02', 0, 'P', 81, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(80, 'hasPlaceItem', 2, '2017-11-13 14:31:46', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(81, 'hasWayOfAcquisition', 2, '2017-11-13 14:31:46', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(82, 'hasItemStatusCataloging', 2, '2017-11-15 11:24:46', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(83, 'ItemStatusCataloging', 2, '2017-11-15 11:24:46', 0, 'C', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(84, 'DateTime', 2, '2017-11-15 11:32:53', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(85, 'hasDateTime', 2, '2017-11-15 11:32:53', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(86, 'hasLanguageExpression', 2, '2017-12-03 23:50:28', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(87, 'primaryTopic', 10, '2017-12-08 08:17:36', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(88, 'isEmbodiedIn', 2, '2017-12-09 00:27:56', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(89, 'hasPage', 2, '2017-12-11 21:57:47', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(90, 'Pages', 2, '2017-12-11 22:03:20', 0, 'C', 99, 0, 1, 1, 0, 1, 0, 0, '', '1900-01-01', 0),
(92, 'long', 12, '2018-01-22 20:48:13', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '2017-01-01', 0),
(93, 'Library', 2, '2018-01-30 14:08:33', 0, 'C', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(94, 'Bookcase', 2, '2018-01-30 14:10:30', 0, 'C', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(95, 'hasRegisterId', 2, '2018-01-30 14:20:53', 0, 'P', 7, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(96, 'isExemplifiedBy', 2, '2018-01-30 19:11:50', 0, 'P', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(97, 'isOwnedBy', 2, '2018-01-30 19:17:20', 0, 'P', 5, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(98, 'hasLocatedIn', 2, '2018-01-30 19:19:00', 0, 'P', 6, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(99, 'wayOfAcquisition', 2, '2018-02-01 14:16:47', 0, 'P', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(100, 'itHasPrefaceOf', 2, '2018-02-08 14:10:52', 0, 'P', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(101, 'itHasIntroductionOf', 2, '2018-02-08 14:13:05', 0, 'P', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(102, 'hasSerieName', 2, '2018-02-09 12:08:13', 0, 'P', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(103, 'SerieName', 2, '2018-02-09 12:09:25', 0, 'C', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(104, 'hasAdaptedBy', 2, '2018-02-09 16:43:00', 0, 'P', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(105, 'hasIncludedIn', 2, '2018-02-09 16:48:17', 0, 'P', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(106, 'hasTitleAlternative', 2, '2018-02-14 16:44:15', 0, 'P', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(107, 'Cutter', 2, '2018-02-22 16:42:44', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(108, 'hasAdvisor', 2, '2018-03-14 10:56:32', 0, 'P', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(109, 'hasFileName', 2, '2018-03-16 00:49:35', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(110, 'SemTitle', 5, '2018-03-31 09:57:21', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(111, 'hasEmail', 1, '2018-06-09 21:36:34', 0, 'P', 9, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(112, 'Journal', 1, '2018-06-09 22:58:29', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(113, 'hasUrl ', 1, '2018-06-09 23:18:35', 0, 'P', 17, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(114, 'Issue', 1, '2018-06-10 01:15:13', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(115, 'hasIssue', 1, '2018-06-10 01:15:13', 0, 'P', 15, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(116, 'Article', 2, '2018-06-10 13:56:51', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(117, 'hasIssueOf ', 1, '2018-06-10 13:57:10', 0, 'P', 18, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(118, 'hasAbstract', 1, '2018-06-10 14:26:22', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(119, 'hasSource ', 1, '2018-06-10 14:41:40', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(120, 'Subject', 1, '2018-06-10 15:12:42', 0, 'C', 20, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(121, 'hasSubject', 1, '2018-06-10 15:13:05', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(122, 'ArticleSection', 1, '2018-06-10 15:24:12', 0, 'C', 3, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(123, 'hasSectionOf', 1, '2018-06-10 15:25:08', 0, 'P', 70, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(124, 'dateOfAvailability', 1, '2018-06-10 16:14:05', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(125, 'affiliatedWith', 1, '2018-06-10 16:22:59', 0, 'P', 4, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(126, 'isPubishIn', 1, '2018-06-10 17:18:00', 0, 'P', 40, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(127, 'Word', 1, '2018-07-02 12:42:01', 1, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '2018-07-01', 0),
(128, 'hasContent', 1, '2018-07-02 19:46:41', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '2018-07-02', 0),
(129, 'equivalentClass', 13, '2018-07-04 02:03:39', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '2018-07-03', 0),
(130, 'hasFileSize', 13, '2018-08-08 16:43:39', 0, 'P', 91, 0, 1, 0, 0, 0, 0, 0, '', '2018-08-08', 0),
(132, 'acronym', 2, '2018-08-30 09:39:55', 0, 'P', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(133, 'abbreviation_of', 2, '2018-08-30 09:40:26', 0, 'P', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(134, 'isMasculine', 2, '2018-08-30 09:41:02', 0, 'P', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(135, 'is_gerund ', 2, '2018-08-30 09:41:30', 0, 'P', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(136, 'is_verbal_inflection', 2, '2018-08-30 09:41:52', 0, 'P', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(137, 'isFeminine', 2, '2018-08-30 09:42:32', 0, 'P', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(138, 'hasPlace', 2, '2018-08-30 23:33:14', 0, 'P', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(139, 'code', 2, '2018-08-30 23:36:52', 0, 'P', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(140, 'hasPublicationNumber', 2, '2018-09-05 08:38:12', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(141, 'hasPageStart', 2, '2018-09-05 08:48:16', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(142, 'hasPageEnd', 2, '2018-09-05 08:48:26', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(143, 'PublicationVolume', 2, '2018-09-05 09:14:50', 0, 'C', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(144, 'PublicationNumber', 2, '2018-09-05 09:15:04', 0, 'C', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(145, 'Community', 2, '2018-09-05 10:56:55', 0, 'C', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(146, 'Collection', 2, '2018-09-05 10:57:10', 0, 'C', 99, 0, 1, 0, 1, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/114', '2019-06-25', 0),
(147, 'File', 2, '2018-09-05 22:36:37', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(148, 'hasId', 2, '2018-09-10 21:32:31', 0, 'P', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(149, 'hasSectionIndex', 2, '2018-10-01 17:08:17', 0, 'P', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(150, 'ExclusiveDisjunction', 2, '2018-10-01 17:17:53', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/103', '2018-10-01', 0),
(151, 'Lattes', 2, '2018-10-21 14:03:39', 0, 'C', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(152, 'hasLattesID', 2, '2018-10-21 14:05:03', 0, 'P', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(153, 'Country', 2, '2018-10-21 14:10:05', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'https://www.ufrgs.br/tesauros/index.php/skos/rdf/106', '2018-10-21', 0),
(154, 'hasCollection', 2, '2018-10-28 01:42:52', 0, 'P', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(155, 'hasWordCollection', 2, '2019-09-23 17:29:32', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(156, 'WordColletion', 2, '2019-09-23 17:29:50', 0, 'C', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(157, 'hasEditor', 2, '2019-10-27 18:55:23', 0, 'P', 99, 0, 1, 1, 1, 0, 0, 0, '', '1900-01-01', 0),
(158, 'Text', 2, '2019-11-24 13:26:23', 0, 'C', 99, 0, 1, 0, 1, 0, 0, 0, '', '1900-01-01', 0),
(159, 'wasReceivedOn', 2, '2020-03-06 13:21:09', 0, 'P', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(160, 'wasAcceptedOn', 2, '2020-03-06 13:21:21', 0, 'P', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(161, 'wasPresentationOn', 2, '2020-03-06 13:21:33', 0, 'P', 99, 0, 1, 1, 0, 0, 0, 0, '', '1900-01-01', 0),
(162, 'hasPicture', 2, '2020-10-14 00:58:42', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(164, 'hasGoogleSchollarId', 2, '2021-06-16 00:18:01', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(165, 'Proceeding', 2, '2022-02-23 10:01:02', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'Proceeding', '1900-01-01', 0),
(166, 'IssueProceeding', 2, '2022-02-23 10:01:02', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'IssueProceeding', '1900-01-01', 0),
(167, 'hasIssueProceedingOf', 2, '2022-02-23 10:01:02', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'hasIssueProceedingOf', '1900-01-01', 0),
(168, 'hasIssueProceeding', 2, '2022-02-23 10:01:02', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'hasIssueProceeding', '1900-01-01', 0),
(169, 'ProceedingSection', 2, '2022-02-23 10:01:03', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'ProceedingSection', '1900-01-01', 0),
(170, 'Author', 2, '2022-02-25 13:39:50', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'Author', '1900-01-01', 2),
(171, 'Book', 2, '2022-08-05 11:59:33', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'Book', '1900-01-01', 0),
(172, 'Publisher', 2, '2022-08-05 18:52:10', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'Publisher', '1900-01-01', 0),
(174, 'hasTumbNail', 0, '2022-08-15 19:34:09', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'hasTumbNail', '1900-01-01', 0),
(175, 'hasFileDirectory', 0, '2022-08-15 19:34:09', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'hasFileDirectory', '1900-01-01', 0),
(176, 'ContentType', 0, '2022-08-15 19:34:09', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'ContentType', '1900-01-01', 0),
(177, 'hasContentType', 0, '2022-08-15 19:34:09', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'hasContentType', '1900-01-01', 0),
(178, 'ClassificationAncib', 2, '2022-08-21 21:01:29', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(179, 'hasClassificationAncib', 2, '2022-08-21 21:04:52', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(180, 'DOI', 2, '2022-08-25 00:35:49', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(181, 'hasDOI', 2, '2022-08-25 00:36:10', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(182, 'BookChapter', 2, '2022-08-25 11:31:17', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(183, 'hasBookChapter', 2, '2022-08-25 11:31:36', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(184, 'License', 2, '2022-10-07 11:35:06', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(185, 'hasLicense', 2, '2022-10-07 11:35:18', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(186, 'Volume', 2, '2022-10-07 11:48:02', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(187, 'hasVolume', 2, '2022-10-07 11:48:15', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(188, 'Summary', 2, '2023-01-25 10:39:11', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(189, 'hasSummary', 2, '2023-01-25 10:39:29', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(190, 'fullText', 2, '2023-03-06 19:27:09', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(191, 'hasLogo', 2, '2023-04-07 10:21:41', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(192, 'RORID', 2, '2023-04-26 15:22:02', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(193, 'ORCID', 2, '2023-04-26 15:22:29', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(194, 'hasOrcId', 2, '2023-04-26 15:22:56', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(195, 'hasRORid', 2, '2023-04-26 15:23:19', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(196, 'hasCoordinator', 2, '2023-05-05 17:23:16', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, '', '1900-01-01', 0),
(197, 'L&PM', 0, '2023-06-15 17:22:19', 0, 'C', 99, 0, 1, 0, 0, 0, 0, 0, 'L&PM', '1900-01-01', 0),
(198, 'description', 0, '2023-06-26 23:43:52', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'description', '1900-01-01', 0),
(199, 'hasColorclassification', 0, '2023-06-27 00:02:51', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'hasColorclassification', '1900-01-01', 0),
(200, 'isPlacePublisher', 0, '2023-06-27 00:05:58', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'isPlacePublisher', '1900-01-01', 0),
(201, 'hasVolumeNumber', 0, '2023-06-27 00:16:40', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'hasVolumeNumber', '1900-01-01', 0),
(202, 'hasClassificationCountry', 0, '2023-06-27 00:49:56', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'hasClassificationCountry', '1900-01-01', 0),
(203, 'hasClassificacaoAssunto', 0, '2023-06-27 00:50:50', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'hasClassificacaoAssunto', '1900-01-01', 0),
(204, 'hasPublisher', 0, '2023-07-03 04:13:20', 0, 'P', 99, 0, 1, 0, 0, 0, 0, 0, 'hasPublisher', '1900-01-01', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `rdf_concept`
--

DROP TABLE IF EXISTS `rdf_concept`;
CREATE TABLE IF NOT EXISTS `rdf_concept` (
  `id_cc` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cc_class` int(11) NOT NULL,
  `cc_use` int(11) NOT NULL DEFAULT '0',
  `cc_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cc_pref_term` int(11) NOT NULL,
  `cc_origin` char(20) NOT NULL,
  `cc_update` date NOT NULL,
  `cc_status` int(11) NOT NULL DEFAULT '0',
  `cc_library` int(11) NOT NULL DEFAULT '9001',
  `c_equivalent` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id_c` (`id_cc`),
  KEY `cc_term` (`cc_pref_term`),
  KEY `cc_class` (`cc_class`),
  KEY `cc_use` (`cc_use`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `rdf_data`
--

DROP TABLE IF EXISTS `rdf_data`;
CREATE TABLE IF NOT EXISTS `rdf_data` (
  `id_d` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `d_r1` int(11) NOT NULL,
  `d_p` int(11) NOT NULL,
  `d_r2` int(11) NOT NULL,
  `d_literal` int(11) NOT NULL DEFAULT '0',
  `d_creadted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `d_o` int(11) NOT NULL DEFAULT '0',
  `d_update` datetime DEFAULT NULL,
  `d_user` int(11) NOT NULL DEFAULT '0',
  `d_library` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id_d` (`id_d`),
  KEY `d_r1` (`d_r1`),
  KEY `d_r2` (`d_r2`),
  KEY `d_p` (`d_p`),
  KEY `dt` (`d_r1`,`d_p`,`d_r2`),
  KEY `d_l` (`d_literal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `rdf_erros`
--

DROP TABLE IF EXISTS `rdf_erros`;
CREATE TABLE IF NOT EXISTS `rdf_erros` (
  `id_erro` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `erro_id` int(11) NOT NULL,
  `erro_msg` int(11) NOT NULL,
  `erro_nr` int(11) NOT NULL,
  `erro_data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_erro` (`id_erro`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `rdf_form_class`
--

DROP TABLE IF EXISTS `rdf_form_class`;
CREATE TABLE IF NOT EXISTS `rdf_form_class` (
  `id_sc` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sc_class` int(11) NOT NULL,
  `sc_propriety` int(11) NOT NULL,
  `sc_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sc_range` int(11) NOT NULL,
  `sc_ativo` int(11) NOT NULL DEFAULT '1',
  `sc_ord` int(11) NOT NULL DEFAULT '99',
  `sc_library` int(11) NOT NULL DEFAULT '0',
  `sc_library_2` int(11) NOT NULL,
  `sc_group` char(20) NOT NULL DEFAULT '''''',
  `sc_global` int(11) NOT NULL DEFAULT '1',
  `sc_visible` int(11) NOT NULL DEFAULT '1',
  UNIQUE KEY `id_sc` (`id_sc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `rdf_name`
--

DROP TABLE IF EXISTS `rdf_name`;
CREATE TABLE IF NOT EXISTS `rdf_name` (
  `id_n` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `n_name` text NOT NULL,
  `n_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `n_lock` int(11) NOT NULL DEFAULT '1',
  `n_lang` char(5) NOT NULL DEFAULT 'pt_BR',
  `n_md5` char(32) NOT NULL DEFAULT '',
  UNIQUE KEY `id_n` (`id_n`),
  KEY `n_md5` (`n_md5`) USING BTREE,
  KEY `n_name` (`n_name`(20))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `rdf_prefix`
--

DROP TABLE IF EXISTS `rdf_prefix`;
CREATE TABLE IF NOT EXISTS `rdf_prefix` (
  `id_prefix` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `prefix_ref` char(30) NOT NULL,
  `prefix_url` char(250) NOT NULL,
  `prefix_ativo` int(11) NOT NULL DEFAULT '1',
  UNIQUE KEY `id_prefix` (`id_prefix`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `rdf_prefix`
--

INSERT INTO `rdf_prefix` (`id_prefix`, `prefix_ref`, `prefix_url`, `prefix_ativo`) VALUES
(1, 'dc', 'http://purl.org/dc/elements/1.1/', 1),
(2, 'brapci', 'http://basessibi.c3sl.ufpr.br/brapci/index.php/rdf/', 1),
(3, 'rdfs', 'http://www.w3.org/2000/01/rdf-schema', 1),
(4, 'skos', 'http://www.w3.org/2004/02/skos/core', 1),
(5, 'dcterm', 'http://purl.org/dc/terms/', 1),
(6, 'fb', 'http://rdf.freebases.com/ns', 1),
(7, 'gn', 'http://www.geonames.org/ontology#', 1),
(8, 'geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#', 1),
(9, 'lotico', 'http://www.lotico.com/ontology/', 1),
(10, 'foaf', 'http://xmlns.com/foaf/spec/', 1),
(11, 'viaf', 'http://viaf.org/viaf/', 1),
(12, 'wgs84_pos', 'https://www.w3.org/2003/01/geo/', 1),
(13, 'owl', 'https://www.w3.org/TR/owl-ref/', 1),
(15, 'VOID', '', 1),
(16, 'frbr', 'https://vocab.org/frbr/core', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
