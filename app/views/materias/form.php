<?php $estaEditando = $materia !== null; ?>

<div class="page-header">
    <a href="<?= APP_URL ?>/materias" class="back-link"><i class="fa-solid fa-arrow-left"></i> Voltar para matérias</a>
    <h1><?= $estaEditando ? 'Editar matéria' : 'Nova matéria' ?></h1>
    <p><?= $estaEditando ? 'Atualize as informações da sua matéria.' : 'Cadastre uma nova disciplina para organizar seus estudos.' ?></p>
</div>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="dash-card" style="max-width: 640px;">
    <form method="POST" action="<?= $estaEditando ? APP_URL . '/materias/' . $materia['id'] : APP_URL . '/materias' ?>" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Nome da matéria *</label>
            <input type="text" class="form-control" id="name" name="name" required
                   value="<?= htmlspecialchars($materia['name'] ?? '') ?>" placeholder="Ex: JavaScript">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea class="form-control" id="description" name="description" rows="3"
                      placeholder="Do que se trata essa matéria?"><?= htmlspecialchars($materia['description'] ?? '') ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="color" class="form-label">Cor</label>
                <input type="color" class="form-control form-control-color" id="color" name="color"
                       value="<?= htmlspecialchars($materia['color'] ?? '#2563eb') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="goal_description" class="form-label">Objetivo</label>
                <input type="text" class="form-control" id="goal_description" name="goal_description"
                       value="<?= htmlspecialchars($materia['goal_description'] ?? '') ?>" placeholder="Ex: Concluir o módulo avançado">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="start_date" class="form-label">Data de início</label>
                <input type="date" class="form-control" id="start_date" name="start_date"
                       value="<?= htmlspecialchars($materia['start_date'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="end_date" class="form-label">Data prevista para conclusão</label>
                <input type="date" class="form-control" id="end_date" name="end_date"
                       value="<?= htmlspecialchars($materia['end_date'] ?? '') ?>">
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary"><?= $estaEditando ? 'Salvar alterações' : 'Criar matéria' ?></button>
            <a href="<?= APP_URL ?>/materias" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
