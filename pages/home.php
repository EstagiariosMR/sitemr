<?php
require 'includes/crud.php';

$imagens = read('carrossel', '*', false, [], false, 'ordem');
?>

<main class="homet">

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

    <div class="titulo_noticias">
    <h1>Noticias Maria Rocha</h1>
    </div>

    <br><br>

    <div id="noticia-box"></div>
    
</main>

<script src="assets/js/carrossel.js"></script>
<script src="assets/js/paginacao.js"></script>