-- =============================================
-- MIGRATION: Recuperação de senha
-- Execute uma vez no banco cadastro_funcionarios
-- =============================================

-- 1. Adiciona coluna email na tabela usuarios (se não existir)
ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS email VARCHAR(200);

-- 2. Atualiza o admin com um email real (ALTERE PARA SEU EMAIL)
UPDATE usuarios SET email = 'seuemail@gmail.com' WHERE usuario = 'admin';

-- 3. Cria tabela de tokens de recuperação de senha
CREATE TABLE IF NOT EXISTS reset_tokens (
    id          SERIAL PRIMARY KEY,
    usuario_id  INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    token       VARCHAR(64) NOT NULL UNIQUE,
    expira_em   TIMESTAMP NOT NULL,
    usado       BOOLEAN DEFAULT FALSE,
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índice para busca rápida por token
CREATE INDEX IF NOT EXISTS idx_reset_tokens_token ON reset_tokens(token);
