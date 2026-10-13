<?php
header('Content-Type: application/json; charset=utf-8');
require 'includes/functions.php';

$conexaoCursos = getConexaoCursos();
$eventos = fetchTurmasParaCalendario($conexaoCursos);

echo json_encode($eventos);
