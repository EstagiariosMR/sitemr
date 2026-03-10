<?php
require 'crud.php';

function removerAcentos($string){
    $string = htmlentities($string, ENT_NOQUOTES, 'UTF-8');
    $string = preg_replace('/&([a-zA-Z])[a-zA-Z]+;/', '$1', $string);
    return $string;
}

function formatarNomeAluno($nome){
    $aluno = removerAcentos($nome);
    $aluno = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $aluno);
    $aluno = preg_replace('/_+/', '_', $aluno);
    $aluno = str_replace(' ', '_', $aluno);
    $aluno = strtolower($aluno);
    return $aluno;
}

$ano = $_GET['ano'] ?? null;
$turma = $_GET['turma'] ?? null;
$aluno = $_GET['aluno'] ?? null;

$where = [];
$valores = [];

if(!empty($ano)){
    $where[] = "ano = :ano";
    $valores['ano'] = $ano;
}

if(!empty($turma)){
    $where[] = "turma = :turma";
    $valores['turma'] = $turma;
}

if(!empty($aluno)){
    $where[] = "aluno = :aluno";
    $valores['aluno'] = $aluno;
        
    $alunoFormatado = formatarNomeAluno($aluno);
    $where[] = "arquivo = :arquivo";
    $valores['arquivo'] = 'uploads/trabalhos/' . $ano . '/' . $turma . '/' . $alunoFormatado . '.pdf';
}

$whereClause = !empty($where) ? implode(' AND ', $where) : null;

$resultado = read('trabalhos_integrado', '*', $whereClause, $valores, false, 'ano DESC, turma ASC, aluno ASC');

if(isset($resultado['ano']) && isset($resultado['turma'])){
    $resultado = [$resultado];
}

$trabalhosPorAnoETurma = [];

if($resultado && is_array($resultado)){
    foreach($resultado as $linha){
        $anoL = $linha['ano'];
        $turmaL = $linha['turma'];

        if(!isset($trabalhosPorAnoETurma[$anoL])){
            $trabalhosPorAnoETurma[$anoL] = [];
        }

        if(!isset($trabalhosPorAnoETurma[$anoL][$turmaL])){
            $trabalhosPorAnoETurma[$anoL][$turmaL] = [
                'ano' => $anoL, 
                'turma' => $turmaL, 
                'alunos' => []
            ];
        }
        
        $trabalhosPorAnoETurma[$anoL][$turmaL]['alunos'][] = [
            'nome' => $linha['aluno'],
            'arquivo' => $linha['arquivo']
        ];
    }
}
?>

<?php if(!empty($trabalhosPorAnoETurma)): ?>
    <?php foreach($trabalhosPorAnoETurma as $ano => $turmas): ?>
        <div class="ano-box">
            <h2><?php echo $ano; ?></h2>
            <?php foreach($turmas as $turma => $dados): ?>
                <div class="turma-box">
                    <h3>Turma: <?php echo $turma; ?></h3>
                    <div class="turma-grid">
                        <?php foreach($dados['alunos'] as $aluno): ?>
                            <a href="<?php echo $aluno['arquivo']; ?>" class="trabalho-link" target="_blank">
                                <div class="trabalho-box">
                                    <p><?php echo $aluno['nome']; ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nenhum trabalho encontrado. </p>
<?php endif; ?>