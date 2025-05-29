<?php
include 'partials/header.php';

$page = $_GET['page'] ?? 'home';

$arquivo = "pages/{$page}.php";

if(file_exists($arquivo)){
    include $arquivo;
}
else{
    include 'pages/404.php';
}

include 'partials/footer.php';
?>