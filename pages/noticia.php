<?php
require 'includes/crud.php';
require 'includes/upload_arquivos.php';

function exibirErroNoticia($mensagem = "Notícia não encontrada."){
    http_response_code(404);
    echo "<main><p style='color: red;'>$mensagem</p></main>";
    return;
}

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    exibirErroNoticia("ID da notícia inválido ou ausente.");
    return;
}

$id = (int) $_GET['id'];

$noticia = read('noticias', '*', 'id = :id', ['id' => $id], true);

if(!$noticia){
    exibirErroNoticia();
    return;
}
?>

<main>
    <article>
        <h1><?= htmlspecialchars($noticia['titulo']); ?></h1>
        <p><em><?= date('d/m/Y H:i', strtotime($noticia['data_publicacao'])); ?></em></p>

        <?php
        $imagem = buscarImagem($noticia['imagem']);
        
        if($imagem): ?>
            <img src="<?= $imagem ?>" alt="Imagem da notícia">
        <?php endif; ?>

        <div>
            <?= htmlspecialchars($noticia['conteudo']); ?>
        </div>
    </article>

    <div>
        <a href="/sitemr/">Voltar</a>
    </div>
</main>