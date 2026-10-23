CREATE DATABASE IF NOT EXISTS `brapci_z39.50` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `brapci_z39.50`.`z3950_servers` (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(32) NOT NULL,
    name VARCHAR(255) NOT NULL,
    host VARCHAR(255) NULL,
    port SMALLINT UNSIGNED NOT NULL DEFAULT 210,
    database_name VARCHAR(255) NULL,
    record_syntax VARCHAR(32) NOT NULL DEFAULT 'usmarc',
    last_active DATETIME NULL COMMENT 'Data e hora da última vez que o servidor foi testado',
    active TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Habilitado para coleta',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_z3950_servers_code (code),
    KEY ix_z3950_servers_active (active)
) ENGINE=InnoDB;

INSERT INTO `brapci_z39.50`.`z3950_servers` (code, name) VALUES
('BN', 'Fundação Biblioteca Nacional (BN)'),
('FUNDACENTRO', 'Fundação Jorge Duprat Figueiredo, de Segurança e Medicina do Trabalho (Fundacentro)'),
('RVBI_ALEPH', 'Rede Virtual de Bibliotecas (RVBI), Senado Federal - Aleph'),
('RVBI_FOLIO', 'Rede Virtual de Bibliotecas (RVBI), Senado Federal - Folio'),
('USP', 'Universidade de São Paulo (USP)'),
('UERJ', 'Universidade do Estado do Rio de Janeiro (UERJ)'),
('UNICAMP', 'Universidade Estadual de Campinas (UNICAMP)'),
('UNESP', 'Universidade Estadual Paulista (UNESP)'),
('UFG', 'Universidade Federal de Goiás (UFG)'),
('UFMG', 'Universidade Federal de Minas Gerais (UFMG)'),
('UFPE', 'Universidade Federal de Pernambuco (UFPE)'),
('UFSC', 'Universidade Federal de Santa Catarina (UFSC)'),
('UFSCAR', 'Universidade Federal de São Carlos (UFSCar)'),
('UNIFESP', 'Universidade Federal de São Paulo (UNIFESP)'),
('UFU', 'Universidade Federal de Uberlândia (UFU)'),
('UFPR', 'Universidade Federal do Paraná (UFPR)'),
('UFRGS', 'Universidade Federal do Rio Grande do Sul (UFRGS)'),
('UFF', 'Universidade Federal Fluminense (UFF)'),
('UTFPR', 'Universidade Tecnológica Federal do Paraná (UTFPR)')
ON DUPLICATE KEY UPDATE code = VALUES(code);
