<?php
// Endpoint AJAX: retorna a lista de turmas/cursos em JSON,
// já aplicando os filtros recebidos por GET.
header('Content-Type: application/json; charset=utf-8');

require 'includes/functions.php';

$conexaoCursos = getConexaoCursos();

$filtros = [
    'area_ids'   => $_GET['area'] ?? [],
    'turno_ids'  => $_GET['turno'] ?? [],
    'status_ids' => $_GET['status'] ?? [],
    'busca'      => $_GET['busca'] ?? '',
];

$cursos = fetchCursos($conexaoCursos, $filtros);

echo json_encode($cursos);
