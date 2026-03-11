<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$statusEnvio = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $mail = new PHPMailer(true);

    try{
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $nome = htmlspecialchars($_POST['nome']);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $telefone = htmlspecialchars($_POST['telefone']);
        $assunto = htmlspecialchars($_POST['assunto']);
        $mensagem = nl2br(htmlspecialchars($_POST['mensagem']));

        if(!$email){
            throw new Exception("E-mail inválido.");
        }

        $mail->setFrom('contatomr2026@gmail.com', 'Sistema de Teste Maria Rocha');
        $mail->addAddress('jr.teixeira2010@gmail.com');
        $mail->addReplyTo($email, $nome);

        $mail->isHTML(true);
        $mail->Subject = "Novo Contato: " . $assunto;
        $mail->Body = "
            <h2></h2>
            <p><b>Nome:</b> $nome</p>
            <p><b>E-mail:</b> $email</p>
            <p><b>Telefone:</b> $telefone</p>
            <hr>
            <p><b>Mensagem:</b><br>$mensagem</p>
        ";

        $mail->send();
        $statusEnvio = "<p style='color: green;'>Mensagem enviada com sucesso!</p>";
    }
    catch(Exception $e){
        $statusEnvio = "<p style='color: red;'>Erro ao enviar: {$mail->ErrorInfo}</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Formulário</title>
</head>
<body>
    <h2>Formulário de Contato</h2>
    
    <?php echo $statusEnvio; ?>

    <form action="contatomail.php" method="post">
        <label for="nome">Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label for="email">E-mail:</label><br>
        <input type="email" name="email" required><br><br>

        <label for="telefone">Telefone:</label><br>
        <input type="text" name="telefone" required><br><br>

        <label for="assunto">Assunto:</label>
        <select id="assunto" name="assunto" required>
            <option value="" disabled selected>-- Selecione uma opção --</option>
            <option value="processo_seletivo">Dúvidas sobre o Processo Seletivo</option>
            <option value="matriculas">Dúvidas sobre Matrículas (indique o curso)</option>
            <option value="aproveitamento">Dúvidas sobre Aproveitamento</option>
            <option value="atestado">Dúvidas sobre Atestado de Frequência</option>
        </select><br><br>

        <label for="mensagem">Mensagem:</label><br>
        <textarea name="mensagem" rows="5" required></textarea><br><br>

        <button type="submit">Enviar Agora</button>
    </form>
</body>
</html>