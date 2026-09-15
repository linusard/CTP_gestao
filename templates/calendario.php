
        <div class="container mt-5">
            <h1>Calendário de Cursos</h1>
        <div id="calendar"></div>
        </div>

        <!-- Modal para adicionar/editar Curso + Turma -->
        <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Adicionar Curso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <input type="hidden" id="turmaId" name="turma_id">

                        <fieldset class="form-secao">
                            <legend>Dados do Curso</legend>

                            <div class="form-group">
                                <label for="cursoNome">Nome do Curso</label>
                                <input type="text" class="form-control" id="cursoNome" name="nome" required>
                            </div>

                            <div class="form-row-dupla">
                                <div class="form-group">
                                    <label for="areaId">Área</label>
                                    <select class="form-control" id="areaId" name="area_id" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($areas as $area): ?>
                                            <option value="<?php echo (int) $area['id']; ?>"><?php echo htmlspecialchars($area['nome']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="cargaHoraria">Carga Horária (h)</label>
                                    <input type="number" min="1" class="form-control" id="cargaHoraria" name="carga_horaria" required>
                                </div>
                            </div>

                            <div class="form-row-dupla">
                                <div class="form-group">
                                    <label for="idadeMinima">Idade Mínima</label>
                                    <input type="number" min="0" class="form-control" id="idadeMinima" name="idade_minima" value="16">
                                </div>

                                <div class="form-group">
                                    <label for="escolaridadeMinima">Escolaridade Mínima</label>
                                    <input type="text" class="form-control" id="escolaridadeMinima" name="escolaridade_minima" value="Não pedido">
                                </div>
                            </div>

                            <div class="form-check-linha">
                                <label class="filtro-check">
                                    <input type="checkbox" id="requerCpf" name="requer_cpf" checked> Exige CPF
                                </label>
                                <label class="filtro-check">
                                    <input type="checkbox" id="requerComprovanteResidencia" name="requer_comprovante_residencia"> Exige comprovante de residência
                                </label>
                                <label class="filtro-check">
                                    <input type="checkbox" id="requerComprovanteEscolaridade" name="requer_comprovante_escolaridade"> Exige comprovante de escolaridade
                                </label>
                            </div>
                        </fieldset>

                        <fieldset class="form-secao">
                            <legend>Dados da Turma</legend>

                            <div class="form-row-dupla">
                                <div class="form-group">
                                    <label for="numeroTurma">Número da Turma</label>
                                    <input type="text" class="form-control" id="numeroTurma" name="numero_turma" placeholder="Ex: TURMA 01">
                                </div>

                                <div class="form-group">
                                    <label for="origemId">Origem</label>
                                    <select class="form-control" id="origemId" name="origem_id" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($origens as $origem): ?>
                                            <option value="<?php echo (int) $origem['id']; ?>"><?php echo htmlspecialchars($origem['nome']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row-dupla">
                                <div class="form-group">
                                    <label for="dataInicio">Data de Início</label>
                                    <input type="date" class="form-control" id="dataInicio" name="data_inicio" required>
                                </div>

                                <div class="form-group">
                                    <label for="dataTermino">Data de Término</label>
                                    <input type="date" class="form-control" id="dataTermino" name="data_termino" required>
                                </div>
                            </div>

                            <div class="form-row-dupla">
                                <div class="form-group">
                                    <label for="turnoId">Turno</label>
                                    <select class="form-control" id="turnoId" name="turno_id" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($turnos as $turno): ?>
                                            <option value="<?php echo (int) $turno['id']; ?>"><?php echo htmlspecialchars($turno['descricao']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="statusId">Status</label>
                                    <select class="form-control" id="statusId" name="status_id">
                                        <?php foreach ($statusList as $status): ?>
                                            <option value="<?php echo (int) $status['id']; ?>"><?php echo htmlspecialchars($status['descricao']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Dias da Semana</label>
                                <div class="dias-semana-grid">
                                    <?php foreach ($diasSemana as $dia): ?>
                                        <label class="filtro-check">
                                            <input type="checkbox" name="dias_semana[]" value="<?php echo (int) $dia['id']; ?>">
                                            <?php echo htmlspecialchars($dia['nome']); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="form-row-dupla">
                                <div class="form-group">
                                    <label for="dataLimiteInscricao">Prazo de Inscrição</label>
                                    <input type="date" class="form-control" id="dataLimiteInscricao" name="data_limite_inscricao">
                                </div>

                                <div class="form-group">
                                    <label for="vagasEstimadas">Vagas Estimadas</label>
                                    <input type="number" min="0" class="form-control" id="vagasEstimadas" name="vagas_estimadas">
                                </div>
                            </div>

                            <div class="form-row-dupla">
                                <div class="form-group">
                                    <label for="localInscricaoId">Local de Inscrição</label>
                                    <select class="form-control" id="localInscricaoId" name="local_inscricao_id">
                                        <option value="">Não definido</option>
                                        <?php foreach ($locais as $local): ?>
                                            <option value="<?php echo (int) $local['id']; ?>"><?php echo htmlspecialchars($local['nome']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="localAulaId">Local da Aula</label>
                                    <select class="form-control" id="localAulaId" name="local_aula_id">
                                        <option value="">Não definido</option>
                                        <?php foreach ($locais as $local): ?>
                                            <option value="<?php echo (int) $local['id']; ?>"><?php echo htmlspecialchars($local['nome']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="linkSistemaInscricao">Link do Sistema de Inscrição</label>
                                <input type="url" class="form-control" id="linkSistemaInscricao" name="link_sistema_inscricao" placeholder="https://...">
                            </div>

                            <div class="form-group">
                                <label for="linkPlanilhaMatricula">Link da Planilha de Matrícula</label>
                                <input type="url" class="form-control" id="linkPlanilhaMatricula" name="link_planilha_matricula" placeholder="https://...">
                            </div>
                        </fieldset>

                        <div class="form-acoes">
                            <button type="submit" class="btn btn-primary" id="btnSalvarEvento">Adicionar Curso</button>
                            <button type="button" class="btn btn-danger" id="deleteEvent" style="display:none;">Excluir Turma</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
