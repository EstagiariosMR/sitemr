<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$statusEnvio = "";
$status_type = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $mail = new PHPMailer(true);

    try{
        $mail->SMTPDebug = 0;
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
        $statusEnvio = "Mensagem enviada com sucesso!";
        $status_type = "success";
    }
    catch(Exception $e){
        $statusEnvio = "Erro ao enviar: {$mail->ErrorInfo}";
        $status_type = "error";
    }
}
?>

<main>
    <h1>Contatos</h1>

    <div class="info-contato">
        <div class="info-box">
            <h2>Contato</h2>
            <p>
                <img class="icon-svg" src="assets/img/phone-call.svg" alt="Telefone" />
                <strong>Telefone:</strong> (55) 3222 - 8171
            </p>
            <p>
                <img class="icon-svg" src="assets/img/email.svg" alt="E-mail" />
                <strong>E-mail:</strong> mariarocha08cre@educacao.rs.gov.br
            </p>
        </div>
        <div class="info-box">
            <h2>Redes Sociais</h2>
            <p>
                <img class="icon-svg" src="assets/img/instagram.svg" alt="Instagram" />
                <a href="https://instagram.com/" target="_blank">Instagram</a>
            </p>
            <p>
                <img class="icon-svg" src="assets/img/facebook.svg" alt="Facebook" />
                <a href="https://facebook.com/" target="_blank">Facebook</a>
            </p>
        </div>
    </div>

    <br>

    <h1>Entre em contato</h1>

    <div class="contato">
        <h2>Escola Maria Rocha</h2>
        <p>Estamos aqui para ajudar! Preencha o formulário abaixo e entraremos em contato o mais breve possível.</p>

        <pre style="display: none;">Type: <?php var_dump($status_type); ?></pre>

        <?php if(!empty($statusEnvio)): ?>
            <div id="status-alert" class="alert-box <?php echo $status_type; ?>">
                <span class="alert-message"><?php echo $statusEnvio?></span>
                <button type="button" id="close-alert" class="close-btn">&times;</button>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" placeholder="Digite seu nome" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="Digite seu email" required>

            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" placeholder="Digite seu telefone" required>

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

    <script src="assets/js/alertMessage.js"></script>
</main>
    
    

    <!-- <form method="post">
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
</html> -->