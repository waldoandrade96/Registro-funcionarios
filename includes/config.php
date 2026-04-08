<?php
// =============================================
// CONFIGURAÇÕES DO SISTEMA
// =============================================

// URL base do sistema — detectada automaticamente pelo caminho real da pasta
// Se precisar forçar manualmente, substitua por: define('BASE_URL', 'http://localhost/NOME_DA_SUA_PASTA');
$_protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host       = $_SERVER['HTTP_HOST'] ?? 'localhost';
// Pega o nome da pasta raiz do projeto (ex: /Lucas_Dweb/esqueci_senha.php → /Lucas_Dweb)
$_scriptDir  = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Sobe um nível se o script estiver em subpasta (ex: /projeto/includes/)
$_parts      = explode('/', trim($_scriptDir, '/'));
$_base       = '/' . $_parts[0];
define('BASE_URL', $_protocol . '://' . $_host . $_base);

// =============================================
// CONFIGURAÇÕES DE EMAIL (Gmail SMTP)
// =============================================

// Sua conta Gmail que vai ENVIAR os emails
define('MAIL_HOST',     'smtp.gmail.com');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'seuemail@gmail.com');   // ← seu Gmail aqui
define('MAIL_PASSWORD', 'xxxx xxxx xxxx xxxx');  // ← senha de app do Gmail (veja README)
define('MAIL_FROM',     'seuemail@gmail.com');   // ← mesmo Gmail
define('MAIL_FROM_NAME','Cadastro de Funcionários');

// Tempo de expiração do token em minutos
define('RESET_TOKEN_EXPIRY', 60);
