<?php
include 'templates/header.php';
include 'templates/navbar.php';
include 'includes/functions.php';

// Conexão com o banco de gestão de cursos e dados para popular o filtro
$conexaoCursos = getConexaoCursos();
$areas         = fetchAreas($conexaoCursos);
$turnos        = fetchTurnos($conexaoCursos);
$statusList    = fetchStatusTurma($conexaoCursos);
?>
<div class="principal">
    <div class="leftbar">
        <?php include 'templates/leftbarFiltro.php'; ?>
    </div>

    <div class="main">
        <div class="container-cursos">

            <div class="cursos-header">
                <h1>Lista de Cursos</h1>
                <input
                    type="text"
                    id="buscaCurso"
                    class="form-control busca-input"
                    placeholder="Buscar curso pelo nome..."
                >
            </div>

            <div id="cursosGrid" class="cursos-grid">
                <p class="carregando">Carregando cursos...</p>
            </div>

        </div>
    </div>
</div>

<script src="assets/js/cursos.js"></script>
<?php include 'templates/footer.php'; ?>
