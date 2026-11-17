<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
    <div>
        <h1>Minhas matérias</h1>
        <p>Organize seus conteúdos e acompanhe o progresso de cada disciplina.</p>
    </div>
    <a href="<?= APP_URL ?>/materias/nova" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Nova matéria
    </a>
</div>

<?php if (!empty($sucesso)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>

<div class="mb-3">
    <a href="<?= APP_URL ?>/materias" class="btn btn-sm <?= !$mostrarArquivadas ? 'btn-primary' : 'btn-outline-secondary' ?>">Ativas</a>
    <a href="<?= APP_URL ?>/materias?arquivadas=1" class="btn btn-sm <?= $mostrarArquivadas ? 'btn-primary' : 'btn-outline-secondary' ?>">Arquivadas</a>
</div>

<div class="row g-3">
    <?php foreach ($materias as $s):
        $totalTarefas = (int) ($s['total_tasks'] ?? 0);
        $tarefasFeitas  = (int) ($s['done_tasks'] ?? 0);
        $minutos    = (int) ($s['minutes_total'] ?? 0);
        $percentual        = $totalTarefas > 0 ? round(($tarefasFeitas / $totalTarefas) * 100) : 0;
        $horas      = floor($minutos / 60);
        $min       = $minutos % 60;
    ?>
        <div class="col-md-6 col-xl-4">
            <div class="subject-card">
                <div class="subject-card-header">
                    <span class="subject-card-dot" style="background: <?= htmlspecialchars($s['color']) ?>"></span>
                    <h5 class="subject-card-name"><?= htmlspecialchars($s['name']) ?></h5>
                    <div class="dropdown ms-auto">
                        <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/materias/<?= $s['id'] ?>/editar"><i class="fa-solid fa-pen me-2"></i>Editar</a></li>
                            <li>
                                <form method="POST" action="<?= APP_URL ?>/materias/<?= $s['id'] ?>/arquivar">
                                    <input type="hidden" name="csrf_token" value="<?= $this->csrfToken() ?>">
                                    <button type="submit" class="dropdown-item">
                                        <i class="fa-solid fa-box-archive me-2"></i><?= $mostrarArquivadas ? 'Restaurar' : 'Arquivar' ?>
                                    </button>
                                </form>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?= APP_URL ?>/materias/<?= $s['id'] ?>/excluir" class="form-confirm-delete" data-confirm="Excluir a matéria &quot;<?= htmlspecialchars($s['name'], ENT_QUOTES) ?>&quot;? Esta ação não pode ser desfeita.">
                                    <input type="hidden" name="csrf_token" value="<?= $this->csrfToken() ?>">
                                    <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-trash me-2"></i>Excluir</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <?php if (!empty($s['description'])): ?>
                    <p class="subject-card-desc"><?= htmlspecialchars($s['description']) ?></p>
                <?php endif; ?>

                <?php if (isset($s['total_tasks'])): ?>
                    <div class="subject-card-progress">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Progresso</span>
                            <strong><?= $percentual ?>%</strong>
                        </div>
                        <div class="subject-progress-bar-lg">
                            <i style="width: <?= $percentual ?>%; background: <?= htmlspecialchars($s['color']) ?>"></i>
                        </div>
                    </div>

                    <div class="subject-card-stats">
                        <span><i class="fa-solid fa-list-check me-1"></i><?= $totalTarefas ?> tarefas</span>
                        <span><i class="fa-regular fa-clock me-1"></i><?= $horas ?>h<?= $min > 0 ? $min . 'min' : '' ?> estudadas</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($materias)): ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="fa-solid fa-book"></i>
                <p><?= $mostrarArquivadas ? 'Nenhuma matéria arquivada.' : 'Você ainda não cadastrou nenhuma matéria.' ?></p>
                <?php if (!$mostrarArquivadas): ?>
                    <a href="<?= APP_URL ?>/materias/nova" class="btn btn-primary btn-sm">Cadastrar primeira matéria</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="<?= APP_URL ?>/assets/js/confirm-delete.js"></script>
