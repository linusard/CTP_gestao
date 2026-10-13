<?php
header('Content-Type: application/json; charset=utf-8');

require 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
    exit;
}

$turmaId = filter_input(INPUT_POST, 'turma_id', FILTER_VALIDATE_INT);

if ($turmaId === false || $turmaId === null) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Turma inválida.']);
    exit;
}

$conn = getConexaoCursos();
$novoStatusId = avancarStatusCurso($conn, $turmaId);

if ($novoStatusId === null) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível avançar o status deste curso.']);
    exit;
}

$stmt = $conn->prepare("SELECT descricao FROM status_turma WHERE id = ?");
$stmt->execute([$novoStatusId]);
$statusTexto = (string) $stmt->fetchColumn();

if ($statusTexto === '') {
    $statusTexto = 'Status atualizado';
}

echo json_encode([
    'sucesso' => true,
    'novo_status_id' => $novoStatusId,
    'novo_status_texto' => $statusTexto,
]);
