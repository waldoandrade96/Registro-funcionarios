<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/config.php';

if (isLoggedIn()) {
    header('Location: home.php');
    exit;
}

$mensagem       = '';
$tipo           = '';
$email_simulado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');

    if (!$usuario) {
        $mensagem = 'Informe o nome de usuário.';
        $tipo     = 'danger';
    } else {
        try {
            $pdo  = getConnection();
            $stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios WHERE usuario = :u LIMIT 1");
            $stmt->execute([':u' => $usuario]);
            $user = $stmt->fetch();

            // Mensagem genérica por segurança (não revela se usuário existe)
            $mensagem = 'Se o usuário existir, um link de recuperação será gerado abaixo.';
            $tipo     = 'success';

            if ($user) {
                // Gera token seguro
                $token  = bin2hex(random_bytes(32));
                $expira = date('Y-m-d H:i:s', strtotime('+' . RESET_TOKEN_EXPIRY . ' minutes'));

                // Invalida tokens anteriores do usuário
                $pdo->prepare("UPDATE reset_tokens SET usado = TRUE WHERE usuario_id = :id AND usado = FALSE")
                    ->execute([':id' => $user['id']]);

                // Salva novo token
                $pdo->prepare("INSERT INTO reset_tokens (usuario_id, token, expira_em) VALUES (:id, :token, :expira)")
                    ->execute([':id' => $user['id'], ':token' => $token, ':expira' => $expira]);

                // Monta link
                $link = BASE_URL . '/redefinir_senha.php?token=' . $token;

                // Guarda link para exibir na tela (simulação sem PHPMailer)
                $email_simulado = $link;
            }

        } catch (Exception $e) {
            $mensagem = "Erro real: " . $e->getMessage();
            $tipo     = 'danger';
            error_log('Erro recuperação senha: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci minha senha — Cadastro de Funcionários</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-header">
            <div class="icon"><i class="fa-solid fa-key"></i></div>
            <h1>Recuperar Senha</h1>
            <p class="text-muted mt-8" style="font-size:13px;">
                Informe seu usuário para gerar o link de redefinição.
            </p>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipo ?>">
                <i class="fa fa-<?= $tipo === 'success' ? 'check-circle' : 'circle-exclamation' ?>"></i>
                <?= htmlspecialchars($mensagem) ?>
            </div>

            <?php if (!empty($email_simulado)): ?>
                <div style="margin-top:16px;padding:16px 18px;border:1px dashed #2563eb;background-color:rgba(37,99,235,0.06);border-radius:8px;text-align:center;">
                    <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:.05em;color:#2563eb;text-transform:uppercase;">
                        &#128274; Simulação — Link de redefinição
                    </p>
                    <p style="margin:0 0 14px 0;font-size:13px;color:#555;">
                        Em produção este link seria enviado por e-mail. Clique para redefinir sua senha:
                    </p>
                    <a href="<?= htmlspecialchars($email_simulado) ?>" class="btn btn-primary" style="display:inline-block;text-decoration:none;">
                        <i class="fa fa-lock-open"></i> Redefinir minha senha
                    </a>
                    <p style="margin:12px 0 0 0;font-size:11px;color:#999;">
                        Este link expira em <?= RESET_TOKEN_EXPIRY ?> minutos.
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!$mensagem || $tipo === 'danger'): ?>
        <form method="POST" action="esqueci_senha.php">
            <div class="form-group">
                <div class="input-icon">
                    <i class="fa fa-user"></i>
                    <input type="text" name="usuario" placeholder="Usuário" autocomplete="username" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:8px;">
                <i class="fa fa-paper-plane"></i> Enviar
            </button>
        </form>
        <?php endif; ?>

        <hr class="login-divider">
        <div class="login-links">
            <a href="index.php"><i class="fa fa-arrow-left"></i> Voltar ao login</a>
        </div>
    </div>
</body>
</html>
