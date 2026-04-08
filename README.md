# Cadastro de Funcionários

Sistema web para cadastro, edição e listagem de funcionários com autenticação, desenvolvido em PHP + PostgreSQL + HTML/CSS puro (sem frameworks).

---

## Tecnologias

- **PHP 5.6+** (compatível com PHP 7/8)
- **PostgreSQL** (via PDO)
- **HTML5 + CSS3** puro
- **Font Awesome 6** (CDN)

---

## Estrutura de Arquivos

```
cadastro-funcionarios/
├── index.php            → Tela de login
├── home.php             → Dashboard (pós-login)
├── listagem.php         → Listagem de funcionários com busca e paginação
├── funcionario.php      → Formulário de cadastro e edição
├── logout.php           → Logout
├── esqueci_senha.php    → Recuperação de senha
├── schema.sql           → Script SQL (criação de tabelas + dados iniciais)
├── assets/
│   └── css/
│       └── style.css    → Estilos globais
└── includes/
    ├── db.php           → Configuração de conexão PostgreSQL (PDO)
    ├── auth.php         → Funções de sessão e autenticação
    └── navbar.php       → Barra de navegação reutilizável
```

---

## Configuração e Instalação

### 1. Banco de dados PostgreSQL

```bash
# Criar banco
psql -U postgres -c "CREATE DATABASE cadastro_funcionarios;"

# Executar schema (cria tabelas e dados iniciais)
psql -U postgres -d cadastro_funcionarios -f schema.sql
```

### 2. Configurar conexão

Edite `includes/db.php` com seus dados:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'cadastro_funcionarios');
define('DB_USER', 'postgres');
define('DB_PASS', 'sua_senha_aqui');  // ← altere aqui
```

### 3. Servidor PHP

```bash
# Opção A: PHP built-in server (desenvolvimento)
cd cadastro-funcionarios
php -S localhost:8080

# Opção B: Apache/Nginx com PHP instalado
# Copie a pasta para htdocs/ ou www/
```

Acesse: `http://localhost:8080`

---

## Credenciais Padrão

| Campo   | Valor    |
|---------|----------|
| Usuário | `admin`  |
| Senha   | `admin123` |

> ⚠️ **Em produção**: altere a senha via `password_hash()` e atualize no banco.

---

## Funcionalidades

| Funcionalidade           | Descrição                                    |
|--------------------------|----------------------------------------------|
| Login / Logout           | Autenticação por sessão PHP                  |
| Esqueci minha senha      | Página de recuperação (placeholder)          |
| Dashboard                | Estatísticas rápidas (total, ativos, inativos) |
| Cadastro de funcionário  | Nome, cargo, e-mail, telefone, situação      |
| Edição de funcionário    | Abre o formulário pré-preenchido             |
| Exclusão                 | Com confirmação via JS                       |
| Busca                    | Por nome ou e-mail (ILIKE)                   |
| Paginação                | 5 registros por página                       |
| PRG Pattern              | Evita reenvio de formulário ao recarregar    |
| Flash messages           | Feedback após salvar/excluir                 |
| Máscara de telefone      | Formatação automática `(XX) XXXXX-XXXX`      |

---

## Segurança

- Senhas armazenadas com `password_hash()` (bcrypt)
- Queries com **PDO + prepared statements** (sem SQL injection)
- Saída HTML com `htmlspecialchars()` (sem XSS)
- Sessão verificada em todas as páginas protegidas
- PRG pattern em formulários de mutação

---

## Publicação no GitHub

```bash
# 1. Inicializar repositório
git init
git add .
git commit -m "feat: sistema de cadastro de funcionários em PHP + PostgreSQL"

# 2. Criar repositório no GitHub e conectar
git remote add origin https://github.com/seu-usuario/cadastro-funcionarios.git
git branch -M main
git push -u origin main
```

> **Atenção**: Antes de publicar, certifique-se de **não commitar credenciais reais** no `db.php`. Use um `.env` ou variáveis de ambiente em produção.

---

## .gitignore sugerido

```
# Ignorar configurações sensíveis
includes/db.php

# Ignorar arquivos do sistema
.DS_Store
Thumbs.db
*.log
```
