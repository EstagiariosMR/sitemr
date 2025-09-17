<?php
ob_start();
session_start();

require 'vendor/autoload.php';
require 'includes/crud.php';
require 'includes/upload_arquivos.php';

if(!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])){
    header('Location: login.php');
    exit;
}

if (isset($_POST['btn_logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$action = $_GET['action'] ?? 'home';

$titulos = [
    'noticias' => 'Notícias',
    'trabalhos' => 'Trabalhos',
    'carrossel' => 'Carrossel',
];

if(isset($titulos[$action])){
    $titulo_pagina = "Painel de Controle - " . $titulos[$action];
}
else{
    $titulo_pagina = "Painel de Controle";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Painel Admin</title>
</head>
<body>
    <header>
        <nav>
            <span><?= htmlspecialchars($titulo_pagina) ?></span>
            <form method="POST" class="admin-logout-form">
                <button type="submit" name="btn_logout" class="admin-logout-btn">Sair</button>
            </form>
        </nav>
    </header>

    <div class="container">
        <aside>
            <nav>
                <ul>
                    <li><a href="admin.php">Início</a></li>
                    <li><a href="admin.php?action=noticias">Notícias</a></li>
                    <li><a href="admin.php?action=trabalhos">Trabalhos</a></li>
                    <li><a href="admin.php?action=carrossel">Carrossel</a></li>
                </ul>
            </nav>
        </aside>

        <main>
        <?php
        $action = $_GET['action'] ?? 'home';

        $paginas_permitidas = ['noticias', 'trabalhos', 'carrossel'];

        $dir_paginas = __DIR__ . '/admin_pages/';

        if(!in_array($action, $paginas_permitidas)){
            $action = 'home';
        }

        $arquivo = $dir_paginas . $action . '.php';

        if(file_exists($arquivo)){
            include $arquivo;
        }
        else{
            echo "<h2>Bem-vindo ao Painel de Controle</h2>";
        }
        ?>
        </main>
    </div>
    
    <script src="assets/js/editor.js"></script>

    <?php ob_end_flush(); ?>
</body>
</html>