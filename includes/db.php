<?php
// $host = 'localhost';
// $db = 'escola';
// $user = 'root';
// $pass = '';

$host = 'ftp.mariarocha.org.br';
$db = 'mariarocha04';
$user = 'mariarocha04';
$pass = 'tecnico2025';

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try{
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}
catch(PDOException $e){
    die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}