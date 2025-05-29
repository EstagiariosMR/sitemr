<?php
// Carregar o autoloader do PHPMailer
require 'vendor/autoload.php'; // Ajuste o caminho conforme necessário

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Criar uma nova instância do PHPMailer]
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $assunto = $_POST['assunto'];
    $mensagem = $_POST['mensagem'];

    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor de e-mail
        $mail->isSMTP();                                           // Configura o envio via SMTP
        $mail->Host = 'smtp.gmail.com';                        // Defina o servidor SMTP
        $mail->SMTPAuth = true;                                      // Habilita autenticação SMTP
        $mail->Username = 'mariarochatest@gmail.com';                    // Seu endereço de e-mail
        $mail->Password = 'bargyavhgwtedcsh';                                // Sua senha do e-mail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Habilita criptografia TLS
        $mail->Port = 587;                                           // Porta SMTP

        $corpoMensagem = "
            <p><strong>Nome:</strong> $nome</p>
            <p><strong>E-mail:</strong> $email</p>
            <p><strong>Assunto:</strong> $assunto</p>
            <p><strong>Mensagem:</strong></p>
            <p>$mensagem</p>
        ";

        // Remetente e destinatário
        $mail->setFrom('mariarochatest@gmail.com', 'Escola Maria Rocha');            // E-mail do remetente
        $mail->addAddress('mariarochatest@gmail.com', 'Maria Rocha'); // E-mail do destinatário
        $mail->addReplyTo($email, $nome);

        // Conteúdo do e-mail
        $mail->isHTML(true);                                          // Envia o e-mail no formato HTML
        $mail->Subject = $assunto;                                    // Assunto do e-mail vindo do formulário
        $mail->Body = $corpoMensagem;                                   // Corpo do e-mail vindo do formulário
        $mail->AltBody = strip_tags($corpoMensagem);

        // Envia o e-mail
        $mail->send();
        echo '<span>Mensagem enviada com sucesso!</span>';
    } catch (Exception $e) {
        echo "<span>Mensagem não pode ser enviada. Erro: {$mail->ErrorInfo}</span>";
    }
    echo '<button><a href="/sitemr/">Voltar ao inicio</button>';
}
?>

<main>
    <div class="contato">
        <h1>Entre em contato com a </h1>
        <h2>Escola Maria Rocha</h2>
        <p>Estamos aqui para ajudar! Preencha o formulário abaixo e entraremos em contato o mais breve possível.</p>
        <form action="contato.php" method="post">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" placeholder="Digite seu nome" d>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="Digite sua senha" required>

            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" placeholder="Digite seu Telefone" required>

            <label for="assunto">Assunto:</label>
            <select id="assunto" name="assunto" required>
                <option value="" disabled selected>-- Selecione uma opção --</option>
                <option value="processo_seletivo">Dúvidas sobre o Processo Seletivo</option>
                <option value="matriculas">Dúvidas sobre Matrículas (indique o curso)</option>
                <option value="aproveitamento">Dúvidas sobre Aproveitamento</option>
                <option value="atestado">Dúvidas sobre Atestado de Frequência</option>
            </select>

            <label for="mensagem">Mensagem:</label>
            <textarea name="mensagem" rows=15></textarea>
            <div class="enviar-bnt">
                <input type="submit" value="Enviar">
            </div>
        </form>
    </div>
</main>