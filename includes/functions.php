<?php
// Conexão com o banco do calendário de eventos (já existente)
require 'db.php';

/**
 * Função de saída segura de um campo de um array associativo.
 */
function passarDadosCurso(array $curso, string $campo) {
    if (isset($curso[$campo]) && $curso[$campo] !== '') {
        echo htmlspecialchars((string) $curso[$campo]);
    } else {
        echo "Não informado";
    }
}

// Função para buscar todos os eventos do calendário
function fetchEvents($conn) {
    $stmt = $conn->prepare("SELECT * FROM events");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* =========================================================
 *  Conexão e funções do banco de gestão de cursos (gestao_cursos)
 * ========================================================= */

/**
 * Abre uma conexão PDO com o banco gestao_cursos.
 * Mantida separada de includes/db.php pois esse arquivo usa
 * outro banco (event_calendar), dedicado ao calendário.
 */
function getConexaoCursos() {
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "gestao_cursos";

    try {
        $conn = new PDO(
            "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
            $username,
            $password
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Erro de conexão com o banco de cursos: " . $e->getMessage());
    }
}

/**
 * Lista as áreas cadastradas (usadas no filtro).
 */
function fetchAreas($conn) {
    return $conn->query("SELECT id, nome FROM areas ORDER BY nome")
                ->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lista os turnos cadastrados (usados no filtro).
 */
function fetchTurnos($conn) {
    return $conn->query("SELECT id, descricao FROM turnos ORDER BY id")
                ->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lista os status de turma cadastrados (usados no filtro).
 */
function fetchStatusTurma($conn) {
    return $conn->query("SELECT id, descricao FROM status_turma ORDER BY id")
                ->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Retorna os dias da semana de uma turma, já formatados
 * como uma string separada por vírgulas (ex: "Segunda, Quarta").
 */
function fetchDiasSemanaPorTurma($conn, $turmaId) {
    $sql = "SELECT ds.nome
            FROM turma_dias_semana tds
            INNER JOIN dias_semana ds ON ds.id = tds.dia_semana_id
            WHERE tds.turma_id = ?
            ORDER BY ds.id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$turmaId]);
    $dias = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return $dias ? implode(', ', $dias) : 'Não definido';
}

/**
 * Busca a lista de turmas/cursos para exibição em cards,
 * podendo ser filtrada por área, turno, status e nome (busca).
 *
 * $filtros aceita as chaves:
 *   'area_ids'   => array de ids de área
 *   'turno_ids'  => array de ids de turno
 *   'status_ids' => array de ids de status
 *   'busca'      => string para buscar no nome do curso
 */
function fetchCursos($conn, array $filtros = []) {
    $sql = "SELECT
                t.id AS turma_id,
                c.id AS curso_id,
                c.nome AS nome,
                a.id AS area_id,
                a.nome AS area,
                c.carga_horaria,
                t.numero_turma,
                t.data_inicio,
                t.data_termino,
                tu.id AS turno_id,
                tu.descricao AS turno,
                t.data_limite_inscricao,
                t.vagas_estimadas,
                st.id AS status_id,
                st.descricao AS status,
                o.nome AS origem
            FROM turmas t
            INNER JOIN cursos c ON c.id = t.curso_id
            INNER JOIN areas a ON a.id = c.area_id
            INNER JOIN turnos tu ON tu.id = t.turno_id
            INNER JOIN status_turma st ON st.id = t.status_id
            LEFT JOIN origens o ON o.id = t.origem_id
            WHERE 1 = 1";

    $params = [];

    if (!empty($filtros['area_ids'])) {
        $placeholders = implode(',', array_fill(0, count($filtros['area_ids']), '?'));
        $sql .= " AND a.id IN ($placeholders)";
        foreach ($filtros['area_ids'] as $id) {
            $params[] = (int) $id;
        }
    }

    if (!empty($filtros['turno_ids'])) {
        $placeholders = implode(',', array_fill(0, count($filtros['turno_ids']), '?'));
        $sql .= " AND tu.id IN ($placeholders)";
        foreach ($filtros['turno_ids'] as $id) {
            $params[] = (int) $id;
        }
    }

    if (!empty($filtros['status_ids'])) {
        $placeholders = implode(',', array_fill(0, count($filtros['status_ids']), '?'));
        $sql .= " AND st.id IN ($placeholders)";
        foreach ($filtros['status_ids'] as $id) {
            $params[] = (int) $id;
        }
    }

    if (!empty($filtros['busca'])) {
        $sql .= " AND c.nome LIKE ?";
        $params[] = '%' . $filtros['busca'] . '%';
    }

    $sql .= " ORDER BY t.data_inicio ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Busca todas as informações de uma turma/curso específico
 * (usado na página cursoinfo.php).
 */
function fetchCursoPorTurmaId($conn, $turmaId) {
    $sql = "SELECT
                t.id AS turma_id,
                c.id AS curso_id,
                c.nome AS nome,
                a.nome AS area,
                c.carga_horaria,
                c.idade_minima,
                c.escolaridade_minima,
                c.requer_cpf,
                c.requer_comprovante_residencia,
                c.requer_comprovante_escolaridade,
                t.numero_turma,
                t.data_inicio,
                t.data_termino,
                tu.descricao AS turno,
                t.data_limite_inscricao,
                t.vagas_estimadas,
                t.link_sistema_inscricao,
                t.link_planilha_matricula,
                st.descricao AS status,
                o.nome AS origem,
                li.nome AS local_inscricao,
                la.nome AS local_aula
            FROM turmas t
            INNER JOIN cursos c ON c.id = t.curso_id
            INNER JOIN areas a ON a.id = c.area_id
            INNER JOIN turnos tu ON tu.id = t.turno_id
            INNER JOIN status_turma st ON st.id = t.status_id
            LEFT JOIN origens o ON o.id = t.origem_id
            LEFT JOIN locais li ON li.id = t.local_inscricao_id
            LEFT JOIN locais la ON la.id = t.local_aula_id
            WHERE t.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$turmaId]);
    $turma = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($turma) {
        $turma['dias_semana'] = fetchDiasSemanaPorTurma($conn, $turmaId);
    }

    return $turma;
}
?>
