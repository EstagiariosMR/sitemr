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
        <div class="data"><p><em><?= date('d/m/Y H:i', strtotime($noticia['data_publicacao'])); ?></em></p></div>

        <div>
            <p><?= htmlspecialchars($noticia['conteudo']); ?></p>
        </div>

        <?php
        $arquivo = $noticia['arquivo'] ?? null;

        if($arquivo){
            $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));

            if(in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])){
                $src = buscarImagem($arquivo);

                if($src): ?>
                    <img src="<?= htmlspecialchars($src) ?>" alt="Imagem da notícia">
                <?php
                endif;
            }
            elseif($extensao === 'pdf'){
                ?>
                <p>Documento disponível: <a href="<?= htmlspecialchars($arquivo) ?>" target="_blank" rel="noopener noreferrer">Abrir PDF</a></p>
                <?php
            }
        } 
        ?>
    </article>

    <div class="voltar">
        <a href="/sitemr/">Voltar</a>
    </div>
</main>