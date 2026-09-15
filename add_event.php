<?php
// Cria um novo curso junto com sua turma (usado pelo botão "Adicionar Curso" do calendário)
header('Content-Type: application/json; charset=utf-8');
require 'includes/functions.php';

$resposta = ['sucesso' => false, 'mensagem' => ''];

$camposObrigatorios = ['nome', 'area_id', 'carga_horaria', 'origem_id', 'data_inicio', 'data_termino', 'turno_id'];
foreach ($camposObrigatorios as $campo) {
    if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
        $resposta['mensagem'] = "Campo obrigatório não preenchido: $campo";
        echo json_encode($resposta);
        exit;
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

$conexaoCursos = getConexaoCursos();

try {
    $turmaId = inserirCursoTurma($conexaoCursos, $dadosCurso, $dadosTurma, $diasSemanaIds);
    $resposta['sucesso']  = true;
    $resposta['turma_id'] = $turmaId;
} catch (Exception $e) {
    $resposta['mensagem'] = $e->getMessage();
}

echo json_encode($resposta);
