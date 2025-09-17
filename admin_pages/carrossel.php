<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

function reorganizarOrdens() {
    $imagens = read('carrossel', '*', null, [], false, 'ordem ASC');

    $novaOrdem = 1;
    foreach ($imagens as $img) {
        if ($img['ordem'] != $novaOrdem) {
            update('carrossel', ['ordem' => $novaOrdem], 'id = :id', ['id' => $img['id']]);
        }
        $novaOrdem++;
    }
}

function empurrarOrdens() {
    $imagens = read('carrossel', '*', null, [], false, 'ordem DESC');

    foreach ($imagens as $img) {
        update('carrossel', ['ordem' => $img['ordem'] + 1], 'id = :id', ['id' => $img['id']]);
    }
}

function salvarImagem($id = null) {
    $titulo = $_POST['titulo'] ?? '';
    $arquivo = $_FILES['imagem'] ?? null;
    $caminho = null;

    if ($arquivo && $arquivo['error'] === UPLOAD_ERR_OK) {
        $caminho = salvarArquivo($arquivo, '', '', '', 'uploads', 'imagem');
    }

    $dados = ['titulo' => $titulo];
    if ($caminho) $dados['imagem'] = $caminho;

    if ($id) {
        update('carrossel', $dados, 'id = :id', ['id' => $id]);
    } else {
        empurrarOrdens();
        $dados['ordem'] = 1;
        $resultado = create('carrossel', $dados);

        if (is_array($resultado) && isset($resultado['warning'])) {
            echo "<p style='color: orange; font-weight: bold;'>{$resultado['warning']}</p>";
            return;
        }
    }

    header('Location: admin.php?action=carrossel');
    exit;
}

function excluirImagem($id) {
    $imagem = read('carrossel', '*', 'id = :id', ['id' => $id], true);

    if ($imagem && !empty($imagem['imagem']) && file_exists($imagem['imagem'])) {
        excluirArquivo($imagem['imagem']);
    }

    delete('carrossel', 'id = :id', ['id' => $id]);
    reorganizarOrdens();

    header('Location: admin.php?action=carrossel');
    exit;
}

$modo = $_GET['modo'] ?? null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$id = ($id > 0) ? $id : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_imagens'])) {
    salvarImagem($id);
}

if ($modo === 'editar' && $id) {
    $imagemEditando = read('carrossel', '*', 'id = :id', ['id' => $id], true);
}

if ($modo === 'excluir' && $id) {
    excluirImagem($id);
}

$imagens = read('carrossel', '*', null, [], false, 'ordem ASC');
?>

<h2><?= isset($imagemEditando) ? "Atualizar a imagem do carrossel" : "Adicionar nova imagem ao carrossel" ?></h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="titulo" placeholder="Título" value="<?= htmlspecialchars($imagemEditando['titulo'] ?? '') ?>" required><br><br>

    <input type="file" name="imagem" accept="image/*" <?= empty($imagemEditando) ? 'required' : '' ?>><br><br>
    
    <button type="submit" name="btn_imagens"><?= isset($id) ? "Atualizar" : "Salvar" ?></button>
</form>

<table border="1">
    <tr>
        <th>Ordem</th>
        <th>Título</th>
        <th>Imagem</th>
        <th>Ações</th>
    </tr>
    <?php foreach($imagens as $img): ?>
        <tr>
            <td><?= htmlspecialchars($img['ordem']) ?></td>
            <td><?= htmlspecialchars($img['titulo']) ?></td>
            <td>
                <img 
                    src="<?= htmlspecialchars($img['imagem']) ?>" 
                    alt="<?= htmlspecialchars($img['titulo']) ?>"
                    style="max-height: 80px;"
                >
            </td>
            <td>
                <a href="admin.php?action=carrossel&modo=editar&id=<?= $img['id'] ?>">Editar</a> |
                <a href="admin.php?action=carrossel&modo=excluir&id=<?= $img['id'] ?>">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>