<?php
require 'includes/crud.php';

$imagens = read('carrossel', '*', false, [], false, 'ordem');
?>

<main class="homet">

    <div class="container-carrossel">
        <div class="carrossel-trilho">
            <?php if(!empty($imagens)): ?>
                <?php foreach($imagens as $img): ?>
                    <div class="carrossel-item">
                        <img src="<?= htmlspecialchars($img['imagem']) ?>" 
                            alt="<?= htmlspecialchars($img['titulo']) ?>">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="carrossel-item carrossel-item-vazio">
                    <div class="aviso-tecnico">
                        <span class="icone-placeholder">🖼️</span>
                        <p>Aguardando inserção de mídias no Banco de Dados.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="controles-setas">
            <button class="seta seta-esquerda"></button>
            <button class="seta seta-direita"></button>
        </div>

        <div class="controles-dots"></div>
    </div>

    <div class="titulo_noticias">
    <h1>Noticias Maria Rocha</h1>
    </div>

    <br><br>

    <div id="noticia-box"></div>
    
</main>

<script src="assets/js/carrossel.js"></script>
<script src="assets/js/filtroNoticias.js"></script>