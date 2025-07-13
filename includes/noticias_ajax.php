<?php
require 'crud.php';

$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$limite_por_pagina = 10;
$offset = ($pagina - 1) * $limite_por_pagina;

$noticias = read(
    "noticias",
    "id, titulo",
    false,
    [],
    false,
    "data_publicacao DESC",
    "LIMIT $limite_por_pagina OFFSET $offset"
);

if($noticias && count($noticias) > 0){
    echo '<div class="noticias-grid">';

    foreach($noticias as $noticia){
        echo '<a href="noticia/' . $noticia['id'] . '"><div class="noticia-item">' . htmlspecialchars($noticia['titulo']) . '</div></a>';
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