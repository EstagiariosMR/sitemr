<?php
require 'crud.php';

$ano = isset($_GET['ano']) ? $_GET['ano'] : '';

if(!empty($ano)){
    $turmas = read(
        'trabalhos_integrado',
        'DISTINCT turma',
        'ano = :ano',
        ['ano' => $ano],
        false,
        'turma ASC'
    );

    header('Content-Type: application/json');
    echo json_encode($turmas);
    exit;
}