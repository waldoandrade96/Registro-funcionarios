<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$id      = (int)($_GET['id'] ?? 0);
$erros   = [];
$sucesso = '';

// Carrega cargos
try {
    $pdo    = getConnection();
    $cargos = $pdo->query("SELECT id, nome FROM cargos ORDER BY nome")->fetchAll();
} catch (Exception $e) {
    $cargos = [];
}

// Valores padrão
$func = [
    'nome'     => '',
    'cargo_id' => '',
    'email'    => '',
    'telefone' => '',
    'situacao' => 'Ativo',
];

// Modo edição: carrega dados do funcionário
if ($id > 0) {
    try {
        $pdo  = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if ($row) {
            $func = $row;
        } else {
            header('Location: listagem.php');
            exit;
        }
    } catch (Exception $e) {
        $erros[] = 'Erro ao carregar funcionário.';
    }
}

// Salvar (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao     = $_POST['acao'] ?? 'salvar';

    if ($acao === 'limpar') {
        header('Location: funcionario.php' . ($id ? "?id=$id" : ''));
        exit;
    }
    if ($acao === 'voltar' || $acao === 'fechar') {
        header('Location: listagem.php');
        exit;
    }

    // Captura e sanitização
    $func['nome']     = trim($_POST['nome'] ?? '');
    $func['cargo_id'] = (int)($_POST['cargo_id'] ?? 0) ?: null;
    $func['email']    = trim($_POST['email'] ?? '');
    $func['telefone'] = trim($_POST['telefone'] ?? '');
    $func['situacao'] = ($_POST['situacao'] ?? 'Ativo') === 'Inativo' ? 'Inativo' : 'Ativo';

    // Validação
    if (!$func['nome'])  $erros[] = 'O campo Nome é obrigatório.';
    if (!$func['email']) $erros[] = 'O campo E-mail é obrigatório.';
    elseif (!filter_var($func['email'], FILTER_VALIDATE_EMAIL))
        $erros[] = 'E-mail inválido.';

    if (empty($erros)) {
        try {
            $pdo = getConnection();

            if ($id > 0) {
                // Atualizar
                $stmt = $pdo->prepare("
                    UPDATE funcionarios
                    SET nome = :nome,
                        cargo_id = :cargo_id,
                        email = :email,
                        telefone = :telefone,
                        situacao = :situacao,
                        atualizado_em = CURRENT_TIMESTAMP
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':nome'     => $func['nome'],
                    ':cargo_id' => $func['cargo_id'],
                    ':email'    => $func['email'],
                    ':telefone' => $func['telefone'],
                    ':situacao' => $func['situacao'],
                    ':id'       => $id,
                ]);
                $_SESSION['flash_msg']  = 'Funcionário atualizado com sucesso.';
                $_SESSION['flash_tipo'] = 'success';
            } else {
                // Inserir
                $stmt = $pdo->prepare("
                    INSERT INTO funcionarios (nome, cargo_id, email, telefone, situacao)
                    VALUES (:nome, :cargo_id, :email, :telefone, :situacao)
                ");
                $stmt->execute([
                    ':nome'     => $func['nome'],
                    ':cargo_id' => $func['cargo_id'],
                    ':email'    => $func['email'],
                    ':telefone' => $func['telefone'],
                    ':situacao' => $func['situacao'],
                ]);
                $_SESSION['flash_msg']  = 'Funcionário cadastrado com sucesso.';
                $_SESSION['flash_tipo'] = 'success';
            }

            header('Location: listagem.php');
            exit;

        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'unique') !== false || strpos($e->getMessage(), 'duplicate') !== false) {
                $erros[] = 'Este e-mail já está cadastrado.';
            } else {
                $erros[] = 'Erro ao salvar: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? 'Editar' : 'Novo' ?> Funcionário — Cadastro de Funcionários</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container page-content">
        <h1 class="page-title">Cadastro de Funcionários</h1>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <?php foreach ($erros as $e): ?>
                    <div><i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-user-tie"></i>
                <?= $id ? 'Editar Funcionário' : 'Cadastro de Funcionários' ?>
            </div>

            <div class="card-body">
                <form method="POST" action="funcionario.php<?= $id ? "?id=$id" : '' ?>">

                    <div class="form-row">
                        <!-- ID -->
                        <div class="form-group">
                            <label>ID</label>
                            <input
                                type="text"
                                class="form-control readonly"
                                value="<?= $id ? $id : 'Automático' ?>"
                                disabled
                            >
                        </div>

                        <!-- Cargo -->
                        <div class="form-group">
                            <label>Cargo</label>
                            <select name="cargo_id" class="form-control">
                                <option value="">Cargo</option>
                                <?php foreach ($cargos as $c): ?>
                                    <option value="<?= $c['id'] ?>"
                                        <?= ($func['cargo_id'] == $c['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Nome -->
                        <div class="form-group">
                            <label>Nome <span style="color:var(--red)">*</span></label>
                            <input
                                type="text"
                                name="nome"
                                class="form-control"
                                placeholder="Nome"
                                value="<?= htmlspecialchars($func['nome']) ?>"
                                required
                            >
                        </div>

                        <!-- E-mail -->
                        <div class="form-group">
                            <label>E-mail <span style="color:var(--red)">*</span></label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="E-mail"
                                value="<?= htmlspecialchars($func['email']) ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Telefone -->
                        <div class="form-group">
                            <label>Telefone</label>
                            <input
                                type="text"
                                name="telefone"
                                class="form-control"
                                placeholder="Telefone"
                                value="<?= htmlspecialchars($func['telefone']) ?>"
                                id="telefoneInput"
                            >
                        </div>

                        <!-- Situação -->
                        <div class="form-group">
                            <label>Situação</label>
                            <div class="radio-group">
                                <label>
                                    <input
                                        type="radio"
                                        name="situacao"
                                        value="Ativo"
                                        <?= ($func['situacao'] === 'Ativo') ? 'checked' : '' ?>
                                    >
                                    Ativo
                                </label>
                                <label>
                                    <input
                                        type="radio"
                                        name="situacao"
                                        value="Inativo"
                                        <?= ($func['situacao'] === 'Inativo') ? 'checked' : '' ?>
                                    >
                                    Inativo
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="form-actions">
                        <button type="submit" name="acao" value="salvar" class="btn btn-success">
                            <i class="fa fa-floppy-disk"></i> Salvar
                        </button>
                        <button type="submit" name="acao" value="limpar" class="btn btn-secondary">
                            <i class="fa fa-eraser"></i> Limpar
                        </button>
                        <button type="submit" name="acao" value="voltar" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </button>
                        <button type="submit" name="acao" value="fechar" class="btn btn-secondary">
                            <i class="fa fa-xmark"></i> Fechar
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
    // Máscara simples de telefone
    (function(){
        const tel = document.getElementById('telefoneInput');
        if (!tel) return;
        tel.addEventListener('input', function(){
            let v = this.value.replace(/\D/g, '').slice(0, 11);
            if (v.length > 6) {
                v = '(' + v.slice(0,2) + ') ' + v.slice(2,7) + '-' + v.slice(7);
            } else if (v.length > 2) {
                v = '(' + v.slice(0,2) + ') ' + v.slice(2);
            } else if (v.length > 0) {
                v = '(' + v;
            }
            this.value = v;
        });
    })();
    </script>
</body>
</html>
