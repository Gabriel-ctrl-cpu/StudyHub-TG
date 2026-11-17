<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
    <div>
        <h1>Metas</h1>
        <p>Defina objetivos pessoais e acompanhe seu progresso.</p>
    </div>
    <a href="<?= APP_URL ?>/metas/nova" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Nova meta
    </a>
</div>

<?php if (!empty($sucesso)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>

<div class="row g-3">
    <?php foreach ($metas as $g):
        $percentual = $g['target_value'] > 0 ? min(100, round(($g['current_value'] / $g['target_value']) * 100)) : 0;
        $metaConcluida = (int) $g['is_completed'] === 1;
        $formatarValor = fn($v) => rtrim(rtrim(number_format((float) $v, 1, ',', '.'), '0'), ',');
    ?>
        <div class="col-md-6">
            <div class="goal-card <?= $metaConcluida ? 'is-completed' : '' ?>">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="goal-card-icon"><i class="fa-solid <?= $metaConcluida ? 'fa-circle-check' : 'fa-bullseye' ?>"></i></div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/metas/<?= $g['id'] ?>/editar"><i class="fa-solid fa-pen me-2"></i>Editar</a></li>
                            <li>
                                <form method="POST" action="<?= APP_URL ?>/metas/<?= $g['id'] ?>/excluir" class="form-confirm-delete" data-confirm="Excluir a meta &quot;<?= htmlspecialchars($g['title'], ENT_QUOTES) ?>&quot;?">
                                    <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">
                                    <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-trash me-2"></i>Excluir</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <h5 class="goal-card-title"><?= htmlspecialchars($g['title']) ?></h5>
                <?php if (!empty($g['description'])): ?>
                    <p class="goal-card-desc"><?= htmlspecialchars($g['description']) ?></p>
                <?php endif; ?>

                <div class="goal-card-progress-bar">
                    <i style="width: <?= $percentual ?>%"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="goal-card-values"><?= $formatarValor($g['current_value']) ?> / <?= $formatarValor($g['target_value']) ?> — <?= $percentual ?>%</span>
                    <?php if ($g['deadline']): ?>
                        <span class="goal-card-deadline"><i class="fa-regular fa-calendar"></i> <?= date('d/m/Y', strtotime($g['deadline'])) ?></span>
                    <?php endif; ?>
                </div>

                <?php $ehAutomatica = in_array($g['goal_type'], ['horas', 'tarefas'], true); ?>
                <?php if (!$metaConcluida && $ehAutomatica): ?>
                    <p class="goal-auto-note"><i class="fa-solid fa-arrows-rotate me-1"></i>Progresso atualizado automaticamente com base na sua atividade.</p>
                <?php elseif (!$metaConcluida): ?>
                    <form method="POST" action="<?= APP_URL ?>/metas/<?= $g['id'] ?>/progresso" class="d-flex gap-2 mt-3">
                        <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">
                        <input type="hidden" name="increment" value="1">
                        <button type="submit" class="btn btn-sm btn-outline-primary">+1 progresso</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($metas)): ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="fa-solid fa-bullseye"></i>
                <p>Você ainda não cadastrou nenhuma meta.</p>
                <a href="<?= APP_URL ?>/metas/nova" class="btn btn-primary btn-sm">Cadastrar primeira meta</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="<?= APP_URL ?>/assets/js/confirm-delete.js"></script>
