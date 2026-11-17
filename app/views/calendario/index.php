<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
    <div>
        <h1>Calendário</h1>
        <p>Visualize tarefas, avaliações, sessões de estudo e compromissos.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal" id="newEventBtn">
        <i class="fa-solid fa-plus me-1"></i> Novo compromisso
    </button>
</div>

<div class="calendar-legend mb-3">
    <span><i class="fa-solid fa-circle" style="color:#2563eb"></i> Tarefas</span>
    <span><i class="fa-solid fa-circle" style="color:#dc2626"></i> Avaliações</span>
    <span><i class="fa-solid fa-circle" style="color:#7c3aed"></i> Sessões de estudo</span>
    <span><i class="fa-solid fa-circle" style="color:#16a34a"></i> Compromissos</span>
</div>

<div class="dash-card">
    <div id="calendar"></div>
</div>

<!-- ===================== MODAL: criar/editar evento ===================== -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="eventForm">
                <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">
                <input type="hidden" name="event_id" id="event_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalTitle">Novo compromisso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título *</label>
                        <input type="text" class="form-control" name="title" id="event_title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="description" id="event_description" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="event_type" id="event_type">
                                <option value="compromisso">Compromisso</option>
                                <option value="lembrete">Lembrete</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Matéria</label>
                            <select class="form-select" name="subject_id" id="event_subject">
                                <option value="">Nenhuma</option>
                                <?php foreach ($materias as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label class="form-label">Data *</label>
                            <input type="date" class="form-control" name="event_date" id="event_date" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Início</label>
                            <input type="time" class="form-control" name="start_time" id="event_start">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Fim</label>
                            <input type="time" class="form-control" name="end_time" id="event_end">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger me-auto d-none" id="deleteEventBtn">Excluir</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/pt-br.global.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/calendario.js"></script>
