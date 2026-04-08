<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

// Estatísticas resumidas
try {
    $pdo   = getConnection();
    $total = $pdo->query("SELECT COUNT(*) FROM funcionarios")->fetchColumn();
    $ativo = $pdo->query("SELECT COUNT(*) FROM funcionarios WHERE situacao = 'Ativo'")->fetchColumn();
    $inat  = $pdo->query("SELECT COUNT(*) FROM funcionarios WHERE situacao = 'Inativo'")->fetchColumn();
} catch (Exception $e) {
    $total = $ativo = $inat = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início — Cadastro de Funcionários</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--white);
            border-radius: 8px;
            padding: 22px 24px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--white);
        }
        .stat-icon.blue  { background: var(--blue-main); }
        .stat-icon.green { background: var(--green); }
        .stat-icon.red   { background: var(--red); }
        .stat-num  { font-size: 32px; font-weight: 800; color: var(--text-dark); line-height: 1; }
        .stat-lbl  { font-size: 12px; color: var(--gray-text); margin-top: 4px; font-weight: 500; }
        .welcome-card {
            background: var(--blue-dark);
            color: var(--white);
            border-radius: 8px;
            padding: 28px 32px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .welcome-card h2 { font-size: 20px; margin-bottom: 6px; }
        .welcome-card p  { opacity: .75; font-size: 13.5px; }
        .welcome-cta { display: flex; gap: 10px; }
        .btn-white {
            background: var(--white);
            color: var(--blue-dark);
            padding: 10px 20px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
        }
        .btn-white:hover { opacity: .9; text-decoration: none; color: var(--blue-dark); }
        .btn-outline-white {
            background: transparent;
            color: var(--white);
            padding: 10px 20px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 13px;
            border: 2px solid rgba(255,255,255,.5);
        }
        .btn-outline-white:hover { border-color: var(--white); text-decoration: none; color: var(--white); }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container page-content">
        <div class="welcome-card">
            <div>
                <h2>Bem-vindo, <?= htmlspecialchars(getCurrentUser()) ?>!</h2>
                <p>Gerencie os funcionários da sua empresa de forma simples e eficiente.</p>
            </div>
            <div class="welcome-cta">
                <a href="funcionario.php" class="btn-white">
                    <i class="fa fa-plus"></i> Novo Funcionário
                </a>
                <a href="listagem.php" class="btn-outline-white">
                    Ver Listagem
                </a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-users"></i></div>
                <div>
                    <div class="stat-num"><?= $total ?></div>
                    <div class="stat-lbl">Total de Funcionários</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-user-check"></i></div>
                <div>
                    <div class="stat-num"><?= $ativo ?></div>
                    <div class="stat-lbl">Funcionários Ativos</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-user-slash"></i></div>
                <div>
                    <div class="stat-num"><?= $inat ?></div>
                    <div class="stat-lbl">Funcionários Inativos</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fa fa-circle-info"></i>
                Sobre o Sistema
            </div>
            <div class="card-body">
                <p style="color: var(--gray-text); line-height: 1.7; font-size: 13.5px;">
                    Este sistema permite o <strong>cadastro, edição e listagem</strong> de funcionários,
                    com controle de situação (Ativo / Inativo), busca por nome ou e-mail, paginação
                    e autenticação por usuário e senha. Utilize o menu superior para navegar entre as seções.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
