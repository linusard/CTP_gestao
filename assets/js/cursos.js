document.addEventListener('DOMContentLoaded', function () {
    const grid = document.getElementById('cursosGrid');
    const form = document.getElementById('filtroForm');
    const buscaInput = document.getElementById('buscaCurso');
    const limparBtn = document.getElementById('limparFiltros');

    if (!grid) {
        return;
    }

    const statusTexto = {
        1: 'Planejado',
        2: 'Inscrições abertas',
        3: 'Em andamento',
        4: 'Concluído',
        5: 'Cancelado'
    };

    const areaClasses = {
        1: 'saude',
        2: 'estetica',
        3: 'gastronomia',
        4: 'tecnologia',
        5: 'outro'
    };

    let buscaTimeout = null;

    function corPorArea(areaId) {
        const id = parseInt(areaId, 10);
        return areaClasses[id] || 'outro';
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

    function formatarStatus(status) {
        const valor = String(status || '').trim().toUpperCase();

        const mapa = {
            'PLANEJADO': 'Planejado',
            'INSCRICOES ABERTAS': 'Inscrições abertas',
            'INSCRIÇÕES ABERTAS': 'Inscrições abertas',
            'EM ANDAMENTO': 'Em andamento',
            'CONCLUIDO': 'Concluído',
            'CANCELADO': 'Cancelado'
        };

        return mapa[valor] || status || 'Não definido';
    }

    function criarContainerToasts() {
        let container = document.getElementById('toastContainer');

        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        return container;
    }

    function mostrarToast(mensagem, tipo = 'info') {
        const container = criarContainerToasts();
        const toast = document.createElement('div');
        toast.className = `toast-item ${tipo}`;
        toast.innerHTML = `
            <div>${mensagem}</div>
            <button type="button" class="toast-close" aria-label="Fechar notificação">×</button>
        `;

        const closeButton = toast.querySelector('.toast-close');
        closeButton.addEventListener('click', function () {
            toast.remove();
        });

        container.appendChild(toast);

        setTimeout(function () {
            toast.remove();
        }, 6000);
    }

    function exibirNotificacoes(cursos) {
        if (!Array.isArray(cursos) || cursos.length === 0) {
            return;
        }

        const hoje = new Date();
        const notificacoes = [];

        cursos.forEach(function (curso) {
            if (!curso.data_inicio || !curso.data_termino) {
                return;
            }

            const dataInicio = new Date(`${curso.data_inicio}T00:00:00`);
            const dataTermino = new Date(`${curso.data_termino}T00:00:00`);
            const diferencaInicio = Math.ceil((dataInicio - hoje) / 86400000);
            const diferencaTermino = Math.ceil((dataTermino - hoje) / 86400000);

            if (diferencaInicio === 5) {
                notificacoes.push(`Faltam 5 dias para o início do curso "${curso.nome}". Recomendamos alertar os alunos.`);
            }

            if (diferencaTermino === 0) {
                notificacoes.push(`Hoje é o dia de encerramento do curso "${curso.nome}".`);
            }
        });

        notificacoes.slice(0, 3).forEach(function (mensagem, index) {
            setTimeout(function () {
                mostrarToast(mensagem, index === 0 ? 'warning' : 'info');
            }, index * 250);
        });
    }

    function montarCard(curso) {
        const card = document.createElement('article');
        card.className = 'curso-card ' + corPorArea(curso.area_id);

        const nome = curso.nome ?? 'Curso sem nome';
        const area = curso.area ?? 'Não definido';
        const status = formatarStatus(curso.status);
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
            <div class="curso-card-acoes">
                <a class="curso-card-link" href="cursoinfo.php?turma_id=${curso.turma_id}">Ver detalhes</a>
                <button type="button" class="btn-status-curso" data-turma-id="${curso.turma_id}">Atualizar status</button>
            </div>
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
        exibirNotificacoes(cursos);
        bindStatusButtons();
    }

    function bindStatusButtons() {
        document.querySelectorAll('.btn-status-curso').forEach(function (button) {
            button.addEventListener('click', function () {
                const turmaId = button.dataset.turmaId;
                const card = button.closest('.curso-card');
                const badge = card.querySelector('.curso-status');
                const params = new URLSearchParams({ turma_id: turmaId });

                button.disabled = true;
                button.textContent = 'Atualizando...';

                fetch('update_curso_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: params.toString()
                })
                    .then(function (resposta) {
                        if (!resposta.ok) {
                            throw new Error('Erro ao atualizar o status');
                        }
                        return resposta.json();
                    })
                    .then(function (dados) {
                        if (!dados || !dados.sucesso) {
                            throw new Error(dados && dados.mensagem ? dados.mensagem : 'Erro ao atualizar');
                        }

                        const novoStatusId = Number(dados.novo_status_id);
                        const novoStatusTexto = statusTexto[novoStatusId] || 'Status atualizado';

                        badge.className = `curso-status status-${novoStatusId}`;
                        badge.textContent = novoStatusTexto;
                        mostrarToast(`Status do curso atualizado para "${novoStatusTexto}".`, 'success');
                    })
                    .catch(function () {
                        mostrarToast('Não foi possível atualizar o status do curso.', 'warning');
                    })
                    .finally(function () {
                        button.disabled = false;
                        button.textContent = 'Atualizar status';
                    });
            });
        });
    }

    function montarQueryString() {
        const params = new URLSearchParams();

        if (!form) {
            return params.toString();
        }

        form.querySelectorAll('input[name="area[]"]:checked')
            .forEach(el => params.append('area[]', el.value));

        form.querySelectorAll('input[name="turno[]"]:checked')
            .forEach(el => params.append('turno[]', el.value));

        form.querySelectorAll('input[name="status[]"]:checked')
            .forEach(el => params.append('status[]', el.value));

        if (buscaInput && buscaInput.value.trim() !== '') {
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

    if (form) {
        form.addEventListener('change', function (evento) {
            if (evento.target.matches('input[type="checkbox"]')) {
                carregarCursos();
            }
        });
    }

    if (buscaInput) {
        buscaInput.addEventListener('input', function () {
            clearTimeout(buscaTimeout);
            buscaTimeout = setTimeout(carregarCursos, 350);
        });
    }

    if (limparBtn) {
        limparBtn.addEventListener('click', function () {
            form.querySelectorAll('input[type="checkbox"]').forEach(el => {
                el.checked = false;
            });
            buscaInput.value = '';
            carregarCursos();
        });
    }

    carregarCursos();
});
