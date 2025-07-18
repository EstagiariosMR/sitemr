<?php
function salvarArquivo($arquivo, $ano='', $turma='', $aluno='', $pastaBase='uploads', $tipoConteudo='noticia'){
    if(empty($arquivo['name']) || $arquivo['error'] !== 0){
        return null;
    }

    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    $tiposImagem = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    // $tipoArquivo = '';

    if($extensao !== 'pdf' && !in_array($extensao, $tiposImagem)){
        return null;
    }
    
    switch($tipoConteudo){
        case 'imagem':
            $pastaDestino = "$pastaBase/carrossel";
            $nomeFinal = basename($arquivo['name']);
            break;
        case 'trabalho':
            $pastaDestino = "$pastaBase/trabalhos/$ano/$turma";
            $nomeFinal = $aluno . '.pdf';
            break;
        case 'noticia':
        default:
            $pastaDestino = "$pastaBase/noticias";
            $nomeFinal = basename($arquivo['name']);
            break;
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