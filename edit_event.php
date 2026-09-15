<?php
// Atualiza uma turma existente. Suporta dois modos:
// - Atualização rápida (arrastar/soltar no calendário): apenas as datas.
// - Atualização completa (formulário do modal): curso, turma e dias da semana.
header('Content-Type: application/json; charset=utf-8');
require 'includes/functions.php';

$resposta = ['sucesso' => false, 'mensagem' => ''];

if (!isset($_POST['turma_id']) || $_POST['turma_id'] === '') {
    $resposta['mensagem'] = 'ID da turma não informado.';
    echo json_encode($resposta);
    exit;
}

$turmaId = (int) $_POST['turma_id'];
$conexaoCursos = getConexaoCursos();

try {
    if (!empty($_POST['apenas_datas'])) {
        if (empty($_POST['data_inicio']) || empty($_POST['data_termino'])) {
            throw new Exception('Datas não informadas.');
        }

        atualizarDatasTurma($conexaoCursos, $turmaId, $_POST['data_inicio'], $_POST['data_termino']);
    } else {
        $camposObrigatorios = ['nome', 'area_id', 'carga_horaria', 'origem_id', 'data_inicio', 'data_termino', 'turno_id'];
        foreach ($camposObrigatorios as $campo) {
            if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                throw new Exception("Campo obrigatório não preenchido: $campo");
            }
        }

        $dadosCurso = [
            'nome'                            => $_POST['nome'],
            'area_id'                         => (int) $_POST['area_id'],
            'carga_horaria'                   => (int) $_POST['carga_horaria'],
            'idade_minima'                    => $_POST['idade_minima'] !== '' ? (int) $_POST['idade_minima'] : 16,
            'escolaridade_minima'             => $_POST['escolaridade_minima'] !== '' ? $_POST['escolaridade_minima'] : 'Não pedido',
            'requer_cpf'                      => !empty($_POST['requer_cpf']) ? 1 : 0,
            'requer_comprovante_residencia'   => !empty($_POST['requer_comprovante_residencia']) ? 1 : 0,
            'requer_comprovante_escolaridade' => !empty($_POST['requer_comprovante_escolaridade']) ? 1 : 0,
        ];

        $dadosTurma = [
            'numero_turma'            => $_POST['numero_turma'] !== '' ? $_POST['numero_turma'] : null,
            'origem_id'               => (int) $_POST['origem_id'],
            'data_inicio'             => $_POST['data_inicio'],
            'data_termino'            => $_POST['data_termino'],
            'turno_id'                => (int) $_POST['turno_id'],
            'data_limite_inscricao'   => $_POST['data_limite_inscricao'] !== '' ? $_POST['data_limite_inscricao'] : null,
            'local_inscricao_id'      => $_POST['local_inscricao_id'] !== '' ? (int) $_POST['local_inscricao_id'] : null,
            'vagas_estimadas'         => $_POST['vagas_estimadas'] !== '' ? (int) $_POST['vagas_estimadas'] : null,
            'link_sistema_inscricao'  => $_POST['link_sistema_inscricao'] !== '' ? $_POST['link_sistema_inscricao'] : null,
            'link_planilha_matricula' => $_POST['link_planilha_matricula'] !== '' ? $_POST['link_planilha_matricula'] : null,
            'local_aula_id'           => $_POST['local_aula_id'] !== '' ? (int) $_POST['local_aula_id'] : null,
            'status_id'               => $_POST['status_id'] !== '' ? (int) $_POST['status_id'] : 1,
        ];

        $diasSemanaIds = isset($_POST['dias_semana']) ? array_map('intval', (array) $_POST['dias_semana']) : [];

        atualizarCursoTurma($conexaoCursos, $turmaId, $dadosCurso, $dadosTurma, $diasSemanaIds);
    }

    $resposta['sucesso'] = true;
} catch (Exception $e) {
    $resposta['mensagem'] = $e->getMessage();
}

echo json_encode($resposta);
