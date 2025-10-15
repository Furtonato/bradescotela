<?php
session_name('ADMINSESS');
session_set_cookie_params([
    'lifetime' => 604800,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

if (
    !(
        (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) ||
        (isset($_SESSION['admin']) && $_SESSION['admin'])
    )
) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../../includes/db.php';
date_default_timezone_set('America/Sao_Paulo');

// Inclui o cabeçalho da página
require __DIR__ . '/partials/_head.php';
?>

<?php require __DIR__ . '/partials/modal_novidades_admin.php'; ?>

<body>

<?php
// 1. Inclui o menu mobile (offcanvas). Sua posição aqui não afeta o layout principal.
require __DIR__ . '/partials/_offcanvas_mobile.php';
?>

<div class="dashboard">

    <?php
    // 2. Inclui o sidebar do desktop DENTRO da div "dashboard" para que o layout flex funcione.
    require __DIR__ . '/partials/_sidebar_desktop.php';
    ?>

    <div class="main">
        <div class="frase-dia">
            <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span class="frase-texto">💡 Não pare até se orgulhar.</span>
            <div class="avatar-menu dropdown">
                <a href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar"><i class="bi bi-person-fill"></i></div>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                    <li><a class="dropdown-item" href="#">Perfil</a></li>
                    <li><a class="dropdown-item" href="#">Alterar Senha</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>

        <div class="client-grid" id="cards-container"></div>
    </div>
</div>



<?php
// Inclui o sistema de chat e os modais
include 'chat_admin.php';
require __DIR__ . '/partials/_modals.php';
require __DIR__ . '/partials/_modalscartao.php';
?>

<?php
// Inclui todos os scripts JS no final
require __DIR__ . '/partials/_scripts.php';
?>



</body>
</html>