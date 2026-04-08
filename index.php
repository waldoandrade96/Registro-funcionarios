<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Redireciona se já logado
if (isLoggedIn()) {
    header('Location: home.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    if ($usuario && $senha) {
        try {
            $pdo  = getConnection();
            $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuarios WHERE usuario = :u LIMIT 1");
            $stmt->execute([':u' => $usuario]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {
                $_SESSION['usuario_id']   = $user['id'];
                $_SESSION['usuario_nome'] = $user['nome'];
                header('Location: home.php');
                exit;
            } else {
                $erro = 'Usuário ou senha inválidos.';
            }
        } catch (Exception $e) {
            $erro = "Erro real do banco: " . $e->getMessage();
        }
    } else {
        $erro = 'Preencha usuário e senha.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Cadastro de Funcionários</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-header">
            <div class="icon"><i class="fa-solid fa-user-tie"></i></div>
            <h1>Cadastro de Funcionários</h1>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-danger">
                <i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="form-group">
                <div class="input-icon">
                    <i class="fa fa-user"></i>
                    <input
                        type="text"
                        name="usuario"
                        placeholder="Usuário"
                        value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
                        autocomplete="username"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <div class="input-icon">
                    <i class="fa fa-lock"></i>
                    <input
                        type="password"
                        name="senha"
                        placeholder="Senha"
                        autocomplete="current-password"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top:8px;">
                Entrar
            </button>
        </form>

        <hr class="login-divider">

        <div class="login-links">
            <a href="esqueci_senha.php">Esqueci minha senha</a>
        </div>
    </div>
</body>
</html>
