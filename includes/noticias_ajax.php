<?php
require 'crud.php';

$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$limite_por_pagina = 5;
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
    echo '<ul>';

    foreach($noticias as $noticia){
        echo '<li><a href="noticia/' . $noticia['id'] . '">' . htmlspecialchars($noticia['titulo']) . '</a></li>';
    }

    echo '</ul>';
}
else{
    echo '<p>Nenhuma notícia encontrada.</p>';
}

$totalRegistros = countAll('noticias');
$totalPaginas = ceil($totalRegistros / $limite_por_pagina);

if($totalPaginas > 1){
    echo '<div id="paginacao">';

    for($i=1; $i<=$totalPaginas; $i++){
        echo '<a href="#" onclick="carregarPagina(' . $i . '); return false;">' . $i . '</a>';
    }

    echo '</div>';
}