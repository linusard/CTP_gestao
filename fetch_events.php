<?php
// Fornece os eventos do calendário (FullCalendar) a partir das turmas cadastradas
header('Content-Type: application/json; charset=utf-8');
require 'includes/functions.php';

$conexaoCursos = getConexaoCursos();
$eventos = fetchTurmasParaCalendario($conexaoCursos);

echo json_encode($eventos);
