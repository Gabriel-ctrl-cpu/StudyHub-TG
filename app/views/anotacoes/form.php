<?php $estaEditando = $anotacao !== null; ?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <a href="<?= APP_URL ?>/anotacoes" class="back-link"><i class="fa-solid fa-arrow-left"></i> Voltar para anotações</a>
        <h1><?= $estaEditando ? 'Editar anotação' : 'Nova anotação' ?></h1>
    </div>
    <?php if ($estaEditando): ?>
        <form method="POST" action="<?= APP_URL ?>/anotacoes/<?= $anotacao['id'] ?>/excluir" class="form-confirm-delete" data-confirm="Excluir esta anotação?">
            <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash me-1"></i> Excluir</button>
        </form>
    <?php endif; ?>
</div>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="dash-card note-editor" style="max-width: 720px;">
    <form method="POST" action="<?= $estaEditando ? APP_URL . '/anotacoes/' . $anotacao['id'] : APP_URL . '/anotacoes' ?>" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">

        <div class="mb-3">
            <input type="text" class="form-control note-title-input" name="title" required
                   value="<?= htmlspecialchars($anotacao['title'] ?? '') ?>" placeholder="Título da anotação">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="subject_id" class="form-label">Matéria</label>
                <select class="form-select" id="subject_id" name="subject_id">
                    <option value="">Nenhuma</option>
                    <?php foreach ($materias as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (($anotacao['subject_id'] ?? '') == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="tags" class="form-label">Tags (separadas por vírgula)</label>
                <input type="text" class="form-control" id="tags" name="tags"
                       value="<?= htmlspecialchars($anotacao['tags'] ?? '') ?>" placeholder="Ex: javascript, funcoes">
            </div>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Conteúdo *</label>
            <textarea class="form-control note-content-input" id="content" name="content" rows="12" required
                      placeholder="Escreva sua anotação aqui..."><?= htmlspecialchars($anotacao['content'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><?= $estaEditando ? 'Salvar alterações' : 'Salvar anotação' ?></button>
            <a href="<?= APP_URL ?>/anotacoes" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script src="<?= APP_URL ?>/assets/js/confirm-delete.js"></script>
