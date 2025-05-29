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

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit;
}

if(isset($_POST['btn_logout'])){
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

if(isset($_POST['btn_noticias'])){
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];

    $caminho_imagem = null;

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK){
        $caminho_imagem = salvarArquivo($_FILES['imagem']);
    }

    $dados = [
        'titulo' => $titulo,
        'conteudo' => $conteudo,
        'imagem' => $caminho_imagem
    ];

    $resultadoImagem = create('noticias', $dados);

    if($resultadoImagem){
        echo "Noticia publicada com sucesso!";
    }
    else{
        echo "Erro ao publicar a notícia.";
    }
}

if(isset($_POST['btn_trabalhos'])){
    $ano = $_POST['ano'];
    $turma = $_POST['turma'];
    $aluno = $_POST['aluno'];
    $arquivo = $_FILES['arquivo'];

    $alunoFormatado = formatarNomeAluno($aluno);

    $caminhoArquivo = salvarArquivo($arquivo, $ano, $turma, $alunoFormatado, $pastaBase='uploads');

    if($caminhoArquivo){
        $dados = [
            'ano' => $ano,
            'turma' => $turma,
            'aluno' => $aluno,
            'arquivo' => $caminhoArquivo
        ];

        $resultadoArquivo = create('trabalhos_integrado', $dados);

        if($resultadoArquivo){
            echo "Trabalho cadastrado com sucesso!";
        }
        else{
            echo "Erro ao cadastrar o trabalho.";
        }
    }
    else{
        echo "Erro ao salvar o arquivo.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <label for="titulo">Título:</label><br>
        <input type="text" name="titulo" id="titulo" required><br><br>

        <label for="conteudo">Conteúdo:</label><br>
        <textarea name="conteudo" id="conteudo" rows="10" columns="50" required></textarea><br><br>

        <label for="imagem">Imgem:</label><br>
        <input type="file" name="imagem" id="imagem" accept="image/*"><br><br>

        <button type="submit" name="btn_noticias">Postar Notícia</button>
    </form>
    <hr>
    <form method="POST" enctype="multipart/form-data">
        <label for="ano">Ano:</label><br>
        <input type="text" name="ano" id="ano" required><br><br>

        <label for="turma">Turma:</label><br>
        <input type="text" name="turma" id="turma" required><br><br>

        <label for="aluno">Nome do Aluno:</label><br>
        <input type="text" name="aluno" id="aluno"><br><br>

        <label for="arquivo">Arquivo PDF:</label><br>
        <input type="file" name="arquivo" id="arquivo" accept="application/pdf" required><br><br>

        <button type="submit" name="btn_trabalhos">Salvar Trabalho</button>
    </form>
    <hr>
    <form method="POST">
        <button type="submit" name="btn_logout">Logout</button>
    </form>
</body>
</html>