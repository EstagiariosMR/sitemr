<?php
require 'includes/crud.php';

$imagens = read('carrossel', '*', false, [], false, 'ordem');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrossel Dinâmico</title>
    <link rel="stylesheet" href="assets/css/carrossel.css">
</head>
<body>
    <div class="carrossel">
        <div class="carrossel-slides">
            <?php if(!empty($imagens)): ?>
                <?php foreach($imagens as $img): ?>
                    <div class="slide">
                        <img src="<?= htmlspecialchars($img['imagem']) ?>" alt="<?= htmlspecialchars($img['titulo']) ?>">
                    </div>
                <?php endforeach; ?>

                <div class="slide">
                    <img src="<?= htmlspecialchars($imagens[0]['imagem']) ?>" alt="<?= htmlspecialchars($imagens[0]['titulo']) ?>">
                </div>
            <?php else: ?>
                <p>Nenhuma imagem cadastrada no carrossel</p>
            <?php endif; ?>
        </div>

        <div class="carrossel-controles">
            <button class="carrossel-anterior">Anterior</button>
            <button class="carrossel-proximo">Próximo</button>
        </div>
    </div>

    <script src="assets/js/carrossel.js"></script>
</body>
</html>