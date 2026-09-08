<aside
    class="sidebar filtro-sidebar"
    aria-label="Filtrar cursos"
>

    <div class="legenda">
        <h2>Filtrar cursos</h2>
    </div>

    <hr>

    <form id="filtroForm" class="filtro-form">

        <div class="filtro-grupo">
            <h3>Área</h3>
            <?php foreach ($areas as $area): ?>
                <label class="filtro-check">
                    <input type="checkbox" name="area[]" value="<?php echo (int) $area['id']; ?>">
                    <?php echo htmlspecialchars($area['nome']); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="filtro-grupo">
            <h3>Turno</h3>
            <?php foreach ($turnos as $turno): ?>
                <label class="filtro-check">
                    <input type="checkbox" name="turno[]" value="<?php echo (int) $turno['id']; ?>">
                    <?php echo htmlspecialchars($turno['descricao']); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="filtro-grupo">
            <h3>Status</h3>
            <?php foreach ($statusList as $status): ?>
                <label class="filtro-check">
                    <input type="checkbox" name="status[]" value="<?php echo (int) $status['id']; ?>">
                    <?php echo htmlspecialchars($status['descricao']); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <button type="button" id="limparFiltros" class="btn-limpar-filtro">
            Limpar filtros
        </button>

    </form>

</aside>
