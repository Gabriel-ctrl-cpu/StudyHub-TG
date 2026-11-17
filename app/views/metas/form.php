<?php $estaEditando = $meta !== null; ?>

<div class="page-header">
    <a href="<?= APP_URL ?>/metas" class="back-link"><i class="fa-solid fa-arrow-left"></i> Voltar para metas</a>
    <h1><?= $estaEditando ? 'Editar meta' : 'Nova meta' ?></h1>
</div>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="dash-card" style="max-width: 640px;">
    <form method="POST" action="<?= $estaEditando ? APP_URL . '/metas/' . $meta['id'] : APP_URL . '/metas' ?>" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Título *</label>
            <input type="text" class="form-control" id="title" name="title" required
                   value="<?= htmlspecialchars($meta['title'] ?? '') ?>" placeholder="Ex: Estudar 10 horas esta semana">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea class="form-control" id="description" name="description" rows="2"><?= htmlspecialchars($meta['description'] ?? '') ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="goal_type" class="form-label">Tipo</label>
                <select class="form-select" id="goal_type" name="goal_type">
                    <?php $tipoAtual = $meta['goal_type'] ?? 'personalizada'; ?>
                    <option value="horas" <?= $tipoAtual === 'horas' ? 'selected' : '' ?>>Horas de estudo (automática)</option>
                    <option value="tarefas" <?= $tipoAtual === 'tarefas' ? 'selected' : '' ?>>Tarefas concluídas (automática)</option>
                    <option value="exercicios" <?= $tipoAtual === 'exercicios' ? 'selected' : '' ?>>Exercícios resolvidos (manual)</option>
                    <option value="personalizada" <?= $tipoAtual === 'personalizada' ? 'selected' : '' ?>>Personalizada (manual)</option>
                </select>
                <small class="form-text text-muted">Metas de "horas" e "tarefas" atualizam o progresso sozinhas, com base na sua atividade real no sistema.</small>
            </div>
            <div class="col-md-6 mb-3">
                <label for="subject_id" class="form-label">Matéria (opcional)</label>
                <select class="form-select" id="subject_id" name="subject_id">
                    <option value="">Nenhuma</option>
                    <?php foreach ($materias as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (($meta['subject_id'] ?? '') == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="target_value" class="form-label">Valor da meta *</label>
                <input type="number" step="0.5" min="0.5" class="form-control" id="target_value" name="target_value" required
                       value="<?= htmlspecialchars($meta['target_value'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="current_value" class="form-label">Progresso atual</label>
                <input type="number" step="0.5" min="0" class="form-control" id="current_value" name="current_value"
                       value="<?= htmlspecialchars($meta['current_value'] ?? '0') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="deadline" class="form-label">Prazo</label>
                <input type="date" class="form-control" id="deadline" name="deadline" value="<?= htmlspecialchars($meta['deadline'] ?? '') ?>">
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary"><?= $estaEditando ? 'Salvar alterações' : 'Criar meta' ?></button>
            <a href="<?= APP_URL ?>/metas" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
