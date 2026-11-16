-- Instala o armazenamento do jogo em banco previamente criado pelo painel da hospedagem.
-- Redator: Allan Max
-- Data de criação: 15/11/2026
-- Status: Sem revisão
-- Data do Status: 15/11/2026

SET NAMES utf8mb4;

CREATE TABLE configuracao (
    identificador TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    conteudo LONGTEXT NOT NULL CHECK (JSON_VALID(conteudo)),
    padrao LONGTEXT NOT NULL CHECK (JSON_VALID(padrao))
) ENGINE=InnoDB;

CREATE TABLE perguntas (
    identificador INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tema VARCHAR(24) NOT NULL,
    enunciado VARCHAR(512) NOT NULL,
    alternativas LONGTEXT NOT NULL CHECK (JSON_VALID(alternativas)),
    correta TINYINT UNSIGNED NOT NULL CHECK (correta BETWEEN 0 AND 4)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jogadores (
    identificador BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(16) NOT NULL,
    nome_normalizado VARCHAR(64) COLLATE utf8mb4_bin NOT NULL,
    email VARCHAR(254) DEFAULT NULL,
    senha_hash VARCHAR(255) DEFAULT NULL,
    token CHAR(64) DEFAULT NULL,
    criado_em DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    UNIQUE KEY jogadores_email_unico (email),
    UNIQUE KEY jogadores_token_nome_unico (token, nome_normalizado),
    KEY jogadores_nome_normalizado (nome_normalizado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE partidas (
    identificador CHAR(32) NOT NULL PRIMARY KEY,
    jogador BIGINT UNSIGNED NOT NULL,
    acertos TINYINT UNSIGNED NOT NULL,
    total TINYINT UNSIGNED NOT NULL,
    nivel TINYINT UNSIGNED NOT NULL,
    tempo INT UNSIGNED NOT NULL,
    motivo VARCHAR(16) NOT NULL,
    temas LONGTEXT NOT NULL CHECK (JSON_VALID(temas)),
    criado_em DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    KEY partidas_jogador (jogador),
    CONSTRAINT partidas_jogador_referencia FOREIGN KEY (jogador) REFERENCES jogadores (identificador) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tentativas (
    chave CHAR(64) NOT NULL PRIMARY KEY,
    quantidade INT UNSIGNED NOT NULL,
    inicio BIGINT UNSIGNED NOT NULL
) ENGINE=InnoDB;
