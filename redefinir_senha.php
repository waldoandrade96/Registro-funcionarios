<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/config.php';

if (isLoggedIn()) {
    header('Location: home.php');
    exit;
}

$token     = trim($_GET['token'] ?? '');
$mensagem  = '';
$tipo      = '';
$tokenValido = false;
$usuarioId   = null;

// Valida o token
if ($token) {
    try {
        $pdo  = getConnection();
        $stmt = $pdo->prepare("
            SELECT rt.id, rt.usuario_id, rt.expira_em, u.nome
            FROM reset_tokens rt
            JOIN usuarios u ON u.id = rt.usuario_id
            WHERE rt.token = :token
              AND rt.usado = FALSE
              AND rt.expira_em > NOW()
            LIMIT 1
        ");
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch();

        if ($row) {
            $tokenValido = true;
            $usuarioId   = $row['usuario_id'];
        } else {
            $mensagem = 'Link inválido ou expirado. Solicite uma nova recuperação de senha.';
            $tipo     = 'danger';
        }
    } catch (\Exception $e) {
        $mensagem = 'Erro ao validar o link. Tente novamente.';
        $tipo     = 'danger';
    }
} else {
    header('Location: index.php');
    exit;
}

// Processa nova senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValido) {
    $nova_senha    = $_POST['nova_senha']    ?? '';
    $conf_senha    = $_POST['conf_senha']    ?? '';

    if (strlen($nova_senha) < 6) {
        $mensagem = 'A senha deve ter pelo menos 6 caracteres.';
        $tipo     = 'danger';
    } elseif ($nova_senha !== $conf_senha) {
        $mensagem = 'As senhas não coincidem.';
        $tipo     = 'danger';
    } else {
        try {
            $pdo  = getConnection();
            $hash = password_hash($nova_senha, PASSWORD_BCRYPT);

            // Atualiza senha
            $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id")
                ->execute([':senha' => $hash, ':id' => $usuarioId]);

            // Invalida token usado
            $pdo->prepare("UPDATE reset_tokens SET usado = TRUE WHERE token = :token")
                ->execute([':token' => $token]);

            $mensagem    = 'Senha redefinida com sucesso! Você já pode fazer login.';
            $tipo        = 'success';
            $tokenValido = false; // Esconde o formulário
        } catch (\Exception $e) {
            $mensagem = 'Erro ao redefinir senha. Tente novamente.';
            $tipo     = 'danger';
            error_log('Erro redefinir senha: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha — Cadastro de Funcionários</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-header">
            <div class="icon"><i class="fa-solid fa-lock-open"></i></div>
            <h1>Nova Senha</h1>
            <p class="text-muted mt-8" style="font-size:13px;">
                Digite e confirme sua nova senha.
            </p>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipo ?>">
                <i class="fa fa-<?= $tipo === 'success' ? 'check-circle' : 'circle-exclamation' ?>"></i>
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <?php if ($tokenValido): ?>
        <form method="POST" action="redefinir_senha.php?token=<?= htmlspecialchars($token) ?>">
            <div class="form-group">
                <div class="input-icon">
                    <i class="fa fa-lock"></i>
                    <input
                        type="password"
                        name="nova_senha"
                        placeholder="Nova senha (mínimo 6 caracteres)"
                        minlength="6"
                        required
                    >
                </div>
            </div>
            <div class="form-group">
                <div class="input-icon">
                    <i class="fa fa-lock"></i>
                    <input
                        type="password"
                        name="conf_senha"
                        placeholder="Confirme a nova senha"
                        minlength="6"
                        required
                    >
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:8px;">
                <i class="fa fa-check"></i> Salvar nova senha
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
