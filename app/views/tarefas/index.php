<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
    <div>
        <h1>Tarefas</h1>
        <p>Controle tudo que precisa ser feito.</p>
    </div>
    <a href="<?= APP_URL ?>/tarefas/nova" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Nova tarefa
    </a>
</div>

<?php if (!empty($sucesso)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>

<!-- ===================== FILTROS ===================== -->
<form method="GET" action="<?= APP_URL ?>/tarefas" class="task-filters mb-3">
    <div class="task-filter-chips">
        <?php
        $periodos = [
            'todas' => 'Todas', 'hoje' => 'Hoje', 'semana' => 'Semana',
        ];
        foreach ($periodos as $valor => $rotulo):
            $ativo = $filtros['period'] === $valor;
        ?>
            <a class="filter-chip <?= $ativo ? 'active' : '' ?>"
               href="<?= APP_URL ?>/tarefas?periodo=<?= $valor ?>&status=<?= urlencode($filtros['status']) ?>&prioridade=<?= urlencode($filtros['priority']) ?>"><?= $rotulo ?></a>
        <?php endforeach; ?>
        <a class="filter-chip <?= $filtros['status'] === 'pendente' ? 'active' : '' ?>"
           href="<?= APP_URL ?>/tarefas?status=pendente">Pendentes</a>
        <a class="filter-chip <?= $filtros['status'] === 'concluida' ? 'active' : '' ?>"
           href="<?= APP_URL ?>/tarefas?status=concluida">Concluídas</a>
        <a class="filter-chip <?= $filtros['priority'] === 'alta' ? 'active' : '' ?>"
           href="<?= APP_URL ?>/tarefas?prioridade=alta">Alta prioridade</a>
    </div>

    <div class="task-filter-row">
        <select name="materia" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">Todas as matérias</option>
            <?php foreach ($materias as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $filtros['subject_id'] == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="periodo" value="<?= htmlspecialchars($filtros['period']) ?>">
        <input type="hidden" name="status" value="<?= htmlspecialchars($filtros['status']) ?>">
        <input type="hidden" name="prioridade" value="<?= htmlspecialchars($filtros['priority']) ?>">
        <div class="task-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" name="q" placeholder="Pesquisar tarefas..." value="<?= htmlspecialchars($filtros['search']) ?>">
        </div>
        <button type="submit" class="btn btn-sm btn-outline-secondary">Filtrar</button>
        <a href="<?= APP_URL ?>/tarefas" class="btn btn-sm btn-link">Limpar</a>
    </div>
</form>

<!-- ===================== LISTA ===================== -->
<div class="dash-card p-0">
    <ul class="list-unstyled task-list mb-0">
        <?php foreach ($tarefas as $t):
            $classePrioridade = ['alta' => 'danger', 'media' => 'warning', 'baixa' => 'success'][$t['priority']] ?? 'secondary';
            $infoSituacao = Task::computeStatus($t);
            $tarefaConcluida = $t['status'] === 'concluida';
        ?>
            <li class="task-item <?= $tarefaConcluida ? 'is-done' : '' ?>">
                <form method="POST" action="<?= APP_URL ?>/tarefas/<?= $t['id'] ?>/concluir" class="task-check-form">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrfToken() ?>">
                    <button type="submit" class="task-checkbox" aria-label="Marcar como concluída">
                        <i class="fa-solid <?= $tarefaConcluida ? 'fa-circle-check' : 'fa-circle' ?>"></i>
                    </button>
                </form>

                <div class="task-item-content">
                    <div class="task-item-title"><?= htmlspecialchars($t['title']) ?></div>
                    <div class="task-item-meta">
                        <?php if (!empty($t['subject_name'])): ?>
                            <span><span class="dot" style="background: <?= htmlspecialchars($t['subject_color']) ?>"></span> <?= htmlspecialchars($t['subject_name']) ?></span>
                        <?php endif; ?>
                        <?php if ($t['due_date']): ?>
                            <span><i class="fa-regular fa-calendar"></i> <?= date('d/m/Y', strtotime($t['due_date'])) ?><?= $t['due_time'] ? ' às ' . date('H:i', strtotime($t['due_time'])) : '' ?></span>
                        <?php endif; ?>
                        <span class="badge status-badge status-<?= $infoSituacao['css'] ?>"><?= $infoSituacao['label'] ?></span>
                    </div>
                </div>

                <span class="badge text-bg-<?= $classePrioridade ?> task-priority-badge"><?= ucfirst($t['priority']) ?></span>

                <div class="dropdown">
                    <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= APP_URL ?>/tarefas/<?= $t['id'] ?>/editar"><i class="fa-solid fa-pen me-2"></i>Editar</a></li>
                        <li>
                            <form method="POST" action="<?= APP_URL ?>/tarefas/<?= $t['id'] ?>/excluir" class="form-confirm-delete" data-confirm="Excluir a tarefa &quot;<?= htmlspecialchars($t['title'], ENT_QUOTES) ?>&quot;?">
                                <input type="hidden" name="csrf_token" value="<?= $this->csrfToken() ?>">
                                <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-trash me-2"></i>Excluir</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </li>
        <?php endforeach; ?>

        <?php if (empty($tarefas)): ?>
            <li class="empty-state border-0">
                <i class="fa-solid fa-list-check"></i>
                <p>Nenhuma tarefa encontrada com esses filtros.</p>
            </li>
        <?php endif; ?>
    </ul>
</div>

<script src="<?= APP_URL ?>/assets/js/confirm-delete.js"></script>
