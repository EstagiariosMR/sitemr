<?php
function salvarArquivo($arquivo, $ano='', $turma='', $aluno='', $pastaBase='uploads'){
    if(empty($arquivo['name']) || $arquivo['error'] !== 0){
        return null;
    }

    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    $tiposImagem = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    $tipoArquivo = '';

    if($extensao === 'pdf'){
        $tipoArquivo = 'trabalhos';
    }
    elseif(in_array($extensao, $tiposImagem)){
        $tipoArquivo = 'imagens';
    }
    else{
        return null;
    }

    if($tipoArquivo === 'trabalhos'){
        $nomeFinal = $aluno . '.pdf';
        $pastaDestino = "$pastaBase/trabalhos/$ano/$turma";
    }
    else{
        $nomeFinal = $arquivo['name'];
        $pastaDestino = "$pastaBase/imagens";
    }

    if(!is_dir($pastaDestino)){
        mkdir($pastaDestino, 0755, true);
    }

    $caminhoFinal = "$pastaDestino/$nomeFinal";

    if(move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)){
        return $caminhoFinal;
    }

    return null;
}

function buscarImagem($caminho, $imagemPadrao = 'uploads/imagens/sem-imagem.jpg'){
    if(empty($caminho)){
        return null;
    }
    
    if(file_exists($caminho)){
        return $caminho;
    }
    
    return $imagemPadrao;
}