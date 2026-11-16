-- Acrescenta arquivamento de perguntas preservando o acervo e as partidas existentes.
-- Redator: Allan Max
-- Data de criação: 16/11/2026
-- Status: Sem revisão
-- Data do Status: 16/11/2026

CREATE TABLE categorias (
    chave VARCHAR(24) NOT NULL PRIMARY KEY,
    nome VARCHAR(80) NOT NULL,
    UNIQUE KEY categorias_nome_unico (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categorias (chave, nome) VALUES
('geografia', 'Geografia'), ('historia', 'História'), ('ciencias', 'Ciências'),
('esportes', 'Esportes'), ('cinema', 'Cinema'), ('musica', 'Música');

ALTER TABLE perguntas
    ADD COLUMN arquivada TINYINT UNSIGNED NOT NULL DEFAULT 0 CHECK (arquivada IN (0, 1)),
    ADD KEY perguntas_arquivada_tema (arquivada, tema),
    ADD CONSTRAINT perguntas_categoria FOREIGN KEY (tema) REFERENCES categorias (chave);
