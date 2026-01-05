-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 05, 2026 at 06:55 PM
-- Server version: 10.11.13-MariaDB-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brapci`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id_ev` bigint(20) UNSIGNED NOT NULL,
  `ev_name` char(200) NOT NULL,
  `ev_place` char(50) NOT NULL,
  `ev_ative` int(11) NOT NULL DEFAULT 1,
  `ev_permanent` int(11) NOT NULL DEFAULT 0,
  `ev_data_start` date NOT NULL,
  `ev_data_end` date NOT NULL DEFAULT '1900-01-01',
  `ev_deadline` int(11) NOT NULL DEFAULT 0,
  `ev_url` char(150) NOT NULL,
  `ev_description` text NOT NULL,
  `ev_image` char(250) DEFAULT NULL,
  `ev_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id_ev`, `ev_name`, `ev_place`, `ev_ative`, `ev_permanent`, `ev_data_start`, `ev_data_end`, `ev_deadline`, `ev_url`, `ev_description`, `ev_image`, `ev_count`) VALUES
(2, 'I Encontro de RDA no Brasil', 'Florianópolis, SC, Brasil', 1, 0, '2019-04-16', '2019-04-18', 20190210, 'http://rdanobrasil.org/', '', 'img/events/rdabrasil.jpg', 2027),
(3, 'XX Enancib', 'Florianópolis, SC, Brasil', 1, 0, '2019-10-21', '2019-10-25', 0, 'http://www.enancib2019.ufsc.br/', '', 'img/events/enancib.jpg', 1747),
(4, 'ISKO-Brasil', 'Belém, PA, Brasil', 1, 0, '2019-09-02', '2019-09-03', 0, 'http://isko-brasil.org.br/?page_id=1563', '', 'img/events/isko-brasil.jpg', 1709),
(5, 'Conferência Luso-Brasileira de Acesso Aberto (ConfOA)', 'Manaus, AM, Brasil', 1, 0, '2019-10-01', '2019-10-04', 0, 'http://confoa.rcaap.pt/2019/', 'A 10ª Conferência Luso-Brasileira de Ciência Aberta (ConfOA) viaja até à Amazónia em 2019. Este ano, a ConfOA é acolhida conjuntamente pela Universidade Federal do Amazonas, a Universidade do Estado do Amazonas e o Instituto Federal do Amazonas. A 10ª ConfOA decorrerá em Manaus, de 1 a 4 de outubro, com abertura e um pré-workshop no dia 1, o programa principal da conferência nos dias 2 e 3, e workshops pós-conferência previstos para 4 de outubro.', 'img/events/confoa.jpg', 1666),
(6, 'Encontro Latinoamericano de Bibliotecários, Arquivistas e Museólogos (EBAM)', 'San Juan, Porto Rico', 1, 0, '2019-08-05', '2019-08-09', 0, 'https://ebam2019.wordpress.com/', '', 'img/events/ebam.jpg', 2037),
(7, 'Encontro Ibérico EDICIC 2019', 'Barcelona, Espanha', 1, 0, '2019-07-09', '2019-07-11', 0, 'https://fbd.ub.edu/edicic2019/pt', '', 'img/events/edicic.jpg', 1862),
(8, 'Biredial', 'São Paulo, SP, Brasil', 1, 0, '2019-07-30', '2019-08-02', 0, 'https://portal.febab.org.br/snbu2025', '', 'img/events/biredial.jpg', 1792),
(9, 'CONGRESSO DE GESTÃO ESTRATÉGICA DA INFORMAÇÃO, EMPREENDEDORISMO E INOVAÇÃO, II', 'Porto Alegre, RS, Brasil', 1, 0, '2019-06-17', '2019-06-19', 0, 'https://eventos.ufpr.br/redegic/CGEI2019', '', 'img/events/cgei.png', 1922),
(10, 'Congresso ISKO Espanha-Portugal', 'Barcelona, Espanha', 1, 0, '2019-07-11', '2019-07-12', 0, 'http://www.iskoiberico.org/', '', 'img/events/isko-pt-es.png', 2103),
(11, 'XIV Encontro Catarinense de Arquivos', 'Florianópolis, SC', 1, 0, '2019-11-18', '2019-11-19', 0, 'https://arquivistasc.wixsite.com/eca2019', '', 'img/events/XIV_Arquivo-m.png', 1670),
(12, '7º Encontro Brasileiro de Bibliometria e Cientometria', 'Salvador, BA, Brasil', 1, 0, '2020-07-21', '2020-07-23', 0, 'http://www.ebbc2020.ici.ufba.br/', '', 'img/events/ebbc2020.jpg', 2149),
(13, '11º Conferência Luso-Brasileira de Ciência Aberta', 'Braga, Portugal', 0, 0, '2020-10-06', '2020-10-08', 0, 'http://confoa.rcaap.pt/2020/', '', 'img/events/logo-confoa2020.png', 590),
(14, 'XXX', 'XX', 0, 0, '2020-10-26', '2020-10-30', 0, 'XX', '', 'img/events/ancib.jpg', 325),
(15, 'V Seminário de Competência em Informação', 'Marília, SP, Brasil', 1, 0, '2020-05-20', '2020-05-22', 0, 'http://enancib.marilia.unesp.br/index.php/V_Seminario/V_Seminario', '', 'img/events/vseminario.jpg', 1796),
(16, 'III Encontro de Pesquisa em Informação e Mediação', 'Marília, SP, Brasil', 1, 0, '2020-05-18', '2020-05-19', 0, 'http://enancib.marilia.unesp.br/index.php/III_EPIM/IIIEPIM', '', 'img/events/iiiepim.png', 1829),
(17, 'Encontro Nacional das Revistas Científicas do Brasil', 'FAC-UNILAGOS - Araruama, RJ', 1, 0, '2021-10-01', '2021-10-01', 0, 'https://www.sympla.com.br/encontro-nacional-das-revistas-cientificas-do-brasil__1116014', 'Apresentação dos principais problemas e entraves burocráticos e legais que as revistas científicas  enfrentam no  seu dia a dia no Brasil. Criar um network nacional dos editores das revistas e trocar experiências bem sucedidas na gestão gerencial das revistas.\r\n\r\nPúblico Alvo – Editores e coordenadores das revistas, bem como  diretores de IES, pesquisadores e acadêmicos em geral. ', 'img/events/enrcb.jpg', 1127),
(18, 'I Seminário Brasileiro Virtual de Biblioteconomia', 'Online', 1, 0, '2021-03-01', '2021-03-05', 0, 'https://www.youtube.com/channel/UCLxFXgjnAd7vZO3k7j8HyqA', '', 'img/events/captura_de_tela_2021-03-04_164730.jpg', 842),
(19, 'XXI Enancib', 'Rio de Janeiro, RJ', 1, 0, '2020-10-25', '2021-10-29', 0, 'http://enancib2021rio.ibict.br/', '', 'img/events/enancib-2021-1.png', 1735),
(20, 'III Latmétricas', 'Medellín, Colombia', 1, 0, '2021-09-13', '2021-09-15', 0, 'https://latmetricas.wordpress.com/', '', 'img/events/captura_de_tela_2021-03-16_154333.jpg', 1165),
(21, '4º FEISC', 'Porto Alegre (Online)', 1, 0, '2021-11-17', '2021-11-19', 0, 'https://www.ufrgs.br/feisc/wp/', '', 'img/events/captura_de_tela_2021-09-15_150218.png', 750),
(22, '8º Encontro Brasileiro de Bibliometria e Cientometria <sup>(presencial)</sup>', 'Maceió, AL', 1, 0, '2022-07-20', '2022-07-22', 0, 'https://www.ebbc.inf.br/ebbc8/', '', 'img/events/captura_de_tela_2022-01-28_092519.png', 1064),
(23, 'XXII Enancib <sup>(presencial)</sup>', 'Porto Alegre, RS', 1, 0, '2022-11-07', '2022-11-11', 0, 'https://www.ufrgs.br/enancib2022/', '', 'img/events/captura_de_tela_2022-02-28_110655.jpg', 1464),
(24, 'IXCNA - Congresso Nacional de Arquivologia <sup>(presencial)</sup>', 'Florianópolis, SC', 1, 0, '2022-05-02', '2022-05-06', 0, 'https://fnarq.com.br/ix-cna-2022/', '', 'img/events/cna.png', 730),
(25, '13º ConfOA 2022 <sup>(hibirdo)</sup>', 'Maputo, Moçambique', 1, 0, '2022-10-10', '2022-10-13', 0, 'https://confoa.rcaap.pt/2022/', '', 'img/events/confoa-2022.png', 1074),
(26, 'IV Encontro de Pesquisa em Informação e Mediação (IV EPIM)', 'Marília, SP, Brasil', 1, 0, '2022-06-23', '2022-06-25', 0, 'https://portalconferenciasppgci.marilia.unesp.br/index.php/IVEPIM/IVEPIM', '', 'img/events/captura_de_tela_2022-04-18_111914.png', 682),
(27, 'BENANCIB', '', 1, 1, '0000-00-00', '0000-00-00', 1, '/benancib', 'Base de dados dos Enancibs', 'https://cip.brapci.inf.br/img/logo/logo_benancib.gif', 964),
(28, '14º Conferência Luso-Brasileira de Ciência Aberta', 'Natal, BR', 1, 0, '2023-09-18', '2023-09-21', 0, 'https://confoa.rcaap.pt/2023/', '', 'img/events/logo_ciencia_aberta_2023-e1672916683839.png', 1940),
(29, 'XXIII Enancib <sup>(presencial)</sup>', 'Aracaju', 1, 0, '2023-11-05', '2023-11-11', 0, 'https://eventos.galoa.com.br/enancib-2023/page/2621-inicio', 'https://enancib.ancib.org/index.php/enancib/xxxiiienancib/index', 'https://www.febab.org/cbbu/wp-content/uploads/2021/02/cropped-cropped-Nova-logo-CBBU-1-2-scaled-1.jpg', 2226),
(30, 'XXII SNBU', 'Florianópolis, SC', 1, 0, '2023-11-28', '2023-12-01', 0, 'https://snbu2023.febab.org/', '', 'img/events/snbu.png', 1819),
(31, '9º EBBC - Encontro Brasileiro de Bibliometria e Cientometria', 'Brasília, DF', 1, 0, '2024-07-23', '2024-07-26', 0, 'https://ebbc.inf.br', '', 'https://ebbc.inf.br/ebbc9/wp-content/themes/ebbc/static/img/logos/logo.svg', 157),
(32, 'VII Workshop de Informação, Dados e Tecnologia', 'Porto Velho, RO', 1, 0, '2024-06-25', '2024-06-27', 0, 'https://labcotec.ibict.br/widat/', '', 'img/events/VII_WIDat.png', 196),
(33, '15ª Conferência Lusófona de Ciência Aberta (ConfOA 2024)', 'Porto. Porptugal', 1, 0, '2024-10-01', '2024-10-04', 0, 'https://confoa.rcaap.pt/2024/', '', 'https://i0.wp.com/confoa.rcaap.pt/2024/wp-content/uploads/sites/11/2023/12/cropped-Logo_Ciencia-Aberta_15_transparente.png?fit=200%2C53&ssl=1', 496),
(34, 'Biredial', 'Santiago, Chile', 1, 0, '2024-10-22', '2024-10-24', 0, 'https://biredial.istec.org/', '', 'https://biredial.istec.org/wp-content/uploads/sites/14/2023/12/Portada_Sin-fondo.png', 248),
(35, 'XXIV Encontro Nacional de Pesquisa em Ciência da Informação (Enancib)', 'Vitória, ES', 1, 0, '2024-11-05', '2024-11-08', 0, 'https://ancib.org/sites/enancib2024/', '', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRSjSDvpMwfXw6FIVsqFtM9BcLRjYB47Du4amRBX31QwhkWdfbCKooxGOGceautrtdrdQ&usqp=CAU', 534),
(36, 'Ontobras 2024', 'Porto Alegre, RS', 1, 0, '2024-10-07', '2024-10-10', 0, 'https://www.inf.ufrgs.br/ontobras/', '', 'https://www.inf.ufrgs.br/ontobras/wp-content/uploads/2022/10/cropped-Logo-full-1.png', 184),
(37, 'Fórum Internacional de Editores de Revistas Científicas (EBCII 2024)', 'Brasília, DF', 1, 0, '2024-09-10', '2024-09-13', 0, 'https://ercim.ibict.br/', '', 'https://www.forum.fi/wp-content/uploads/sites/2/2021/12/forum_logo_black_rgb.svg', 251),
(38, 'VI FEISC', 'Hibrido - POA', 1, 1, '2024-10-09', '2024-10-11', 0, 'https://www.ufrgs.br/feisc/feisc6/', 'O 6º FEISC será realizado em formato híbrido, nos dias 9, 10 e 11 de outubro de 2024, na Faculdade de Biblioteconomia e Comunicação da Universidade Federal do Rio Grande do Sul.', 'https://www.ufrgs.br/feisc/feisc6//data/uploads/pageheaderlogoimage_pt_br.png', 176),
(39, 'X SECIN', 'Londrina, PR', 1, 0, '2024-08-19', '2024-08-21', 0, 'http://www.uel.br/eventos/cinf/index.php/secin2024/secin2024', 'O SEMINÁRIO EM CIÊNCIA DA INFORMAÇÃO (SECIN) visa promover o debate acerca dos fenômenos abarcados pela Ciência da Informação e a interação entre professores, estudantes de graduação e pós-graduação, pesquisadores e profissionais ligados à Ciência  da Informação e áreas afins.', 'http://www.uel.br/eventos/cinf/public/conferences/18/pageHeaderTitleImage_pt_BR.png', 111),
(40, 'VIII COAIC', 'Londrina, PR', 1, 1, '2024-08-22', '2024-08-23', 0, 'http://www.uel.br/eventos/cinf/index.php/coaic2024/coaic2024', '', 'http://www.uel.br/eventos/cinf/public/conferences/19/pageHeaderTitleImage_pt_BR.png', 194),
(41, 'XI Colóquio de Pesquisa em Ciência da Informação', 'Natal (Presencial)', 1, 0, '2024-09-24', '2024-09-26', 0, 'https://seminario.ccsa.ufrn.br/subeventos', '', 'img/events/logo-coloquio2024.png', 105),
(42, 'Congresso Brasileiro de Biblioteconomia e Documentação', 'Recife, PE', 1, 0, '2024-11-25', '2024-11-29', 0, 'https://cbbd2024.febab.org/', '', 'https://cbbd2024.febab.org/wp-content/uploads/2024/05/cropped-logo_ccbd-2024-1.png', 171),
(43, 'ISKO Brasil 2024', 'Canela, RS', 1, 0, '2025-06-25', '2025-06-27', 0, 'https://isko.org.br/iskobrasil-2025/', '', 'https://isko.org.br/wp-content/uploads/2021/03/logoISKO_720.png', 208),
(44, 'VII Congresso ISKO Espanha-Portugal', 'Porto, Portugal', 1, 0, '2025-11-13', '2025-11-14', 0, '', '', 'https://cip.brapci.inf.br/img/events/isko-espanha-portugal.jpg', 638),
(45, '11º CEISAL', '', 1, 0, '2025-06-02', '2025-06-04', 0, 'https://www.ceisal2025.com/es/', '', 'https://cip.brapci.inf.br/img/events/logo-ceisal2025.jpg', 227),
(46, '16ª Conferência Lusófona de Ciência Aberta (ConfOA 2025)', 'Goiania, GO', 1, 0, '2025-09-08', '2025-09-11', 0, 'https://confoa.rcaap.pt/2025/', '', 'https://i0.wp.com/confoa.rcaap.pt/2024/wp-content/uploads/sites/11/2024/03/ConfOA2025_logo_recortado.png?ssl=1', 373),
(47, 'XXIII SNBU', 'São Paulo, SP', 1, 0, '2025-11-17', '2025-11-20', 0, 'https://snbu2025.febab.org/', '', 'https://cip.brapci.inf.br/img/events/SNBU2025.png', 274),
(48, 'WIDAT 2025', 'Marília, SP', 1, 0, '2025-05-27', '2025-05-29', 0, 'https://labcotec.ibict.br/widat/index.php/widat2025', '', 'https://cip.brapci.inf.br/img/events/widat2025.png', 218),
(49, 'Biredial', 'Brasília, DF', 1, 0, '2025-10-08', '2025-10-10', 0, 'https://biredial.istec.org/call-for-paper-2025/', '', 'https://biredial.istec.org/wp-content/uploads/sites/14/2019/11/logobi.png', 258),
(50, 'Rede Brasileira de Ripositórios Digitais - RBRD', 'Rio de Janeiro, RJ', 1, 0, '2025-07-03', '2025-07-04', 0, 'http://rbrd.ibict.br/', '', 'https://cip.brapci.inf.br/img/events/logo-rbrd.png', 94),
(51, 'XXV Enancib', 'Rio de Janeiro, RJ', 1, 0, '2025-11-03', '2025-11-07', 0, 'https://enancib2025.ibict.br/', '', 'https://enancib2025.ibict.br/wp-content/uploads/2025/04/LOGO_HOME_cor.png', 263),
(52, 'ISKO Internacional', 'São Paulo', 1, 0, '2026-08-17', '2026-08-19', 19, 'https://www.isko2026.com.br/isko-2026/page/6703-home', '', 'https://static.galoa.com.br/file/Eventmanager-Private/2025-08/Banner%20ISKO%20Ko%20oficial%20%286%29.png?VersionId=4_z9e083e414507696175f50716_f113a9ca7322f7399_d20250815_m204059_c003_v0312029_t0012_u01755290459944', 61),
(53, 'XXVI Enancib', 'Belem, PA', 1, 0, '2026-11-09', '2026-11-13', 0, '', '', 'https://www.freeiconspng.com/thumbs/no-image-icon/no-image-icon-6.png', 230),
(54, 'Conferência Luso-Brasileira de Acesso Aberto (ConfOA)', 'Faro, Portugal', 1, 0, '2026-10-06', '2026-10-09', 0, 'https://confoa.rcaap.pt/2026/', '', 'https://confoa.rcaap.pt/2026/wp-content/uploads/sites/13/2025/11/cropped-ConfOA2026_logo_final_transparente.png', 46),
(55, '10º EBBC - Encontro Brasileiro de Bibliometria e Cientometria', 'Curitiba, PR', 1, 0, '2026-07-22', '2026-07-24', 0, 'https://ebbc.inf.br', '', 'https://ebbc.inf.br/ojs/public/journals/1/pageHeaderLogoImage_pt_BR.png', 44),
(56, 'Dataverse Meeting', 'Barcelona, Espanha', 1, 0, '2026-05-13', '2026-05-15', 0, 'https://dcm2026.com/', '', 'https://dcm2026.com/wp-content/uploads/2025/12/sky-01.svg', 0),
(57, 'Open Repository 2026', 'Virtual', 1, 0, '2026-06-08', '2026-06-11', 0, 'https://or2026.openrepositories.org/', '', 'https://www.freeiconspng.com/thumbs/no-image-icon/no-image-icon-6.png', 0),
(58, 'Biredial - ISTEC', 'Lima, Peru', 1, 0, '2026-10-20', '2026-10-23', 0, 'https://biredial.istec.org/sede-2026/', '', 'https://biredial.istec.org/wp-content/uploads/sites/14/2019/11/logobi.png', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD UNIQUE KEY `id_ev` (`id_ev`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id_ev` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
