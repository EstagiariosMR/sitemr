<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

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

function salvarTrabalho($id = null){
    $ano = trim($_POST['ano'] ?? '');
    $turma = trim($_POST['turma'] ?? '');
    $aluno = trim($_POST['aluno'] ?? '');
    $arquivo = $_FILES['arquivo'] ?? null;

    if($ano === '' || $turma === '' || $aluno === ''){
        header('Location: admin.php?action=trabalhos&erro=campos_obrigatorios');
        exit;
    }

    $alunoFormatado = formatarNomeAluno($aluno);

    $dados = [
        'ano' => $ano,
        'turma' => $turma,
        'aluno' => $aluno
    ];

    $arquivoAntigo = null;

    if($id !== null){
        $id = (int)$id;

        if($id <= 0){
            header('Location: admin.php?action=trabalhos&erro=id_invalido');
            exit;
        }

        $trabalhoAntigo = read('trabalhos_integrado', '*', 'id = :id', ['id' => $id], true);
        $arquivoAntigo = $trabalhoAntigo['arquivo'] ?? null;
    }
    
    if($arquivo && $arquivo['error'] === UPLOAD_ERR_OK){
        if($arquivoAntigo){
            excluirArquivo($arquivoAntigo);
        }
        
        $caminho = salvarArquivo($arquivo, $ano, $turma, $alunoFormatado, 'uploads', 'trabalho');

        if($caminho){
            $dados['arquivo'] = $caminho;
        }
    }
    elseif($arquivoAntigo){
        $dados['arquivo'] = $arquivoAntigo;
    }

    if($id){
        update('trabalhos_integrado', $dados, 'id = :id', ['id' => $id]);
    }
    else{
        create('trabalhos_integrado', $dados);
    }

    header('Location: admin.php?action=trabalhos');
    exit;
}

function excluirTrabalho($id){
    if(!$id) return;

    $trabalho = read('trabalhos_integrado', '*', 'id = :id', ['id' => $id], true);

    if($trabalho && !empty($trabalho['arquivo'])){
        excluirArquivo($trabalho['arquivo']);
    }

    delete('trabalhos_integrado', 'id = :id', ['id' => $id]);

    header('Location: admin.php?action=trabalhos');
    exit;
}

$modo = $_GET['modo'] ?? null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$id = ($id > 0) ? $id : null;

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_trabalhos'])){
    salvarTrabalho($id);
}

if($modo === 'editar' && $id){
    $trabalho = read('trabalhos_integrado', '*', 'id = :id', ['id' => $id], true);
}

if($modo === 'excluir' && $id){
    excluirTrabalho($id);
}

$trabalhos = read('trabalhos_integrado', '*', null, [], false, 'id DESC');
?>

<h2><?= isset($trabalho) ? "Editar Trabalho" : "Novo Trabalho" ?></h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="ano" placeholder="Ano" value="<?= htmlspecialchars($trabalho['ano'] ?? '') ?>" required><br><br>

    <input type="text" name="turma" placeholder="Turma" value="<?= htmlspecialchars($trabalho['turma'] ?? '') ?>" required><br><br>

    <input type="text" name="aluno" placeholder="Nome do Aluno" value="<?= htmlspecialchars($trabalho['aluno'] ?? '') ?>"><br><br>

    <input type="file" name="arquivo" accept="application/pdf" <?= isset($trabalho) ? '' : 'required'?>><br><br>

    <button type="submit" name="btn_trabalhos"><?= isset($trabalho) ? "Atualizar" : "Salvar" ?></button>
</form>

<table border="1">
    <tr>
        <th>Aluno</th>
        <th>Ano</th>
        <th>Turma</th>
        <th>Ações</th>
    </tr>
    <?php foreach($trabalhos as $t): ?>
        <tr>
            <td><?= htmlspecialchars($t['aluno']) ?></td>
            <td><?= $t['ano'] ?></td>
            <td><?= $t['turma'] ?></td>
            <td>
                <a href="admin.php?action=trabalhos&modo=editar&id=<?= $t['id'] ?>">Editar</a> |
                <a href="admin.php?action=trabalhos&modo=excluir&id=<?= $t['id'] ?>" onclick="return confirm('Tem certeza que deseja excluireste trabalho?')">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>