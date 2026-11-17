<?php $estaEditando = $avaliacao !== null; ?>

<div class="page-header">
    <a href="<?= APP_URL ?>/avaliacoes" class="back-link"><i class="fa-solid fa-arrow-left"></i> Voltar para avaliações</a>
    <h1><?= $estaEditando ? 'Editar avaliação' : 'Nova avaliação' ?></h1>
</div>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="dash-card" style="max-width: 640px;">
    <form method="POST" action="<?= $estaEditando ? APP_URL . '/avaliacoes/' . $avaliacao['id'] : APP_URL . '/avaliacoes' ?>" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Título *</label>
            <input type="text" class="form-control" id="title" name="title" required
                   value="<?= htmlspecialchars($avaliacao['title'] ?? '') ?>" placeholder="Ex: Prova de Banco de Dados">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="subject_id" class="form-label">Matéria *</label>
                <select class="form-select" id="subject_id" name="subject_id" required>
                    <?php foreach ($materias as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (($avaliacao['subject_id'] ?? '') == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="assessment_type" class="form-label">Tipo</label>
                <select class="form-select" id="assessment_type" name="assessment_type">
                    <?php $tipoAtual = $avaliacao['assessment_type'] ?? 'prova'; ?>
                    <option value="prova" <?= $tipoAtual === 'prova' ? 'selected' : '' ?>>Prova</option>
                    <option value="trabalho" <?= $tipoAtual === 'trabalho' ? 'selected' : '' ?>>Trabalho</option>
                    <option value="seminario" <?= $tipoAtual === 'seminario' ? 'selected' : '' ?>>Seminário</option>
                    <option value="projeto" <?= $tipoAtual === 'projeto' ? 'selected' : '' ?>>Projeto</option>
                    <option value="outro" <?= $tipoAtual === 'outro' ? 'selected' : '' ?>>Outro</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="assessment_date" class="form-label">Data *</label>
                <input type="date" class="form-control" id="assessment_date" name="assessment_date" required
                       value="<?= htmlspecialchars($avaliacao['assessment_date'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="assessment_time" class="form-label">Horário</label>
                <input type="time" class="form-control" id="assessment_time" name="assessment_time"
                       value="<?= htmlspecialchars($avaliacao['assessment_time'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <?php $situacaoAtual = $avaliacao['status'] ?? 'agendado'; ?>
                    <option value="agendado" <?= $situacaoAtual === 'agendado' ? 'selected' : '' ?>>Agendado</option>
                    <option value="realizado" <?= $situacaoAtual === 'realizado' ? 'selected' : '' ?>>Realizado</option>
                    <option value="corrigido" <?= $situacaoAtual === 'corrigido' ? 'selected' : '' ?>>Corrigido</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea class="form-control" id="description" name="description" rows="2"><?= htmlspecialchars($avaliacao['description'] ?? '') ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="weight" class="form-label">Peso</label>
                <input type="number" step="0.1" min="0" class="form-control" id="weight" name="weight"
                       value="<?= htmlspecialchars($avaliacao['weight'] ?? '') ?>" placeholder="Ex: 10">
            </div>
            <div class="col-md-6 mb-3">
                <label for="grade" class="form-label">Nota</label>
                <input type="number" step="0.1" min="0" max="10" class="form-control" id="grade" name="grade"
                       value="<?= htmlspecialchars($avaliacao['grade'] ?? '') ?>" placeholder="Preencha após a correção">
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary"><?= $estaEditando ? 'Salvar alterações' : 'Criar avaliação' ?></button>
            <a href="<?= APP_URL ?>/avaliacoes" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
