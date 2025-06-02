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
</head>
<body>
<header style="background: #333; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center;">
    <div style="font-size: 18px; font-weight: bold;">Painel Administrativo</div>
    <nav style="display: flex; gap: 10px; align-items: center;">
        <a href="admin.php" style="color: white; text-decoration: none; padding: 6px 12px; background: #555; border-radius: 4px;">Início</a>
        <a href="admin.php?action=noticias" style="color: white; text-decoration: none; padding: 6px 12px; background: #555; border-radius: 4px;">Notícias</a>
        <a href="admin.php?action=trabalhos" style="color: white; text-decoration: none; padding: 6px 12px; background: #555; border-radius: 4px;">Trabalhos</a>
        <form method="POST" style="display: inline;">
            <button type="submit" name="btn_logout" style="color: white; background: crimson; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Sair</button>
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
    echo "<input type='file' name='imagem' accept='image/*'><br><br>";
    echo "<button type='submit' name='btn_noticias'>" . ($id ? "Atualizar" : "Publicar") . "</button>";
    echo "</form><hr>";
}

function salvarNoticia($id = null) {
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $imagem = $_FILES['imagem'] ?? null;
    $caminho = $imagem && $imagem['error'] === UPLOAD_ERR_OK ? salvarArquivo($imagem) : null;

    $dados = ['titulo' => $titulo, 'conteudo' => $conteudo];
    if ($caminho) $dados['imagem'] = $caminho;

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
    $caminho = $arquivo && $arquivo['error'] === UPLOAD_ERR_OK ? salvarArquivo($arquivo, $ano, $turma, $alunoFormatado, 'uploads') : null;

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
?>
</body>
</html>
