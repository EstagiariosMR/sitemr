<?php
require 'includes/crud.php';

$anos = read('trabalhos_integrado', 'DISTINCT ano', false, [], false, 'ano DESC');
$turmas = read('trabalhos_integrado', 'DISTINCT turma', false, [], false, 'turma ASC');
$resultado = read('trabalhos_integrado', '*', false, [], false, 'ano DESC, turma ASC, aluno ASC');

function removerAcentos($string)
{
    $string = htmlentities($string, ENT_NOQUOTES, 'UTF-8');
    $string = preg_replace('/&([a-zA-Z])[a-zA-Z]+;/', '$1', $string);
    return $string;
}

function formatarNomeAluno($nome)
{
    $aluno = removerAcentos($nome);
    $aluno = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $aluno);
    $aluno = preg_replace('/_+/', '_', $aluno);
    $aluno = str_replace(' ', '_', $aluno);
    $aluno = strtolower($aluno);

    return $aluno;
}

if (isset($_POST['btn_trabalhos'])) {
    $ano = $_POST['ano'] ?? null;
    $turma = $_POST['turma'] ?? null;
    $aluno = $_POST['aluno'] ?? null;

    $where = [];
    $valores = [];

    if (!empty($ano)) {
        $where[] = "ano = :ano";
        $valores['ano'] = $ano;
    }

    if (!empty($turma)) {
        $where[] = "turma = :turma";
        $valores['turma'] = $turma;
    }

    if (!empty($aluno)) {
        $where[] = "aluno = :aluno";
        $valores['aluno'] = $aluno;
    }

    if (!empty($aluno)) {
        $alunoFormatado = formatarNomeAluno($aluno);
        $where[] = "arquivo = :arquivo";
        $valores['arquivo'] = 'uploads/trabalhos/' . $ano . '/' . $turma . '/' . $alunoFormatado . '.pdf';
    }

    $whereClause = !empty($where) ? implode(' AND ', $where) : null;

    if ($ano && $turma && !$aluno) {
        // Buscar alunos distintos para esse ano e turma
        $resultado = read('trabalhos_integrado', '*', $whereClause, $valores, false, 'ano DESC, turma ASC, aluno ASC');
    } elseif (!$whereClause) {
        // Consulta sem filtros (todos os registros)
        $resultado = read('trabalhos_integrado', '*', false, [], false, 'ano DESC, turma ASC, aluno ASC');
    } else {
        // Caso contrário, filtra por ano, turma e aluno específico
        $resultado = read('trabalhos_integrado', '*', $whereClause, $valores, true, 'ano DESC, turma ASC, aluno ASC');
    }
}
?>

<main>
    <section class="texto">
        <h1> Ensino Técnico Integrado ao Ensino Médio</h1>

        <h2> Objetivo do Curso</h2>

        <p>
            Proporcionar meios que assegurem a formação humana indispensável para o exercício da cidadania, as
            condições para uma inserção no mundo do trabalho e continuidade dos estudos
            Oportunizar uma formação técnica, sólida pautada por princípios éticos, além de promover o desenvolvimento
            cultural do(a) educando(a) a autonomia intelectual e o pensamento crítico
            Articular teoria e prática potencializando a qualificação profissional
            Promover a articulação da Formação Geral, da Formação Profissional e da Parte Diversificada através da
            linhas de pesquisa definida a partir do curso técnico em foco, na relação desses com os processos produtivos
            locais/regionais, suas tecnologias e respectivos impactos, seja eles, sociais, culturais, ambientais,
            políticos, éticos e econômicos
            Potencializar, através do diálogo entre os Componentes Curriculares dos blocos de estrutura curricular. e
            compreensão dos fundamentos científicos das diferente técnicas e tecnologias utilizadas no curso técnico e
            nos processos produtivos, assim como das transformações ocorridas ao longo da história e a relação entre
            elas e o desenvolvimento local/regional
            Viabilizar a compreensão lógica da construção do conhecimento, convocando a reflexão sobre a relação entre
            esta, o trabalho (agir sobre a natureza transformando-a em função das necessidades humanas da construção de
            sua vida material), a cultura e o desenvolvimento/avanço da ciência e da tecnologia
            Possibilitar aos(as) educando(as) práticas orientadas, permitindo-lhes vivências no mundo do trabalho </p>

        <h2> Perfil Profissional de Conclusão</h2>

        <p>
            O(a) profissional formado(a) na Educação Profissional Técnica de Nível Médio deve ter uma formação
            técnica sólida, capaz de articular os conhecimentos científicos, filosóficos, tecnológicos, sócio
            histórico&nbsp;e pautar-se pelos princípios da ética e da cidadania, buscando o aperfeiçoamento permanente e
            a integração consciente no mundo do trabalho. </p>
    </section>
    <div class="integrado">

        <h1>Trabalhos Integrado</h1>

        <form method="POST" id="form-trabalhos">
            <div class="form-trabalhos">
                <label for="ano"><strong>Ano:</strong></label>
                <select name="ano" id="ano">
                    <option value="" disabled selected>-- Selecione o ano --</option>
                    <?php foreach ($anos as $item): ?>
                        <option value="<?php echo $item['ano']; ?>" <?php if (isset($ano) && $ano == $item['ano'])
                               echo 'selected'; ?>>
                            <?php echo $item['ano']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="turma"><strong>Turma:</strong></label>
                <select name="turma" id="turma">
                    <option value="" disabled selected>-- Selecione a turma --</option>
                    <?php foreach ($turmas as $item): ?>
                        <option value="<?php echo $item['turma']; ?>" <?php if (isset($turma) && $turma == $item['turma'])
                               echo 'selected'; ?>>
                            <?php echo $item['turma']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="aluno"><strong>Nome do aluno:</strong></label>
                <input type="text" name="aluno" id="aluno" placeholder="Pesquise o Nome">

                <button type="submit" name="btn_trabalhos">Pesquisar</button>
                <button type="button" onclick="limparFormulario()">Limpar</button>
            </div>
        </form>

        <?php
        // Verificar se $resultado é um array associativo ou um array com um único resultado
        if (isset($resultado['ano']) && isset($resultado['turma'])) {
            // Se for um único resultado (um array associativo), transformamos ele em um array de um único item
            $resultado = [$resultado];
        }

        // Agrupar por ano e por turma
        if (isset($resultado) && is_array($resultado)) {
            $trabalhosPorAnoETurma = [];

            foreach ($resultado as $linha) {
                $ano = $linha['ano'];
                $turma = $linha['turma'];
                $aluno = $linha['aluno'];
                $arquivo = $linha['arquivo'];  // Pega o arquivo para usá-lo depois
        
                // Inicializa o ano se ainda não existir
                if (!isset($trabalhosPorAnoETurma[$ano])) {
                    $trabalhosPorAnoETurma[$ano] = [];
                }

                // Inicializa a turma dentro do ano se ainda não existir
                if (!isset($trabalhosPorAnoETurma[$ano][$turma])) {
                    $trabalhosPorAnoETurma[$ano][$turma] = [
                        'ano' => $ano,
                        'turma' => $turma,
                        'aluno' => []  // Inicializa o array de alunos
                    ];
                }

                // Agora adiciona o aluno com o arquivo associado
                $trabalhosPorAnoETurma[$ano][$turma]['aluno'][] = [
                    'nome' => $aluno,
                    'arquivo' => $arquivo  // Adiciona o link do arquivo
                ];
            }

        }

        ?>

        <div class="resultado">
            <?php if (!empty($trabalhosPorAnoETurma)): ?>
                <!-- Exibe lista de alunos agrupados por ano e turma -->
                <?php foreach ($trabalhosPorAnoETurma as $ano => $turmas): ?>
                    <div class="ano-box">
                        <h2><?php echo $ano; ?></h2>
                        <?php foreach ($turmas as $turma => $dados): ?>
                            <div class="turma-box">
                                <h3>Turma: <?php echo $turma; ?></h3>
                                <div class="turma-grid">
                                    <?php foreach ($dados['aluno'] as $aluno): ?>
                                        <!-- Certifique-se de que a variável $dados contém o arquivo -->
                                        <?php
                                        // Suponho que cada item de $dados também tem o arquivo
                                        $arquivo = isset($aluno['arquivo']) ? $aluno['arquivo'] : '#';
                                        ?>
                                        <!-- Caixa do aluno clicável que leva ao PDF (usando a coluna 'arquivo') -->
                                        <a href="<?php echo $arquivo; ?>" class="trabalho-link" target="_blank">
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
                <!-- Exibe todos os registros agrupados por ano, turma e aluno -->
                <?php foreach ($trabalhosPorAnoETurma as $ano => $turmas): ?>
                    <div class="ano-box">
                        <h2><?php echo $ano; ?></h2>
                        <?php foreach ($turmas as $turma => $trabalhos): ?>
                            <div class="turma-box">
                                <h3>Turma: <?php echo $turma; ?></h3>
                                <div class="turma-grid">
                                    <?php foreach ($trabalhos as $trabalho): ?>
                                        <!-- Certifique-se de que a variável $trabalho contém o arquivo -->
                                        <?php
                                        // Suponho que cada item de $trabalho tem a chave 'arquivo'
                                        $arquivo = isset($trabalho['arquivo']) ? $trabalho['arquivo'] : '#';
                                        ?>
                                        <!-- Caixa do aluno clicável que leva ao PDF (usando a coluna 'arquivo') -->
                                        <a href="<?php echo $arquivo; ?>" class="trabalho-link" target="_blank">
                                            <div class="trabalho-box">
                                                <p><?php echo $trabalho['aluno']; ?></p>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>



        <script src="/sitemr/assets/js/scrollForm.js"></script>
        <script src="/sitemr/assets/js/limparForm.js"></script>
    </div>
</main>