<?php
// =========================================================================
// CONTROLE DE AMBIENTE: Defina como 'mamp', 'xampp' ou 'producao'
// =========================================================================
$ambiente = 'mamp'; 
// =========================================================================

$charset = 'utf8mb4';

switch ($ambiente) {
    case 'mamp':
        // Configuração atual do seu Mac M3
        $host = 'localhost';
        $port = '8889';
        $db   = 'escola';
        $user = 'root';
        $pass = 'root';
        break;

    case 'xampp':
        // Configuração padrão para quando rodar no Windows/XAMPP
        $host = 'localhost';
        $port = '3306';
        $db   = 'escola';
        $user = 'root';
        $pass = ''; // Senha vazia do XAMPP
        break;

    case 'producao':
        // Credenciais da hospedagem oficial da escola
        $host = 'mysql.mariarocha.org.br';
        $port = '3306'; // Geralmente a padrão, mas se a hospedagem usar outra é só mudar aqui
        $db   = 'mariarocha04';
        $user = 'mariarocha04';
        $pass = 'tecnico2025';
        break;
}

// Montagem dinâmica da DSN incluindo a porta correta de cada ambiente
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

try{
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}
catch(PDOException $e){
    die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}