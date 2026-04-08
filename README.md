# Registro de Funcionários 🏢

##  Tecnologias Utilizadas

O projeto foi desenvolvido utilizando a seguinte stack:

* **Front-end:** HTML5, CSS3
* **Back-end:** PHP (8.x)
* **Banco de Dados:** PostgreSQL 17
* **Comunicação DB:** PDO (PHP Data Objects)
* **Segurança:** Hashing de senhas nativo do PHP (`bcrypt` via `password_hash`)

##  Pré-requisitos

Antes de começar, você precisará ter instalado em sua máquina as seguintes ferramentas:
* Um servidor web local (Apache via XAMPP)
* PHP (versão 8.0 ou superior)
* PostgreSQL (versão 17 ou superior)

##  Instalação e Configuração

Siga os passos abaixo para rodar o projeto localmente:

### 1. Preparando o ambiente PHP (Ativar PDO)
Para que o PHP consiga se comunicar com o PostgreSQL, é necessário habilitar os drivers PDO.
1. Abra o seu arquivo `php.ini` (no XAMPP, fica em Config > PHP).
2. Procure e remova o ponto e vírgula (`;`) do início das seguintes linhas:
   ```ini
   extension=pdo_pgsql
   extension=pgsql
Salve o arquivo e reinicie o seu servidor Apache.

2. Configurando o Banco de Dados
Abra o seu gerenciador de banco de dados (ex: pgAdmin, DBeaver) e conecte-se ao seu servidor PostgreSQL.

Crie um novo banco de dados chamado cadastro_funcionarios.

Execute o conteúdo do arquivo schema.sql fornecido para criar a estrutura inicial.

Atualização Estrutural (Importante): Para que a recuperação de senha funcione corretamente, adicione a coluna de e-mail e defina um contato para o administrador executando os seguintes comandos SQL:

SQL
ALTER TABLE usuarios ADD COLUMN email VARCHAR(255);
UPDATE usuarios SET email = 'admin@teste.com' WHERE usuario = 'admin';
3. Configurando a Conexão no Projeto
Clone ou copie a pasta do projeto para o diretório público do seu servidor web (ex: htdocs no XAMPP ou www no WAMP).

Navegue até o arquivo includes/db.php.

Verifique e atualize as credenciais de acesso ao banco de dados:

PHP
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'cadastro_funcionarios');
define('DB_USER', 'postgres');
define('DB_PASS', 'sua_senha_aqui');
Acesso e Recuperação de Senha
O sistema utiliza criptografia unidirecional para a segurança das senhas.

Primeiro Acesso
Usuário: admin

Senha: (Definida na inserção inicial do banco de dados. Caso tenha perdido o acesso, gere um novo hash com password_hash() e atualize a tabela diretamente).



