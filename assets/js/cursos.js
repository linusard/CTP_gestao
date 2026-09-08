document.addEventListener('DOMContentLoaded', function () {
    const grid = document.getElementById('cursosGrid');
    const form = document.getElementById('filtroForm');
    const buscaInput = document.getElementById('buscaCurso');
    const limparBtn = document.getElementById('limparFiltros');

    if (!grid || !form) {
        return; // Página sem o bloco de lista de cursos
    }

    let buscaTimeout = null;
    const coresArea = ['azul', 'rosa', 'verde', 'amarelo', 'roxo'];

    function corPorArea(areaId) {
        const indice = (parseInt(areaId, 10) - 1) % coresArea.length;
        return coresArea[indice >= 0 ? indice : 0];
    }

    function formatarData(dataStr) {
        if (!dataStr) {
            return 'Não definido';
        }
        const partes = dataStr.split('-');
        if (partes.length !== 3) {
            return dataStr;
        }
        const [ano, mes, dia] = partes;
        return `${dia}/${mes}/${ano}`;
    }

    function montarCard(curso) {
        const card = document.createElement('article');
        card.className = 'curso-card ' + corPorArea(curso.area_id);

        const nome = curso.nome ?? 'Curso sem nome';
        const area = curso.area ?? 'Não definido';
        const status = curso.status ?? 'Não definido';
        const turno = curso.turno ?? 'Não definido';
        const numeroTurma = curso.numero_turma ?? 'Não definido';
        const vagas = curso.vagas_estimadas ?? 'Não definido';

        card.innerHTML = `
            <div class="curso-card-topo">
                <span class="curso-area">${area}</span>
                <span class="curso-status status-${curso.status_id}">${status}</span>
            </div>
            <h3>${nome}</h3>
            <p class="curso-info-linha"><strong>Turma:</strong> ${numeroTurma}</p>
            <p class="curso-info-linha"><strong>Turno:</strong> ${turno}</p>
            <p class="curso-info-linha"><strong>Período:</strong> ${formatarData(curso.data_inicio)} a ${formatarData(curso.data_termino)}</p>
            <p class="curso-info-linha"><strong>Vagas:</strong> ${vagas}</p>
            <a class="curso-card-link" href="cursoinfo.php?turma_id=${curso.turma_id}">Ver detalhes</a>
        `;

        return card;
    }

    function renderCursos(cursos) {
        grid.innerHTML = '';

        if (!Array.isArray(cursos) || cursos.length === 0) {
            grid.innerHTML = '<p class="sem-resultado">Nenhum curso encontrado com esses filtros.</p>';
            return;
        }

        const fragmento = document.createDocumentFragment();
        cursos.forEach(curso => fragmento.appendChild(montarCard(curso)));
        grid.appendChild(fragmento);
    }

    function montarQueryString() {
        const params = new URLSearchParams();

        form.querySelectorAll('input[name="area[]"]:checked')
            .forEach(el => params.append('area[]', el.value));

        form.querySelectorAll('input[name="turno[]"]:checked')
            .forEach(el => params.append('turno[]', el.value));

        form.querySelectorAll('input[name="status[]"]:checked')
            .forEach(el => params.append('status[]', el.value));

        if (buscaInput.value.trim() !== '') {
            params.append('busca', buscaInput.value.trim());
        }

        return params.toString();
    }

    function carregarCursos() {
        grid.innerHTML = '<p class="carregando">Carregando cursos...</p>';
        const query = montarQueryString();

        fetch('fetch_cursos.php' + (query ? '?' + query : ''))
            .then(resposta => {
                if (!resposta.ok) {
                    throw new Error('Erro na resposta do servidor');
                }
                return resposta.json();
            })
            .then(renderCursos)
            .catch(() => {
                grid.innerHTML = '<p class="sem-resultado">Erro ao carregar os cursos. Tente novamente.</p>';
            });
    }

    // Filtragem dinâmica ao marcar/desmarcar checkboxes
    form.addEventListener('change', function (evento) {
        if (evento.target.matches('input[type="checkbox"]')) {
            carregarCursos();
        }
    });

    // Busca por nome com pequeno atraso para não disparar a cada tecla
    buscaInput.addEventListener('input', function () {
        clearTimeout(buscaTimeout);
        buscaTimeout = setTimeout(carregarCursos, 350);
    });

    // Botão para limpar todos os filtros
    limparBtn.addEventListener('click', function () {
        form.querySelectorAll('input[type="checkbox"]').forEach(el => {
            el.checked = false;
        });
        buscaInput.value = '';
        carregarCursos();
    });

    // Primeiro carregamento, sem filtros
    carregarCursos();
});
