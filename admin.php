<?php
ob_start();
session_start();

require 'vendor/autoload.php';
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

$tituloBase = 'Painel Administrativo';
$tituloFormatado = $tituloBase;

if($action === 'imagens'){
    $tituloFormatado .= ' - Carrossel';
}
elseif($action === 'noticias'){
    $tituloFormatado .= ' - Notícias';
}
elseif($action === 'trabalhos'){
    $tituloFormatado .= ' - Trabalhos';
}

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
    <div class="admin-title"><?= htmlspecialchars($tituloFormatado) ?></div>
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

function purificarConteudo($html){
    $config = HTMLPurifier_Config::createDefault();

    $config->set('HTML.Allowed', 'p,b,strong,i,em,u,a[href],ul,ol,li,br,blockquote,h1,h2,h3,font[style],span[style],img[src|alt|width|height],table,tr,td,th'); 
    $config->set('CSS.AllowedProperties', ['color', 'background-color', 'font-size', 'text-align', 'font-weight', 'font-style', 'text-decoration']);
    $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
    
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}

function exibirNoticias() {
    formNoticia();
    listarNoticias();
}

function formNoticia($id = null) {
    $noticia = $id ? read('noticias', '*', 'id = :id', ['id' => $id], true) : null;

    echo "<h2>" . ($id ? "Editar Notícia" : "Nova Notícia") . "</h2>";
    echo "<div id='msgErro' style='display:none; color: red; margin-bottom: 10px;'></div>";
    echo "<form id='formEditor' method='POST' enctype='multipart/form-data'>";
    echo "<input type='text' name='titulo' placeholder='Título' value='" . htmlspecialchars($noticia['titulo'] ?? '') . "' required><br><br>";

    echo "
    <div class='toolbar'>
      <button type='button' onclick='format(\"bold\")'><b>B</b></button>
      <button type='button' onclick='format(\"italic\")'><i>I</i></button>
      <button type='button' onclick='format(\"underline\")'><u>U</u></button>
      <button type='button' onclick='format(\"strikeThrough\")'>abc</button>
      <select onchange='format(\"formatBlock\", this.value)'>
        <option value=''>Parágrafo</option>
        <option value='h1'>Título 1</option>
        <option value='h2'>Título 2</option>
        <option value='h3'>Título 3</option>
      </select>
      <select onchange='format(\"fontSize\", this.value)'>
        <option value=''>Tamanho</option>
        <option value='1'>Pequeno</option>
        <option value='3'>Normal</option>
        <option value='5'>Grande</option>
        <option value='7'>Enorme</option>
      </select>
      <input type='color' onchange='format(\"foreColor\", this.value)' title='Cor da fonte' />
      <input type='color' onchange='format(\"hiliteColor\", this.value)' title='Cor de fundo' />
      <button type='button' onclick='format(\"insertOrderedList\")'>1.</button>
      <button type='button' onclick='format(\"insertUnorderedList\")'>•</button>
      <button type='button' onclick='format(\"justifyLeft\")'>⬅️</button>
      <button type='button' onclick='format(\"justifyCenter\")'>⬅️➡️</button>
      <button type='button' onclick='format(\"justifyRight\")'>➡️</button>
      <button type='button' onclick='format(\"indent\")'>➡️ Recuar</button>
      <button type='button' onclick='format(\"outdent\")'>⬅️ Voltar</button>
      <button type='button' onclick='format(\"removeFormat\")'>✖ Limpar</button>
    </div>

    <div id='editor' contenteditable='true' style='background-color: white; border: 1px solid #ccc; min-height: 150px; padding: 10px; overflow-y: auto;'>" . ($noticia['conteudo'] ?? '') . "</div>

    <input type='hidden' name='conteudo' id='conteudo'>

    <br><br>
    ";

    echo "<input type='file' name='arquivo' accept='.jpg,.jpeg,.png,.pdf'><br><br>";
    echo "<button type='submit' name='btn_noticias'>" . ($id ? "Atualizar" : "Publicar") . "</button>";
    echo "</form><hr>";
}

function salvarNoticia($id = null) {

    $conteudoSalvo = '';
    $conteudo = $_POST['conteudo'] ?? '';
    if ($conteudo) {
        $conteudoSalvo = $conteudo;
    }

    echo $conteudoSalvo . "<br><br><br><br>";

    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $conteudoLimpo = purificarConteudo($conteudo);
    $arquivo = $_FILES['arquivo'] ?? null;
    $caminho = $arquivo && $arquivo['error'] === UPLOAD_ERR_OK ? salvarArquivo($arquivo, 'noticia') : null;

    $dados = ['titulo' => $titulo, 'conteudo' => $conteudoLimpo];
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
        $id = $_GET['id'];

        $noticia = read('noticias', '*', 'id = :id', ['id' => $id], true);

        if($noticia && !empty($noticia['arquivo'])){
            excluirArquivo($noticia['arquivo']);
        }

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
        $id = $_GET['id'];

        $trabalho = read('trabalhos_integrado', '*', 'id = :id', ['id' => $id], true);

        if($trabalho && !empty($trabalho['arquivo'])){
            excluirArquivo($trabalho['arquivo']);
        }
        
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

    echo "<h2>" . ($id ? "Atualizar a imagem do carrossel" : "Adicionar nova imagem ao carrossel") . "</h2>";
    echo "<form method='POST' enctype='multipart/form-data'>";
    echo "<input type='text' name='titulo' placeholder='Título' value='" . htmlspecialchars($imagem['titulo'] ?? '') . "' required><br><br>";
    echo "<input type='file' name='imagem' accept='image/*'" . (!$id ? ' required' : '') . "><br><br>";
    echo "<button type='submit' name='btn_imagens'>" . ($id ? "Atualizar" : "Salvar") . "</button>";
    echo "</form><hr>";
}

function reorganizarOrdens(){
    $imagens = read('carrossel', '*', null, [], false, 'ordem ASC');

    $novaOrdem = 1;

    foreach($imagens as $img){
        if($img['ordem'] != $novaOrdem){
            update('carrossel', ['ordem' => $novaOrdem], 'id = :id', ['id' => $img['id']]);
        }

        $novaOrdem++;
    }
}

function empurrarOrdens(){
    $imagens = read('carrossel', '*', null, [], false, 'ordem DESC');

    foreach($imagens as $img){
        update('carrossel', ['ordem' => $img['ordem'] + 1], 'id = :id', ['id' => $img['id']]);
    }
}

function salvarImagem($id=null){
    $titulo = $_POST['titulo'] ?? '';
    $arquivo = $_FILES['imagem'] ?? null;
    $caminho = $arquivo && $arquivo['error'] === UPLOAD_ERR_OK ? salvarArquivo($arquivo, '', '', '', 'uploads', 'imagem') : null;

    $dados = ['titulo' => $titulo];

    if($caminho) $dados['imagem'] = $caminho;

    if($id){
        update('carrossel', $dados, 'id = :id', ['id' => $id]);
    }
    else{
        empurrarOrdens();
        $dados['ordem'] = 1;
        $resultado = create('carrossel', $dados);

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

function excluirImagem() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $imagem = read('carrossel', '*', 'id = :id', ['id' => $id], true);

        if ($imagem && !empty($imagem['imagem']) && file_exists($imagem['imagem'])) {
            excluirArquivo($imagem['imagem']);
        }

        delete('carrossel', 'id = :id', ['id' => $id]);

        reorganizarOrdens();

        header('Location: admin.php?action=imagens');
        exit;
    }
}
?>

<script src="assets/js/editor.js"></script>

<?php ob_end_flush(); ?>
</body>
</html>
