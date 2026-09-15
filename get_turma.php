<?php
// Retorna, em JSON, todos os dados de uma turma (curso + turma + dias da semana)
// usados para preencher o formulário de edição no modal do calendário.
header('Content-Type: application/json; charset=utf-8');
require 'includes/functions.php';

$conexaoCursos = getConexaoCursos();

$turmaId = isset($_GET['turma_id']) ? (int) $_GET['turma_id'] : 0;
$dados = $turmaId ? fetchTurmaFormData($conexaoCursos, $turmaId) : null;

echo json_encode($dados);
