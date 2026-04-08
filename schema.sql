-- =============================================
-- SCHEMA: Cadastro de Funcionários
-- Banco: PostgreSQL
-- =============================================

-- Tabela de usuários do sistema (autenticação)
CREATE TABLE IF NOT EXISTS usuarios (
    id          SERIAL PRIMARY KEY,
    usuario     VARCHAR(100) NOT NULL UNIQUE,
    senha       VARCHAR(255) NOT NULL,
    nome        VARCHAR(150) NOT NULL,
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de cargos
CREATE TABLE IF NOT EXISTS cargos (
    id    SERIAL PRIMARY KEY,
    nome  VARCHAR(100) NOT NULL UNIQUE
);

-- Tabela de funcionários
CREATE TABLE IF NOT EXISTS funcionarios (
    id          SERIAL PRIMARY KEY,
    nome        VARCHAR(150) NOT NULL,
    cargo_id    INTEGER REFERENCES cargos(id),
    email       VARCHAR(200) NOT NULL UNIQUE,
    telefone    VARCHAR(20),
    situacao    VARCHAR(10) NOT NULL DEFAULT 'Ativo' CHECK (situacao IN ('Ativo', 'Inativo')),
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- DADOS INICIAIS
-- =============================================

-- Usuário admin padrão (senha: admin123)
INSERT INTO usuarios (usuario, senha, nome)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador')
ON CONFLICT (usuario) DO NOTHING;

-- Cargos padrão
INSERT INTO cargos (nome) VALUES
    ('Administrador'),
    ('Gerente'),
    ('Assistente'),
    ('Analista'),
    ('Desenvolvedor'),
    ('Recepcionista'),
    ('Coordenador')
ON CONFLICT (nome) DO NOTHING;

-- Funcionários de exemplo
INSERT INTO funcionarios (nome, cargo_id, email, telefone, situacao) VALUES
    ('João Silva',    1, 'jo@mi@ensx.com',    '(61) 99999-0001', 'Ativo'),
    ('Ana Mendes',    2, 'repca@ensx.com',    '(61) 99999-0002', 'Ativo'),
    ('Pedro Souza',   3, 'souza@ensx.com',    '(61) 99999-0003', 'Ativo'),
    ('Carla Oliveira',1, 'robog@ensx.com',    '(61) 99999-0004', 'Ativo'),
    ('Lucas Martins', 3, 'lucas@ensx.com',    '(61) 99999-0005', 'Inativo')
ON CONFLICT (email) DO NOTHING;
