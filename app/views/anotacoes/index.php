<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
    <div>
        <h1>Anotações</h1>
        <p>Registre seus conhecimentos em um bloco de notas pessoal.</p>
    </div>
    <a href="<?= APP_URL ?>/anotacoes/nova" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Nova anotação
    </a>
</div>

<?php if (!empty($sucesso)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>

<form method="GET" action="<?= APP_URL ?>/anotacoes" class="d-flex flex-wrap gap-2 mb-3">
    <div class="task-search flex-grow-1" style="max-width: 360px;">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" name="q" placeholder="Pesquisar por título, conteúdo ou tag..." value="<?= htmlspecialchars($termo) ?>">
    </div>
    <select name="materia" class="form-select form-select-sm" style="max-width: 200px;" onchange="this.form.submit()">
        <option value="">Todas as matérias</option>
        <?php foreach ($materias as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $idMateria == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-sm btn-outline-secondary">Buscar</button>
    <?php if ($termo || $idMateria): ?>
        <a href="<?= APP_URL ?>/anotacoes" class="btn btn-sm btn-link">Limpar</a>
    <?php endif; ?>
</form>

<div class="row g-3">
    <?php foreach ($anotacoes as $n):
        $resumo = mb_strlen($n['content']) > 140 ? mb_substr($n['content'], 0, 140) . '…' : $n['content'];
        $etiquetas = !empty($n['tags']) ? array_filter(explode(',', $n['tags'])) : [];
    ?>
        <div class="col-md-6 col-xl-4">
            <a href="<?= APP_URL ?>/anotacoes/<?= $n['id'] ?>/editar" class="note-card-link">
                <div class="note-card">
                    <div class="note-card-header">
                        <h6 class="note-card-title"><?= htmlspecialchars($n['title']) ?></h6>
                        <?php if (!empty($n['subject_name'])): ?>
                            <span class="note-card-subject" style="background: <?= htmlspecialchars($n['subject_color']) ?>22; color: <?= htmlspecialchars($n['subject_color']) ?>"><?= htmlspecialchars($n['subject_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="note-card-excerpt"><?= htmlspecialchars($resumo) ?></p>
                    <?php if (!empty($etiquetas)): ?>
                        <div class="note-card-tags">
                            <?php foreach ($etiquetas as $etiqueta): ?>
                                <span>#<?= htmlspecialchars(trim($etiqueta)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="note-card-date"><?= date('d/m/Y', strtotime($n['updated_at'])) ?></div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>

    <?php if (empty($anotacoes)): ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="fa-solid fa-note-sticky"></i>
                <p><?= $termo || $idMateria ? 'Nenhuma anotação encontrada.' : 'Você ainda não tem anotações.' ?></p>
                <?php if (!$termo && !$idMateria): ?>
                    <a href="<?= APP_URL ?>/anotacoes/nova" class="btn btn-primary btn-sm">Criar primeira anotação</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
