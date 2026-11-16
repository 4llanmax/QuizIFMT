-- Instala identidades administrativas, clientes OAuth e credenciais MCP com resumos opacos.
-- Redator: Allan Max
-- Data de criação: 16/11/2026
-- Status: Sem revisão
-- Data do Status: 16/11/2026

CREATE TABLE usuarios_mcp (
    identificador BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    administrador TINYINT UNSIGNED NOT NULL DEFAULT 0,
    ativo TINYINT UNSIGNED NOT NULL DEFAULT 1,
    senha_inicial TINYINT UNSIGNED NOT NULL DEFAULT 1,
    criado_em DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    UNIQUE KEY usuarios_mcp_email_unico (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE clientes_mcp (
    identificador CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    nome VARCHAR(80) NOT NULL,
    redirecionamentos LONGTEXT NOT NULL CHECK (JSON_VALID(redirecionamentos)),
    criado_em DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE codigos_mcp (
    resumo CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    usuario BIGINT UNSIGNED NOT NULL,
    cliente CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    redirecionamento VARCHAR(2048) NOT NULL,
    desafio CHAR(43) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    recurso VARCHAR(2048) NOT NULL,
    expira BIGINT UNSIGNED NOT NULL,
    CONSTRAINT codigos_mcp_usuario FOREIGN KEY (usuario) REFERENCES usuarios_mcp (identificador) ON DELETE CASCADE,
    CONSTRAINT codigos_mcp_cliente FOREIGN KEY (cliente) REFERENCES clientes_mcp (identificador) ON DELETE CASCADE,
    KEY codigos_mcp_expira (expira)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tokens_mcp (
    resumo CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    renovacao CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    usuario BIGINT UNSIGNED NOT NULL,
    cliente CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    recurso VARCHAR(2048) NOT NULL,
    expira BIGINT UNSIGNED NOT NULL,
    renovacao_expira BIGINT UNSIGNED NOT NULL,
    revogado TINYINT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY tokens_mcp_renovacao_unica (renovacao),
    CONSTRAINT tokens_mcp_usuario FOREIGN KEY (usuario) REFERENCES usuarios_mcp (identificador) ON DELETE CASCADE,
    CONSTRAINT tokens_mcp_cliente FOREIGN KEY (cliente) REFERENCES clientes_mcp (identificador) ON DELETE CASCADE,
    KEY tokens_mcp_expira (renovacao_expira)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
