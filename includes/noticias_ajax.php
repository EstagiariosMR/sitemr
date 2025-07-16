<?php
require 'crud.php';

$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$limite_por_pagina = 12;
$offset = ($pagina - 1) * $limite_por_pagina;

$noticias = read(
    "noticias",
    "id, titulo, conteudo, arquivo",
    false,
    [],
    false,
    "data_publicacao DESC",
    "LIMIT $limite_por_pagina OFFSET $offset"
);

function obterCapa($arquivo){
    if(empty($arquivo)){
        return 'assets/img/capa-padrao.jpg';
    }

    $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
    $extensoesImagem = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if(in_array($extensao, $extensoesImagem)){
        return $arquivo;
    }

    return 'assets/img/imagem1.png';
}

function gerarIntroducao($conteudo, $minCaracteres=80, $maxCaracteres=160){
    $textoLimpo = strip_tags($conteudo);

    if(mb_strlen($textoLimpo) <= $maxCaracteres){
        return $textoLimpo;
    }

    $pos = mb_strpos($textoLimpo, '.', $minCaracteres);

    if($pos !== false && $pos < $maxCaracteres){
        return mb_substr($textoLimpo, 0, $pos + 1);
    }

    $corte = mb_substr($textoLimpo, 0, $maxCaracteres);
    $ultimoEspaco = mb_strrpos($corte, ' ');

    return mb_substr($corte, 0, $ultimoEspaco) . '...';
}

if($noticias && count($noticias) > 0){
    echo '<div class="noticias-grid">';

    foreach($noticias as $noticia){
        $capa = obterCapa($noticia['arquivo']);
        $introducao = gerarIntroducao($noticia['conteudo']);

        echo '<a href="noticia/' . $noticia['id'] . '">';
        echo '<div class="noticia-item">';
        echo '<img src="' . $capa . '" alt="Capa">';
        echo '<div><strong>' . htmlspecialchars($noticia['titulo']) . '</strong></div>';
        echo '<div>' . htmlspecialchars($introducao) . '</div>';
        echo '</div></a>';
    }

    echo '</div>';
}
else{
    echo '<p>Nenhuma notícia encontrada.</p>';
}

$totalRegistros = countAll('noticias');
$totalPaginas = ceil($totalRegistros / $limite_por_pagina);

if($totalPaginas > 1){
    echo '<nav class="paginacao"><ul>';

    // Botão Anterior
    $prev = $pagina - 1;
    $disabledPrev = $pagina <= 1 ? 'style="pointer-events:none;opacity:0.5;"' : '';
    echo '<li><a href="#" class="anterior" ' . $disabledPrev . ' onclick="carregarPagina(' . $prev . '); return false;">Anterior</a></li>';

    // Números das páginas
    for($i=1; $i<=$totalPaginas; $i++){
        $active = $i == $pagina ? ' ativa' : '';
        echo '<li><a href="#" class="pagina' . $active . '" onclick="carregarPagina(' . $i . '); return false;">' . $i . '</a></li>';
    }

    // Botão Próxima
    $next = $pagina + 1;
    $disabledNext = $pagina >= $totalPaginas ? 'style="pointer-events:none;opacity:0.5;"' : '';
    echo '<li><a href="#" class="proxima" ' . $disabledNext . ' onclick="carregarPagina(' . $next . '); return false;">Próxima</a></li>';

    echo '</ul></nav>';
}