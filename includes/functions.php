<?php
// Conexão padrão do sistema (gestao_cursos)
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

/* =========================================================
 *  Conexão com o banco de gestão de cursos (gestao_cursos)
 * ========================================================= */

/**
 * Abre uma nova conexão PDO com o banco gestao_cursos.
 * Usada nas páginas que não precisam da conexão global $conn.
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

/* =========================================================
 *  Listas de apoio (usadas em filtros e nos formulários)
 * ========================================================= */

function fetchAreas($conn) {
    return $conn->query("SELECT id, nome FROM areas ORDER BY nome")
                ->fetchAll(PDO::FETCH_ASSOC);
}

function fetchTurnos($conn) {
    return $conn->query("SELECT id, descricao FROM turnos ORDER BY id")
                ->fetchAll(PDO::FETCH_ASSOC);
}

function fetchStatusTurma($conn) {
    return $conn->query("SELECT id, descricao FROM status_turma ORDER BY id")
                ->fetchAll(PDO::FETCH_ASSOC);
}

function fetchOrigens($conn) {
    return $conn->query("SELECT id, nome FROM origens ORDER BY nome")
                ->fetchAll(PDO::FETCH_ASSOC);
}

function fetchLocais($conn) {
    return $conn->query("SELECT id, nome FROM locais ORDER BY nome")
                ->fetchAll(PDO::FETCH_ASSOC);
}

function fetchDiasSemana($conn) {
    return $conn->query("SELECT id, nome FROM dias_semana ORDER BY id")
                ->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Retorna os dias da semana de uma turma já formatados
 * como uma string separada por vírgulas (ex: "Segunda-feira, Quarta-feira").
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

/* =========================================================
 *  Listagem de cursos/turmas (listaCursos.php)
 * ========================================================= */

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

/* =========================================================
 *  Calendário (fetch_events.php)
 * ========================================================= */

/**
 * Busca as turmas cadastradas e monta o formato de evento
 * esperado pelo FullCalendar (id, title, start, end, cor).
 */
function fetchTurmasParaCalendario($conn) {
    $sql = "SELECT
                t.id AS id,
                c.nome AS curso_nome,
                t.numero_turma,
                t.data_inicio,
                t.data_termino,
                t.status_id
            FROM turmas t
            INNER JOIN cursos c ON c.id = t.curso_id
            ORDER BY t.data_inicio ASC";

    $stmt = $conn->query($sql);
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mesmas cores usadas nos badges de status dos cards (assets/css/style.css)
    $coresStatus = [
        1 => '#9AA0A6', // Planejado
        2 => '#3CB878', // Inscrições abertas
        3 => '#4C8BF5', // Em andamento
        4 => '#555555', // Concluído
        5 => '#E5484D', // Cancelado
    ];

    $eventos = [];
    foreach ($turmas as $turma) {
        $titulo = $turma['curso_nome'];
        if (!empty($turma['numero_turma'])) {
            $titulo .= ' - ' . $turma['numero_turma'];
        }

        // O FullCalendar trata o "end" de eventos de dia inteiro como exclusivo,
        // por isso somamos 1 dia à data de término (inclusiva no banco).
        $termino = new DateTime($turma['data_termino']);
        $termino->modify('+1 day');

        $cor = $coresStatus[$turma['status_id']] ?? '#4C8BF5';

        $eventos[] = [
            'id'              => $turma['id'],
            'title'           => $titulo,
            'start'           => $turma['data_inicio'],
            'end'             => $termino->format('Y-m-d'),
            'allDay'          => true,
            'backgroundColor' => $cor,
            'borderColor'     => $cor,
        ];
    }

    return $eventos;
}

/* =========================================================
 *  Criação / edição / exclusão de curso + turma
 *  (add_event.php, edit_event.php, delete_event.php, get_turma.php)
 * ========================================================= */

/**
 * Cria um novo curso e sua turma associada em uma única operação
 * (é assim que o modal "Adicionar Curso" do calendário funciona:
 * cada turma nova é criada junto com seu próprio curso).
 *
 * Retorna o id da turma criada.
 */
function inserirCursoTurma($conn, array $dadosCurso, array $dadosTurma, array $diasSemanaIds) {
    $conn->beginTransaction();

    try {
        $sqlCurso = "INSERT INTO cursos
                        (nome, area_id, carga_horaria, idade_minima, escolaridade_minima,
                         requer_cpf, requer_comprovante_residencia, requer_comprovante_escolaridade)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlCurso);
        $stmt->execute([
            $dadosCurso['nome'],
            $dadosCurso['area_id'],
            $dadosCurso['carga_horaria'],
            $dadosCurso['idade_minima'],
            $dadosCurso['escolaridade_minima'],
            $dadosCurso['requer_cpf'],
            $dadosCurso['requer_comprovante_residencia'],
            $dadosCurso['requer_comprovante_escolaridade'],
        ]);
        $cursoId = (int) $conn->lastInsertId();

        $sqlTurma = "INSERT INTO turmas
                        (curso_id, numero_turma, origem_id, data_inicio, data_termino, turno_id,
                         data_limite_inscricao, local_inscricao_id, vagas_estimadas,
                         link_sistema_inscricao, link_planilha_matricula, local_aula_id, status_id)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlTurma);
        $stmt->execute([
            $cursoId,
            $dadosTurma['numero_turma'],
            $dadosTurma['origem_id'],
            $dadosTurma['data_inicio'],
            $dadosTurma['data_termino'],
            $dadosTurma['turno_id'],
            $dadosTurma['data_limite_inscricao'],
            $dadosTurma['local_inscricao_id'],
            $dadosTurma['vagas_estimadas'],
            $dadosTurma['link_sistema_inscricao'],
            $dadosTurma['link_planilha_matricula'],
            $dadosTurma['local_aula_id'],
            $dadosTurma['status_id'],
        ]);
        $turmaId = (int) $conn->lastInsertId();

        salvarDiasSemanaDaTurma($conn, $turmaId, $diasSemanaIds);

        $conn->commit();
        return $turmaId;
    } catch (Exception $e) {
        $conn->rollBack();
        throw new Exception('Não foi possível salvar o curso: ' . $e->getMessage());
    }
}

/**
 * Atualiza o curso e a turma associados a uma turma existente
 * (usado quando o formulário completo do modal é salvo).
 */
function atualizarCursoTurma($conn, $turmaId, array $dadosCurso, array $dadosTurma, array $diasSemanaIds) {
    $stmt = $conn->prepare("SELECT curso_id FROM turmas WHERE id = ?");
    $stmt->execute([$turmaId]);
    $turma = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$turma) {
        throw new Exception('Turma não encontrada.');
    }

    $cursoId = $turma['curso_id'];

    $conn->beginTransaction();

    try {
        $sqlCurso = "UPDATE cursos SET
                        nome = ?, area_id = ?, carga_horaria = ?, idade_minima = ?, escolaridade_minima = ?,
                        requer_cpf = ?, requer_comprovante_residencia = ?, requer_comprovante_escolaridade = ?
                     WHERE id = ?";
        $stmt = $conn->prepare($sqlCurso);
        $stmt->execute([
            $dadosCurso['nome'],
            $dadosCurso['area_id'],
            $dadosCurso['carga_horaria'],
            $dadosCurso['idade_minima'],
            $dadosCurso['escolaridade_minima'],
            $dadosCurso['requer_cpf'],
            $dadosCurso['requer_comprovante_residencia'],
            $dadosCurso['requer_comprovante_escolaridade'],
            $cursoId,
        ]);

        $sqlTurma = "UPDATE turmas SET
                        numero_turma = ?, origem_id = ?, data_inicio = ?, data_termino = ?, turno_id = ?,
                        data_limite_inscricao = ?, local_inscricao_id = ?, vagas_estimadas = ?,
                        link_sistema_inscricao = ?, link_planilha_matricula = ?, local_aula_id = ?, status_id = ?
                     WHERE id = ?";
        $stmt = $conn->prepare($sqlTurma);
        $stmt->execute([
            $dadosTurma['numero_turma'],
            $dadosTurma['origem_id'],
            $dadosTurma['data_inicio'],
            $dadosTurma['data_termino'],
            $dadosTurma['turno_id'],
            $dadosTurma['data_limite_inscricao'],
            $dadosTurma['local_inscricao_id'],
            $dadosTurma['vagas_estimadas'],
            $dadosTurma['link_sistema_inscricao'],
            $dadosTurma['link_planilha_matricula'],
            $dadosTurma['local_aula_id'],
            $dadosTurma['status_id'],
            $turmaId,
        ]);

        salvarDiasSemanaDaTurma($conn, $turmaId, $diasSemanaIds);

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        throw new Exception('Não foi possível atualizar o curso: ' . $e->getMessage());
    }
}

/**
 * Substitui os dias da semana de uma turma pelos ids informados.
 */
function salvarDiasSemanaDaTurma($conn, $turmaId, array $diasSemanaIds) {
    $stmt = $conn->prepare("DELETE FROM turma_dias_semana WHERE turma_id = ?");
    $stmt->execute([$turmaId]);

    if (!$diasSemanaIds) {
        return;
    }

    $sql = "INSERT INTO turma_dias_semana (turma_id, dia_semana_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    foreach (array_unique($diasSemanaIds) as $diaId) {
        $stmt->execute([$turmaId, $diaId]);
    }
}

/**
 * Atualização rápida usada ao arrastar/soltar um evento no calendário:
 * altera apenas as datas de início e término da turma.
 */
function atualizarDatasTurma($conn, $turmaId, $dataInicio, $dataTermino) {
    $stmt = $conn->prepare("SELECT id FROM turmas WHERE id = ?");
    $stmt->execute([$turmaId]);
    if (!$stmt->fetch()) {
        throw new Exception('Turma não encontrada.');
    }

    $stmt = $conn->prepare("UPDATE turmas SET data_inicio = ?, data_termino = ? WHERE id = ?");
    $stmt->execute([$dataInicio, $dataTermino, $turmaId]);
}

/**
 * Exclui uma turma. Se o curso associado não tiver mais nenhuma
 * outra turma, o curso também é removido (cada turma criada pelo
 * modal "Adicionar Curso" tem seu próprio curso dedicado).
 */
function excluirTurma($conn, $turmaId) {
    $stmt = $conn->prepare("SELECT curso_id FROM turmas WHERE id = ?");
    $stmt->execute([$turmaId]);
    $turma = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$turma) {
        throw new Exception('Turma não encontrada.');
    }

    $cursoId = $turma['curso_id'];

    $stmt = $conn->prepare("DELETE FROM turmas WHERE id = ?");
    $stmt->execute([$turmaId]);

    $stmt = $conn->prepare("SELECT COUNT(*) FROM turmas WHERE curso_id = ?");
    $stmt->execute([$cursoId]);
    $totalTurmasRestantes = (int) $stmt->fetchColumn();

    if ($totalTurmasRestantes === 0) {
        $stmt = $conn->prepare("DELETE FROM cursos WHERE id = ?");
        $stmt->execute([$cursoId]);
    }
}

/**
 * Busca todos os dados (curso + turma + dias da semana selecionados)
 * necessários para popular o formulário de edição do modal.
 */
function fetchTurmaFormData($conn, $turmaId) {
    $sql = "SELECT
                t.id AS turma_id,
                c.id AS curso_id,
                c.nome,
                c.area_id,
                c.carga_horaria,
                c.idade_minima,
                c.escolaridade_minima,
                c.requer_cpf,
                c.requer_comprovante_residencia,
                c.requer_comprovante_escolaridade,
                t.numero_turma,
                t.origem_id,
                t.data_inicio,
                t.data_termino,
                t.turno_id,
                t.data_limite_inscricao,
                t.local_inscricao_id,
                t.vagas_estimadas,
                t.link_sistema_inscricao,
                t.link_planilha_matricula,
                t.local_aula_id,
                t.status_id
            FROM turmas t
            INNER JOIN cursos c ON c.id = t.curso_id
            WHERE t.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$turmaId]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dados) {
        return null;
    }

    $stmt = $conn->prepare("SELECT dia_semana_id FROM turma_dias_semana WHERE turma_id = ?");
    $stmt->execute([$turmaId]);
    $dados['dias_semana_ids'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return $dados;
}
?>
