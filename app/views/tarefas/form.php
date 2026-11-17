<?php $estaEditando = $tarefa !== null; ?>

<div class="page-header">
    <a href="<?= APP_URL ?>/tarefas" class="back-link"><i class="fa-solid fa-arrow-left"></i> Voltar para tarefas</a>
    <h1><?= $estaEditando ? 'Editar tarefa' : 'Nova tarefa' ?></h1>
</div>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="dash-card" style="max-width: 640px;">
    <form method="POST" action="<?= $estaEditando ? APP_URL . '/tarefas/' . $tarefa['id'] : APP_URL . '/tarefas' ?>" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Título *</label>
            <input type="text" class="form-control" id="title" name="title" required
                   value="<?= htmlspecialchars($tarefa['title'] ?? '') ?>" placeholder="Ex: Estudar funções em JavaScript">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($tarefa['description'] ?? '') ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="subject_id" class="form-label">Matéria</label>
                <select class="form-select" id="subject_id" name="subject_id">
                    <option value="">Sem matéria</option>
                    <?php foreach ($materias as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (($tarefa['subject_id'] ?? '') == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="priority" class="form-label">Prioridade</label>
                <select class="form-select" id="priority" name="priority">
                    <?php $prioridadeAtual = $tarefa['priority'] ?? 'media'; ?>
                    <option value="alta" <?= $prioridadeAtual === 'alta' ? 'selected' : '' ?>>🔴 Alta</option>
                    <option value="media" <?= $prioridadeAtual === 'media' ? 'selected' : '' ?>>🟡 Média</option>
                    <option value="baixa" <?= $prioridadeAtual === 'baixa' ? 'selected' : '' ?>>🟢 Baixa</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="due_date" class="form-label">Prazo</label>
                <input type="date" class="form-control" id="due_date" name="due_date" value="<?= htmlspecialchars($tarefa['due_date'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="due_time" class="form-label">Horário</label>
                <input type="time" class="form-control" id="due_time" name="due_time" value="<?= htmlspecialchars($tarefa['due_time'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <?php $situacaoAtual = $tarefa['status'] ?? 'pendente'; ?>
                    <option value="pendente" <?= $situacaoAtual === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="em_andamento" <?= $situacaoAtual === 'em_andamento' ? 'selected' : '' ?>>Em andamento</option>
                    <option value="concluida" <?= $situacaoAtual === 'concluida' ? 'selected' : '' ?>>Concluída</option>
                    <option value="cancelada" <?= $situacaoAtual === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary"><?= $estaEditando ? 'Salvar alterações' : 'Criar tarefa' ?></button>
            <a href="<?= APP_URL ?>/tarefas" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
