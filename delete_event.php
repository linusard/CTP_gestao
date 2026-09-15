<?php
// Exclui uma turma (e o curso associado, se ele não tiver mais nenhuma turma)
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
    excluirTurma($conexaoCursos, $turmaId);
    $resposta['sucesso'] = true;
} catch (Exception $e) {
    $resposta['mensagem'] = $e->getMessage();
}

echo json_encode($resposta);
