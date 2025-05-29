<?php
session_start();
require 'includes/crud.php';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])){
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $where = "email = :email AND senha = :senha AND tipo = :tipo";

    $valores = [
        'email' => $email,
        'senha' => $senha,
        'tipo' => 'admin'
    ];

    $resultado = read('usuarios', '*', $where, $valores, true);

    if($resultado){
        unset($resultado['senha']);
        $_SESSION['usuario'] = $resultado;
        header('Location: admin.php');
        exit;
    }
    else{
        $erro = 'Credenciais inválidas ou acesso não autorizado.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-be">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <title>Login</title>
</head>
<body>

    <div class="login">

        <h3>Login Administrador</h3>

            <form method="POST">
                <div class="box-login">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required placeholder="Digite seu email">
                </div>
                
                <div class="box-login">
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" required placeholder="Digite sua senha">
                    <?php if(!empty($erro)) echo "<p>$erro</p>";?>
                </div>
                
                <div class="btn-enviar">
                    <button type="submit" name="login">Entrar</button>
                </div>  
            </form>

            <div class="btn-voltar">
                <a href="/sitemr/"><button>Voltar</button><a>
            </div>
                
    </div>

    <div class="logo">
    <img src="assets/img/logopng.png">
    </div>

</body>
</html>