<?php
include 'templates/header.php';
include 'templates/navbar.php';
include 'includes/functions.php';

// Dados de apoio usados para montar os <select> e checkboxes
// do formulário de "Adicionar/Editar Curso" no modal do calendário
$conexaoCursos = getConexaoCursos();
$areas         = fetchAreas($conexaoCursos);
$turnos        = fetchTurnos($conexaoCursos);
$statusList    = fetchStatusTurma($conexaoCursos);
$origens       = fetchOrigens($conexaoCursos);
$locais        = fetchLocais($conexaoCursos);
$diasSemana    = fetchDiasSemana($conexaoCursos);
?>
<div class="principal">
    <div class="leftbar">
        <?php include 'templates/leftbar.php'; ?>
    </div>

    <div class="main">
        <?php include 'templates/calendario.php'; ?>
    </div>
</div>
<?php include 'templates/footer.php'; ?>
