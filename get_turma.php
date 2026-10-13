<?php
header('Content-Type: application/json; charset=utf-8');
require 'includes/functions.php';

$conexaoCursos = getConexaoCursos();

$turmaId = isset($_GET['turma_id']) ? (int) $_GET['turma_id'] : 0;
$dados = $turmaId ? fetchTurmaFormData($conexaoCursos, $turmaId) : null;

echo json_encode($dados);
