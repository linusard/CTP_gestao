$(document).ready(function () {
    const form = $('#eventForm');
    const modal = $('#eventModal');
    const modalTitle = $('#eventModalLabel');
    const saveBtn = $('#btnSalvarEvento');
    const deleteBtn = $('#deleteEvent');

    // Restaura o formulário para o estado inicial (novo curso)
    function limparFormulario() {
        form[0].reset();
        $('#turmaId').val('');
        $('#requerCpf').prop('checked', true);
        $('#requerComprovanteResidencia').prop('checked', false);
        $('#requerComprovanteEscolaridade').prop('checked', false);
        $('input[name="dias_semana[]"]').prop('checked', false);
    }

    // Abre o modal já pronto para cadastrar um curso novo
    function abrirModalNovo(dataInicio, dataTermino) {
        limparFormulario();
        modalTitle.text('Adicionar Curso');
        saveBtn.text('Adicionar Curso');
        deleteBtn.hide();

        if (dataInicio) {
            $('#dataInicio').val(dataInicio);
        }
        if (dataTermino) {
            $('#dataTermino').val(dataTermino);
        }

        modal.modal('show');
    }

    // Preenche todos os campos do formulário com os dados de uma turma existente
    function preencherFormulario(dados) {
        $('#turmaId').val(dados.turma_id);

        $('#cursoNome').val(dados.nome);
        $('#areaId').val(dados.area_id);
        $('#cargaHoraria').val(dados.carga_horaria);
        $('#idadeMinima').val(dados.idade_minima);
        $('#escolaridadeMinima').val(dados.escolaridade_minima);
        $('#requerCpf').prop('checked', Number(dados.requer_cpf) === 1);
        $('#requerComprovanteResidencia').prop('checked', Number(dados.requer_comprovante_residencia) === 1);
        $('#requerComprovanteEscolaridade').prop('checked', Number(dados.requer_comprovante_escolaridade) === 1);

        $('#numeroTurma').val(dados.numero_turma);
        $('#origemId').val(dados.origem_id);
        $('#dataInicio').val(dados.data_inicio);
        $('#dataTermino').val(dados.data_termino);
        $('#turnoId').val(dados.turno_id);
        $('#statusId').val(dados.status_id);
        $('#dataLimiteInscricao').val(dados.data_limite_inscricao);
        $('#vagasEstimadas').val(dados.vagas_estimadas);
        $('#localInscricaoId').val(dados.local_inscricao_id);
        $('#localAulaId').val(dados.local_aula_id);
        $('#linkSistemaInscricao').val(dados.link_sistema_inscricao);
        $('#linkPlanilhaMatricula').val(dados.link_planilha_matricula);

        $('input[name="dias_semana[]"]').prop('checked', false);
        (dados.dias_semana_ids || []).forEach(function (id) {
            $('input[name="dias_semana[]"][value="' + id + '"]').prop('checked', true);
        });
    }

    // Busca os dados completos da turma clicada e abre o modal em modo de edição
    function abrirModalEdicao(turmaId) {
        $.ajax({
            url: 'get_turma.php',
            data: { turma_id: turmaId },
            type: 'GET',
            dataType: 'json',
            success: function (dados) {
                if (!dados) {
                    alert('Não foi possível carregar os dados da turma.');
                    return;
                }

                limparFormulario();
                preencherFormulario(dados);
                modalTitle.text('Editar Curso');
                saveBtn.text('Salvar Alterações');
                deleteBtn.show();
                modal.modal('show');
            },
            error: function () {
                alert('Erro ao carregar os dados da turma.');
            }
        });
    }

    // Initialize the FullCalendar
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        events: 'fetch_events.php', // URL para buscar as turmas cadastradas
        selectable: true,
        selectHelper: true,
        select: function (start, end) {
            // Ao selecionar um período vazio no calendário, abre o modal
            // já com as datas de início/término preenchidas
            const dataInicio = moment(start).format('YYYY-MM-DD');
            const dataTermino = moment(end).subtract(1, 'days').format('YYYY-MM-DD');
            abrirModalNovo(dataInicio, dataTermino);
        },
        editable: true,
        eventDrop: function (event) {
            // Arrastar um curso no calendário atualiza apenas as datas da turma
            const dataInicio = moment(event.start).format('YYYY-MM-DD');
            const dataTermino = event.end
                ? moment(event.end).subtract(1, 'days').format('YYYY-MM-DD')
                : dataInicio;

            $.ajax({
                url: 'edit_event.php',
                data: {
                    turma_id: event.id,
                    data_inicio: dataInicio,
                    data_termino: dataTermino,
                    apenas_datas: 1
                },
                type: 'POST',
                dataType: 'json',
                success: function (resposta) {
                    if (!resposta || !resposta.sucesso) {
                        alert('Erro ao atualizar as datas da turma.');
                        $('#calendar').fullCalendar('refetchEvents');
                        return;
                    }
                    alert('Datas da turma atualizadas com sucesso');
                },
                error: function () {
                    alert('Erro ao atualizar as datas da turma.');
                    $('#calendar').fullCalendar('refetchEvents');
                }
            });
        },
        eventClick: function (event) {
            // Ao clicar em um curso já existente, carrega os dados completos para edição
            abrirModalEdicao(event.id);
        }
    });

    // Envio do formulário: cria um curso novo ou atualiza um existente
    form.on('submit', function (e) {
        e.preventDefault();

        const turmaId = $('#turmaId').val();
        const url = turmaId ? 'edit_event.php' : 'add_event.php';

        const formData = new FormData(form[0]);
        // Checkboxes desmarcados não são enviados por padrão; forçamos 0/1 explicitamente
        formData.set('requer_cpf', $('#requerCpf').is(':checked') ? 1 : 0);
        formData.set('requer_comprovante_residencia', $('#requerComprovanteResidencia').is(':checked') ? 1 : 0);
        formData.set('requer_comprovante_escolaridade', $('#requerComprovanteEscolaridade').is(':checked') ? 1 : 0);

        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (resposta) {
                if (resposta && resposta.sucesso) {
                    $('#calendar').fullCalendar('refetchEvents');
                    modal.modal('hide');
                    alert(turmaId ? 'Curso atualizado com sucesso' : 'Curso adicionado com sucesso');
                } else {
                    alert('Erro: ' + (resposta && resposta.mensagem ? resposta.mensagem : 'não foi possível salvar.'));
                }
            },
            error: function () {
                alert('Erro ao salvar o curso.');
            }
        });
    });

    // Exclusão da turma (e do curso, se ele ficar sem nenhuma turma)
    deleteBtn.on('click', function () {
        const turmaId = $('#turmaId').val();

        if (turmaId && confirm('Deseja realmente excluir esta turma?')) {
            $.ajax({
                url: 'delete_event.php',
                data: { turma_id: turmaId },
                type: 'POST',
                dataType: 'json',
                success: function (resposta) {
                    if (resposta && resposta.sucesso) {
                        $('#calendar').fullCalendar('removeEvents', turmaId);
                        modal.modal('hide');
                        alert('Turma excluída com sucesso');
                    } else {
                        alert('Erro ao excluir a turma.');
                    }
                },
                error: function () {
                    alert('Erro ao excluir a turma.');
                }
            });
        }
    });
});
