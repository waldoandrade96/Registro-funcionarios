<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$mensagem = '';
$tipoMsg  = '';

// Exclusão via POST (PRG)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'excluir') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            $pdo  = getConnection();
            $stmt = $pdo->prepare("DELETE FROM funcionarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['flash_msg']  = 'Funcionário excluído com sucesso.';
            $_SESSION['flash_tipo'] = 'success';
        } catch (Exception $e) {
            $_SESSION['flash_msg']  = 'Erro ao excluir funcionário.';
            $_SESSION['flash_tipo'] = 'danger';
        }
    }
    header('Location: listagem.php');
    exit;
}

// Flash messages
if (isset($_SESSION['flash_msg'])) {
    $mensagem = $_SESSION['flash_msg'];
    $tipoMsg  = $_SESSION['flash_tipo'];
    unset($_SESSION['flash_msg'], $_SESSION['flash_tipo']);
}

// Parâmetros de busca e paginação
$busca   = trim($_GET['busca'] ?? '');
$porPag  = 5;
$pagina  = max(1, (int)($_GET['pagina'] ?? 1));
$offset  = ($pagina - 1) * $porPag;

try {
    $pdo = getConnection();

    $where  = '';
    $params = [];
    if ($busca !== '') {
        $where    = "WHERE (f.nome ILIKE :b OR f.email ILIKE :b2)";
        $params   = [':b' => "%$busca%", ':b2' => "%$busca%"];
    }

    // Total para paginação
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM funcionarios f $where");
    $stmtCount->execute($params);
    $total     = (int)$stmtCount->fetchColumn();
    $totalPag  = (int)ceil($total / $porPag);

    // Funcionários da página atual
    $sql  = "SELECT f.id, f.nome, c.nome AS cargo, f.email, f.situacao
             FROM funcionarios f
             LEFT JOIN cargos c ON c.id = f.cargo_id
             $where
             ORDER BY f.id
             LIMIT :lim OFFSET :off";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->bindValue(':lim', $porPag, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
    $stmt->execute();
    $funcionarios = $stmt->fetchAll();

} catch (Exception $e) {
    $funcionarios = [];
    $total = $totalPag = 0;
    $mensagem = 'Erro ao conectar ao banco de dados.';
    $tipoMsg  = 'danger';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem — Cadastro de Funcionários</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container page-content">
        <h1 class="page-title">Cadastro de Funcionários</h1>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipoMsg ?>">
                <i class="fa fa-<?= $tipoMsg === 'success' ? 'check-circle' : 'circle-exclamation' ?>"></i>
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <!-- FORMULÁRIO DE CADASTRO (LINK) -->
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-user-tie"></i>
                Cadastro de Funcionários
            </div>
            <div class="card-body" style="text-align:center; padding: 20px;">
                <p style="color: var(--gray-text); margin-bottom: 14px;">
                    Para adicionar um novo funcionário, clique no botão abaixo.
                </p>
                <a href="funcionario.php" class="btn btn-success">
                    <i class="fa fa-plus"></i> Novo Funcionário
                </a>
            </div>
        </div>

        <!-- LISTAGEM -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i>
                Listagem de Funcionários
            </div>
            <div class="card-body">
                <!-- Barra de busca -->
                <form method="GET" action="listagem.php">
                    <div class="table-toolbar">
                        <div class="search-box">
                            <i class="fa fa-magnifying-glass"></i>
                            <input
                                type="text"
                                name="busca"
                                placeholder="Buscar funcionário..."
                                value="<?= htmlspecialchars($busca) ?>"
                            >
                        </div>
                        <div class="table-actions-right">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-magnifying-glass"></i> Pesquisar
                            </button>
                            <a href="funcionario.php" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Novo Funcionário
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Tabela -->
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px">ID</th>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>E-mail</th>
                            <th style="width:90px">Situação</th>
                            <th style="width:110px">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($funcionarios)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; color: var(--gray-text); padding: 32px;">
                                    Nenhum funcionário encontrado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($funcionarios as $i => $f): ?>
                                <tr>
                                    <td><?= $offset + $i + 1 ?>.</td>
                                    <td>
                                        <a href="funcionario.php?id=<?= $f['id'] ?>">
                                            <?= htmlspecialchars($f['nome']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($f['cargo'] ?? '—') ?></td>
                                    <td style="font-style:italic; color: var(--gray-text);">
                                        <?= htmlspecialchars($f['email']) ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($f['situacao']) ?>">
                                            <?= $f['situacao'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="funcionario.php?id=<?= $f['id'] ?>"
                                               class="btn-icon btn-edit"
                                               title="Editar">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                            <a href="mailto:<?= htmlspecialchars($f['email']) ?>"
                                               class="btn-icon btn-email"
                                               title="Enviar e-mail">
                                                <i class="fa fa-envelope"></i>
                                            </a>
                                            <form method="POST" action="listagem.php"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Confirma exclusão de <?= htmlspecialchars(addslashes($f['nome'])) ?>?')">
                                                <input type="hidden" name="action" value="excluir">
                                                <input type="hidden" name="id" value="<?= $f['id'] ?>">
                                                <button type="submit" class="btn-icon btn-delete" title="Excluir">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Paginação -->
                <?php if ($totalPag > 1): ?>
                    <div class="pagination">
                        <?php if ($pagina > 1): ?>
                            <a href="?busca=<?= urlencode($busca) ?>&pagina=<?= $pagina - 1 ?>"
                               class="page-link">
                                &laquo; Anterior
                            </a>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $totalPag; $p++): ?>
                            <a href="?busca=<?= urlencode($busca) ?>&pagina=<?= $p ?>"
                               class="page-link <?= $p === $pagina ? 'active' : '' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($pagina < $totalPag): ?>
                            <a href="?busca=<?= urlencode($busca) ?>&pagina=<?= $pagina + 1 ?>"
                               class="page-link">
                                Próximo &raquo;
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div><!-- /card-body -->
        </div><!-- /card -->

    </div><!-- /container -->
</body>
</html>
