<?php
include 'templates/header.php';
include 'templates/navbar.php';
include 'includes/functions.php';

$conexaoCursos = getConexaoCursos();

$turmaId   = isset($_GET['turma_id']) ? (int) $_GET['turma_id'] : 0;
$infoCurso = $turmaId ? fetchCursoPorTurmaId($conexaoCursos, $turmaId) : null;
?>
<div class="principal">
    <div class="leftbar">
        <?php include 'templates/leftbar.php'; ?>
    </div>

    <div class="main">
        <div class="container curso-detalhe">

            <?php if (!$infoCurso): ?>

                <h1>Curso não encontrado</h1>
                <p>
                    Verifique se o link acessado está correto ou
                    <a href="listaCursos.php">volte para a lista de cursos</a>.
                </p>

            <?php else: ?>

                <h1>Informações do curso</h1>

                <div class="curso-detalhe-grid">
                    <p><strong>Status:</strong> <?php passarDadosCurso($infoCurso, 'status'); ?></p>
                    <p><strong>Nome:</strong> <?php passarDadosCurso($infoCurso, 'nome'); ?></p>
                    <p><strong>Área:</strong> <?php passarDadosCurso($infoCurso, 'area'); ?></p>
                    <p><strong>Carga Horária:</strong> <?php passarDadosCurso($infoCurso, 'carga_horaria'); ?> horas</p>
                    <p><strong>Turma:</strong> <?php passarDadosCurso($infoCurso, 'numero_turma'); ?></p>
                    <p>
                        <strong>Data de Início:</strong>
                        <?php echo $infoCurso['data_inicio'] ? date('d/m/Y', strtotime($infoCurso['data_inicio'])) : 'Não definido'; ?>
                    </p>
                    <p>
                        <strong>Data do Fim:</strong>
                        <?php echo $infoCurso['data_termino'] ? date('d/m/Y', strtotime($infoCurso['data_termino'])) : 'Não definido'; ?>
                    </p>
                    <p><strong>Turno:</strong> <?php passarDadosCurso($infoCurso, 'turno'); ?></p>
                    <p><strong>Dias da Semana:</strong> <?php passarDadosCurso($infoCurso, 'dias_semana'); ?></p>
                    <p>
                        <strong>Prazo de Inscrição:</strong>
                        <?php echo $infoCurso['data_limite_inscricao'] ? date('d/m/Y', strtotime($infoCurso['data_limite_inscricao'])) : 'Não definido'; ?>
                    </p>
                    <p><strong>Local de Inscrição:</strong> <?php passarDadosCurso($infoCurso, 'local_inscricao'); ?></p>
                    <p><strong>Local da Aula:</strong> <?php passarDadosCurso($infoCurso, 'local_aula'); ?></p>
                    <p><strong>Vagas Previstas:</strong> <?php passarDadosCurso($infoCurso, 'vagas_estimadas'); ?></p>
                    <p><strong>Idade Mínima:</strong> <?php passarDadosCurso($infoCurso, 'idade_minima'); ?> anos</p>
                    <p><strong>Escolaridade Mínima:</strong> <?php passarDadosCurso($infoCurso, 'escolaridade_minima'); ?></p>
                    <p><strong>Instituição de Origem:</strong> <?php passarDadosCurso($infoCurso, 'origem'); ?></p>

                    <p>
                        <strong>Documentos Necessários:</strong>
                        <?php
                            $documentos = [
                                'CPF'                          => $infoCurso['requer_cpf'],
                                'Comprovante de Residência'    => $infoCurso['requer_comprovante_residencia'],
                                'Comprovante de Escolaridade'  => $infoCurso['requer_comprovante_escolaridade'],
                            ];
                            $exigidos = array_keys(array_filter($documentos));
                            echo $exigidos ? implode(', ', $exigidos) : 'Nenhum documento adicional exigido';
                        ?>
                    </p>
                </div>

                <div class="curso-detalhe-links">
                    <?php if (!empty($infoCurso['link_sistema_inscricao'])): ?>
                        <a
                            class="btn-curso-link"
                            href="<?php echo htmlspecialchars($infoCurso['link_sistema_inscricao']); ?>"
                            target="_blank"
                            rel="noopener"
                        >
                            Ir para inscrição
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($infoCurso['link_planilha_matricula'])): ?>
                        <a
                            class="btn-curso-link secundario"
                            href="<?php echo htmlspecialchars($infoCurso['link_planilha_matricula']); ?>"
                            target="_blank"
                            rel="noopener"
                        >
                            Ver planilha de matrícula
                        </a>
                    <?php endif; ?>
                </div>

                <a class="voltar-lista" href="listaCursos.php">&larr; Voltar para lista de cursos</a>

            <?php endif; ?>

        </div>
    </div>
</div>
<?php include 'templates/footer.php'; ?>
