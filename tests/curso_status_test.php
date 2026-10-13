<?php
require __DIR__ . '/../includes/functions.php';

function assertEquals($expected, $actual, $message) {
    if ($expected !== $actual) {
        throw new RuntimeException($message . " | esperado: " . var_export($expected, true) . " | obtido: " . var_export($actual, true));
    }
}

try {
    assertEquals(3, proximoStatusCursoId(1), 'Planejado deve avançar para Em andamento');
    assertEquals(3, proximoStatusCursoId(2), 'Inscrições abertas deve avançar para Em andamento');
    assertEquals(4, proximoStatusCursoId(3), 'Em andamento deve avançar para Concluído');
    assertEquals(5, proximoStatusCursoId(4), 'Concluído deve avançar para Cancelado');

    assertEquals(3, calcularStatusCursoAutomatico('2026-10-15', '2026-10-15', '2026-10-20'), 'A partir da data de início, o curso deve entrar em andamento');
    assertEquals(4, calcularStatusCursoAutomatico('2026-10-11', '2026-10-01', '2026-10-10'), 'Quando a data final já passou, o curso deve ficar concluído');

    echo "OK\n";
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
