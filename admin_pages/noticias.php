<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

function purificarConteudo($html){
    $config = HTMLPurifier_Config::createDefault();

    $config->set('HTML.Allowed', 'p,b,strong,i,em,u,a[href],ul,ol,li,br,blockquote,h1,h2,h3,font[style],span[style],img[src|alt|width|height],table,tr,td,th'); 
    $config->set('CSS.AllowedProperties', ['color', 'background-color', 'font-size', 'text-align', 'font-weight', 'font-style', 'text-decoration']);
    $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
    
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}

function processarArquivoUpload($arquivo, $arquivoAntigo = null){
    if($arquivo && $arquivo['error'] === UPLOAD_ERR_OK){
        if($arquivoAntigo){
            excluirArquivo($arquivoAntigo);
        }

        return salvarArquivo($arquivo, 'noticia');
    }

    return $arquivoAntigo;
}

function salvarNoticia($id = null){
    $titulo = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['conteudo'] ?? '');
    $arquivo = $_FILES['arquivo'] ?? null;

    if ($titulo === '' || $conteudo === '') {
        header('Location: admin.php?action=noticias&erro=campos_obrigatorios');
        exit;
    }

    $conteudoLimpo = purificarConteudo($conteudo);

    $arquivoAntigo = null; // inicializa para evitar undefined

    if($id !== null){
        $id = (int)$id;

        if($id <= 0){
            header('Location: admin.php?action=noticias&erro=id_invalido');
            exit;
        }

        $noticiaAntiga = read('noticias', '*', 'id = :id', ['id' => $id], true);
        $arquivoAntigo = $noticiaAntiga['arquivo'] ?? null;
    }

    $caminhoArquivo = processarArquivoUpload($arquivo, $arquivoAntigo);

    $dados = [
        'titulo' => $titulo,
        'conteudo' => $conteudoLimpo,
    ];

    if($caminhoArquivo){
        $dados['arquivo'] = $caminhoArquivo;
    }

    if($id){
        update('noticias', $dados, 'id = :id', ['id' => $id]);
    }
    else{
        create('noticias', $dados);
    }

    header('Location: admin.php?action=noticias');
    exit;
}

function excluirNoticia($id){
    if(!$id) return;

    $noticia = read('noticias', '*', 'id = :id', ['id' => $id], true);

    if($noticia && !empty($noticia['arquivo'])){
        excluirArquivo($noticia['arquivo']);
    }

    delete('noticias', 'id = :id', ['id' => $id]);

    header('Location: admin.php?action=noticias');
    exit;
}

$modo = $_GET['modo'] ?? null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$id = ($id > 0) ? $id : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_noticias'])) {
    salvarNoticia($id);
}

if ($modo === 'editar' && $id) {
    $noticia = read('noticias', '*', 'id = :id', ['id' => $id], true);
}

if ($modo === 'excluir' && $id) {
    excluirNoticia($id);
}

$noticias = read('noticias', '*', null, [], false, 'id DESC');
?>

<h2><?= isset($noticia) ? "Editar Notícia" : "Nova Notícia" ?></h2>

<form id="formEditor" method="POST" enctype="multipart/form-data">
    <input
        type="text"
        name="titulo"
        placeholder="Título"
        value="<?= htmlspecialchars($noticia['titulo'] ?? '') ?>"
        required
    >
    <br><br>

    <div class="toolbar">
        <button type="button" onclick="format('bold')"><b>B</b></button>
        <button type="button" onclick="format('italic')"><i>I</i></button>
        <button type="button" onclick="format('underline')"><u>U</u></button>
        <button type="button" onclick="format('strikeThrough')">abc</button>
        <select onchange="format('formatBlock', this.value)">
            <option value="">Parágrafo</option>
            <option value="h1">Título 1</option>
            <option value="h2">Título 2</option>
            <option value="h3">Título 3</option>
        </select>
        <select onchange="format('fontSize', this.value)">
            <option value="">Tamanho</option>
            <option value="1">Pequeno</option>
            <option value="3">Normal</option>
            <option value="5">Grande</option>
            <option value="7">Enorme</option>
        </select>
        <input type="color" onchange="format('foreColor', this.value)" title="Cor da fonte" />
        <input type="color" onchange="format('hiliteColor', this.value)" title="Cor de fundo" />
        <button type="button" onclick="format('insertOrderedList')">1.</button>
        <button type="button" onclick="format('insertUnorderedList')">•</button>
        <button type="button" onclick="format('justifyLeft')">⬅️</button>
        <button type="button" onclick="format('justifyCenter')">⬅️➡️</button>
        <button type="button" onclick="format('justifyRight')">➡️</button>
        <button type="button" onclick="format('indent')">➡️ Recuar</button>
        <button type="button" onclick="format('outdent')">⬅️ Voltar</button>
        <button type="button" onclick="format('removeFormat')">✖ Limpar</button>
    </div>

    <div
        id="editor"
        contenteditable="true"
        style="background-color: white; border: 1px solid #ccc; min-height: 150px; padding: 10px; overflow-y: auto;"
    ><?= $noticia['conteudo'] ?? '' ?></div>

    <input type="hidden" name="conteudo" id="conteudo">

    <br><br>

    <input type="file" name="arquivo" accept=".jpg,.jpeg,.png,.pdf"><br><br>

    <button type="submit" name="btn_noticias">
        <?= $id ? "Atualizar" : "Publicar" ?>
    </button>
</form>

<table border="1">
    <tr>
        <th>Título</th>
        <th>Data</th>
        <th>Ações</th>
    </tr>

    <?php foreach ($noticias as $n): ?>
        <tr>
            <td><?= htmlspecialchars($n['titulo']) ?></td>
            <td><?= $n['data_publicacao'] ?></td>
            <td>
                <a href="admin.php?action=noticias&modo=editar&id=<?= $n['id'] ?>">Editar</a> |
                <a href="admin.php?action=noticias&modo=excluir&id=<?= $n['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>