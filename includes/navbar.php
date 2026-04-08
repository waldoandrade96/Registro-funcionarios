<?php
// Determina a página ativa
$paginaAtual = basename($_SERVER['PHP_SELF'], '.php');
?>
<nav class="navbar">
    <div class="navbar-brand">
        <i class="fa-solid fa-globe"></i>
        Cadastro de Funcionários
    </div>

    <div class="navbar-nav">
        <a href="home.php" class="nav-link <?= $paginaAtual === 'home' ? 'active' : '' ?>">
            Início
        </a>
        <a href="listagem.php" class="nav-link <?= in_array($paginaAtual, ['listagem','funcionario']) ? 'active' : '' ?>">
            Listagem
        </a>
    </div>

    <div class="navbar-user">
        <button class="user-menu-btn" id="userMenuBtn">
            Olá, <?= htmlspecialchars(getCurrentUser()) ?> <i class="fa fa-chevron-down" style="font-size:11px"></i>
        </button>
        <div class="dropdown-menu" id="userDropdown">
            <a href="logout.php" class="dropdown-item">
                <i class="fa fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>
</nav>

<script>
(function(){
    const btn = document.getElementById('userMenuBtn');
    const menu = document.getElementById('userDropdown');
    btn.addEventListener('click', function(e){
        e.stopPropagation();
        menu.classList.toggle('open');
    });
    document.addEventListener('click', function(){
        menu.classList.remove('open');
    });
})();
</script>
