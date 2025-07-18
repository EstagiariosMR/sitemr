<?php
session_start();

require 'includes/crud.php';
require 'includes/upload_arquivos.php';

function removerAcentos($string){
    $string = htmlentities($string, ENT_NOQUOTES, 'UTF-8');
    $string = preg_replace('/&([a-zA-Z])[a-zA-Z]+;/', '$1', $string);
    return $string;
}

function formatarNomeAluno($nome){
    $formatado = removerAcentos($nome);
    $formatado = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $formatado);
    $formatado = preg_replace('/_+/', '_', $formatado);
    $formatado = str_replace(' ', '_', $formatado);
    return strtolower($formatado);
}

if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['btn_logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$action = $_GET['action'] ?? null;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<header class="admin-header">
    <div class="admin-title">Painel Administrativo</div>
    <nav class="admin-nav">
        <a href="admin.php" class="admin-link">Início</a>
        <a href="admin.php?action=imagens" class="admin-link">Carrossel</a>
        <a href="admin.php?action=noticias" class="admin-link">Notícias</a>
        <a href="admin.php?action=trabalhos" class="admin-link">Trabalhos</a>
        <form method="POST" class="admin-logout-form">
            <button type="submit" name="btn_logout" class="admin-logout-btn">Sair</button>
        </form>
    </nav>
</header>

<?php
switch ($action) {
    case 'noticias':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_noticias'])) salvarNoticia();
        exibirNoticias();
        break;

    case 'editar_noticia':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_noticias'])) salvarNoticia($_GET['id']);
        formNoticia($_GET['id']);
        break;

    case 'excluir_noticia':
        excluirNoticia();
        break;

    case 'trabalhos':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_trabalhos'])) salvarTrabalho();
        exibirTrabalhos();
        break;

    case 'editar_trabalho':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_trabalhos'])) salvarTrabalho($_GET['id']);
        formTrabalho($_GET['id']);
        break;

    case 'excluir_trabalho':
        excluirTrabalho();
        break;

    case 'imagens':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_imagens'])) salvarImagem();
        exibirImagens();
        break;

    case 'editar_imagem':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_imagens'])) salvarImagem($_GET['id']);
        formImagem($_GET['id']);
        break;

    case 'excluir_imagem':
        excluirImagem();
        break;

    case null:
        echo "<p>Bem-vindo ao painel administrativo. Selecione uma das opções acima.</p>";
        break;

    default:
        echo "<p>Ação inválida.</p>";
        break;
}

function exibirNoticias() {
    formNoticia();
    listarNoticias();
}

function formNoticia($id = null) {
    $noticia = $id ? read('noticias', '*', 'id = :id', ['id' => $id], true) : null;

    echo "<h2>" . ($id ? "Editar Notícia" : "Nova Notícia") . "</h2>";
    echo "<form method='POST' enctype='multipart/form-data'>";
    echo "<input type='text' name='titulo' placeholder='Título' value='" . htmlspecialchars($noticia['titulo'] ?? '') . "' required><br><br>";
    echo "<textarea name='conteudo' placeholder='Conteúdo' rows='5'>" . htmlspecialchars($noticia['conteudo'] ?? '') . "</textarea><br><br>";
    echo "<input type='file' name='arquivo' accept='.jpg,.jpeg,.png,.pdf'><br><br>";
    echo "<button type='submit' name='btn_noticias'>" . ($id ? "Atualizar" : "Publicar") . "</button>";
    echo "</form><hr>";
}

function salvarNoticia($id = null) {
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $arquivo = $_FILES['arquivo'] ?? null;
    $caminho = $arquivo && $arquivo['error'] === UPLOAD_ERR_OK ? salvarArquivo($arquivo, 'noticia') : null;

    $dados = ['titulo' => $titulo, 'conteudo' => $conteudo];
    if ($caminho) $dados['arquivo'] = $caminho;

    $id
        ? update('noticias', $dados, 'id = :id', ['id' => $id])
        : create('noticias', $dados);

    header('Location: admin.php?action=noticias');
    exit;
}

function listarNoticias() {
    $noticias = read('noticias', '*', null, [], false, 'id DESC');
    echo "<table border='1'><tr><th>Título</th><th>Data</th><th>Ações</th></tr>";
    foreach ($noticias as $n) {
        echo "<tr>
            <td>" . htmlspecialchars($n['titulo']) . "</td>
            <td>{$n['data_publicacao']}</td>
            <td>
                <a href='admin.php?action=editar_noticia&id={$n['id']}'>Editar</a> |
                <a href='admin.php?action=excluir_noticia&id={$n['id']}'>Excluir</a>
            </td>
        </tr>";
    }
    echo "</table><hr>";
}

function excluirNoticia() {
    if (isset($_GET['id'])) {
        delete('noticias', 'id = :id', ['id' => $_GET['id']]);
        header('Location: admin.php?action=noticias');
        exit;
    }
}

function exibirTrabalhos() {
    formTrabalho();
    listarTrabalhos();
}

function formTrabalho($id = null) {
    $trabalho = $id ? read('trabalhos_integrado', '*', 'id = :id', ['id' => $id], true) : null;

    echo "<h2>" . ($id ? "Editar Trabalho" : "Novo Trabalho") . "</h2>";
    echo "<form method='POST' enctype='multipart/form-data'>";
    echo "<input type='text' name='ano' placeholder='Ano' value='" . htmlspecialchars($trabalho['ano'] ?? '') . "' required><br><br>";
    echo "<input type='text' name='turma' placeholder='Turma' value='" . htmlspecialchars($trabalho['turma'] ?? '') . "' required><br><br>";
    echo "<input type='text' name='aluno' placeholder='Nome do Aluno' value='" . htmlspecialchars($trabalho['aluno'] ?? '') . "'><br><br>";
    echo "<input type='file' name='arquivo' accept='application/pdf'" . (!$id ? ' required' : '') . "><br><br>";
    echo "<button type='submit' name='btn_trabalhos'>" . ($id ? "Atualizar" : "Salvar") . "</button>";
    echo "</form><hr>";
}

function salvarTrabalho($id = null) {
    $ano = $_POST['ano'];
    $turma = $_POST['turma'];
    $aluno = $_POST['aluno'];
    $arquivo = $_FILES['arquivo'];
    $alunoFormatado = formatarNomeAluno($aluno);
    $caminho = $arquivo && $arquivo['error'] === UPLOAD_ERR_OK ? salvarArquivo($arquivo, $ano, $turma, $alunoFormatado, 'uploads', 'trabalho') : null;

    if (!$id && !$caminho) {
        echo "Erro ao salvar o arquivo.";
        return;
    }

    $dados = ['ano' => $ano, 'turma' => $turma, 'aluno' => $aluno];
    if ($caminho) $dados['arquivo'] = $caminho;

    $id
        ? update('trabalhos_integrado', $dados, 'id = :id', ['id' => $id])
        : create('trabalhos_integrado', $dados);

    header('Location: admin.php?action=trabalhos');
    exit;
}

function listarTrabalhos() {
    $trabalhos = read('trabalhos_integrado', '*', null, [], false, 'id DESC');
    echo "<table border='1'><tr><th>Aluno</th><th>Ano</th><th>Turma</th><th>Ações</th></tr>";
    foreach ($trabalhos as $t) {
        echo "<tr>
            <td>" . htmlspecialchars($t['aluno']) . "</td>
            <td>{$t['ano']}</td>
            <td>{$t['turma']}</td>
            <td>
                <a href='admin.php?action=editar_trabalho&id={$t['id']}'>Editar</a> |
                <a href='admin.php?action=excluir_trabalho&id={$t['id']}'>Excluir</a>
            </td>
        </tr>";
    }
    echo "</table><hr>";
}

function excluirTrabalho() {
    if (isset($_GET['id'])) {
        delete('trabalhos_integrado', 'id = :id', ['id' => $_GET['id']]);
        header('Location: admin.php?action=trabalhos');
        exit;
    }
}

function exibirImagens(){
    formImagem();
    listarImagens();
}

function formImagem($id=null){
    $imagem = $id ? read('carrossel', '*', 'id = :id', ['id' => $id], true) : null;

    echo "<h2>" . ($id ? "Editar Imagem" : "Nova Imagem") . "</h2>";
    echo "<form method='POST' enctype='multipart/form-data'>";
    echo "<input type='text' name='titulo' placeholder='Título' value='" . htmlspecialchars($imagem['titulo'] ?? '') . "' required><br><br>";
    echo "<input type='file' name='arquivo' accept='image/*'" . (!$id ? ' required' : '') . "><br><br>";
    echo "<button type='submit' name='btn_imagens'>" . ($id ? "Atualizar" : "Salvar") . "</button>";
    echo "</form><hr>";
}

function salvarImagem($id=null){
    $titulo = $_POST['titulo'];
    $arquivo = $_FILES['arquivo'] ?? null;
    $caminho = $arquivo && $arquivo['error'] === UPLOAD_ERR_OK ? salvarArquivo($arquivo, 'uploads', 'carrossel') : null;

    $dados = ['titulo' => $titulo];

    if($caminho) $dados['imagem'] = $caminho;

    if($id){
        update('carrossel', $dados, 'id = :id', ['id' => $id]);
    }
    else{
        $maior = read('carrossel', 'MAX(ordem) AS max_ordem', null, [], true);
        $dados['ordem'] = ($maior['max_ordem'] ?? 0) + 1;

        $resultadi = create('carrossel', $dados);

        if(is_array($resultado) && isset($resultado['warning'])){
            echo "<p style='color: orange; font-weight: bold;'>{$resultado['warning']}</p>";
            return;
        }
    }

    header('Location: admin.php?action=imagens');
    exit;
}

function listarImagens(){
    $imagens = read('carrossel', '*', null, [], false, 'ordem ASC');
    echo "<table border='1'><tr><th>Ordem</th><th>Título</th><th>Imagem</th><th>Ações</th></tr>";
    foreach($imagens as $img){
        $imgPath = htmlspecialchars($img['imagem']);
        echo "<tr>
            <td>{$img['ordem']}</td>
            <td>" . htmlspecialchars($img['titulo']) . "</td>
            <td><img src='{$imgPath}' alt='" . htmlspecialchars($img['titulo']) . "' style='max-height: 80px;'></td>
            <td>
                <a href='admin.php?action=editar_imagem&id={$img['id']}'>Editar</a> |
                <a href='admin.php?action=excluir_imagem&id={$img['id']}'>Excluir</a>
            </td>
        </tr>";
    }
}

function excluirImagem(){

}
?>
</body>
</html>
