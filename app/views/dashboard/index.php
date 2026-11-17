<div class="page-header">
    <h1>Olá, <?= htmlspecialchars(explode(' ', $nomeUsuario)[0]) ?>! 👋</h1>
    <p>Pronto para estudar hoje?</p>
</div>

<!-- ===================== CARDS DE RESUMO ===================== -->
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="summary-icon bg-blue-soft text-blue"><i class="fa-solid fa-book"></i></div>
            <div class="summary-value"><?= $totalMaterias ?></div>
            <div class="summary-label">Matérias<br><small>cadastradas</small></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="summary-icon bg-green-soft text-green"><i class="fa-solid fa-check"></i></div>
            <div class="summary-value"><?= $tarefasPendentes ?></div>
            <div class="summary-label">Tarefas pendentes<br><small>de um total de <?= $totalTarefas ?></small></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="summary-icon bg-purple-soft text-purple"><i class="fa-regular fa-clock"></i></div>
            <div class="summary-value"><?= intdiv($minutosHoje, 60) ?>h <?= $minutosHoje % 60 ?>min</div>
            <div class="summary-label">Tempo estudado hoje<br><small>de <?= number_format($metaMinutosDiaria / 60, 0) ?>h meta diária</small></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="summary-icon bg-orange-soft text-orange"><i class="fa-solid fa-bullseye"></i></div>
            <div class="summary-value"><?= $percentualMetas ?>%</div>
            <div class="summary-label">Metas concluídas<br><small>de <?= $totalMetas ?> metas</small></div>
        </div>
    </div>
</div>

<!-- ===================== PROGRESSO + GRÁFICO + CALENDÁRIO ===================== -->
<div class="row g-3 mb-3">
    <div class="col-lg-4">
        <div class="dash-card h-100">
            <h6 class="dash-card-title"><i class="fa-solid fa-chart-pie me-2"></i>Progresso geral</h6>
            <div class="progress-donut-wrap">
                <canvas id="progressDonut"></canvas>
                <div class="progress-donut-center">
                    <strong><?= $percentualMetas ?>%</strong>
                    <span>Concluído</span>
                </div>
            </div>
            <ul class="subject-progress-list">
                <?php foreach ($progressoMaterias as $s):
                    $percentual = $s['total_tasks'] > 0 ? round(($s['done_tasks'] / $s['total_tasks']) * 100) : 0;
                ?>
                    <li>
                        <span class="dot" style="background: <?= htmlspecialchars($s['color']) ?>"></span>
                        <span class="subject-progress-name"><?= htmlspecialchars($s['name']) ?></span>
                        <span class="subject-progress-bar"><i style="width: <?= $percentual ?>%; background: <?= htmlspecialchars($s['color']) ?>"></i></span>
                        <span class="subject-progress-pct"><?= $percentual ?>%</span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($progressoMaterias)): ?>
                    <li class="text-muted small">Nenhuma matéria cadastrada ainda.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="dash-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="dash-card-title mb-0"><i class="fa-solid fa-chart-column me-2"></i>Horas estudadas por dia</h6>
            </div>
            <canvas id="weeklyChart" height="220"></canvas>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="dash-card h-100">
            <h6 class="dash-card-title"><i class="fa-solid fa-calendar-days me-2"></i>Calendário</h6>
            <?php
                $mes = (int) date('n');
                $ano  = (int) date('Y');
                $primeiroDiaSemana = (int) date('w', mktime(0, 0, 0, $mes, 1, $ano));
                $diasNoMes    = (int) date('t', mktime(0, 0, 0, $mes, 1, $ano));
                $hoje = (int) date('j');
                $nomesDosMeses = ['', 'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
            ?>
            <div class="mini-calendar">
                <div class="mini-calendar-title"><?= $nomesDosMeses[$mes] ?> <?= $ano ?></div>
                <div class="mini-calendar-grid mini-calendar-head">
                    <span>D</span><span>S</span><span>T</span><span>Q</span><span>Q</span><span>S</span><span>S</span>
                </div>
                <div class="mini-calendar-grid">
                    <?php for ($i = 0; $i < $primeiroDiaSemana; $i++): ?>
                        <span></span>
                    <?php endfor; ?>
                    <?php for ($d = 1; $d <= $diasNoMes; $d++): ?>
                        <span class="<?= $d === $hoje ? 'is-today' : '' ?>"><?= $d ?></span>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================== LISTAS ===================== -->
<div class="row g-3 mb-3">
    <div class="col-lg-4">
        <div class="dash-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="dash-card-title mb-0"><i class="fa-solid fa-list-check me-2"></i>Próximas tarefas</h6>
                <a href="<?= APP_URL ?>/tarefas" class="dash-card-link">Ver todas <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <ul class="list-unstyled dash-list">
                <?php foreach ($tarefasProximas as $t):
                    $classePrioridade = ['alta' => 'danger', 'media' => 'warning', 'baixa' => 'success'][$t['priority']] ?? 'secondary';
                    $estaAtrasada = !empty($t['due_date']) && $t['due_date'] < date('Y-m-d');
                ?>
                    <li>
                        <input type="checkbox" class="form-check-input me-2" disabled>
                        <div class="dash-list-content">
                            <div class="dash-list-title"><?= htmlspecialchars($t['title']) ?></div>
                            <div class="dash-list-meta"><?= htmlspecialchars($t['subject_name'] ?? 'Sem matéria') ?></div>
                        </div>
                        <?php if ($estaAtrasada): ?>
                            <span class="badge status-badge status-atrasada">Atrasada</span>
                        <?php endif; ?>
                        <span class="badge text-bg-<?= $classePrioridade ?> badge-priority"><?= ucfirst($t['priority']) ?></span>
                        <span class="dash-list-time"><?= $t['due_date'] ? date('d/m', strtotime($t['due_date'])) : '' ?> <?= $t['due_time'] ? date('H:i', strtotime($t['due_time'])) : '' ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($tarefasProximas)): ?>
                    <li class="text-muted small">Nenhuma tarefa pendente. 🎉</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="dash-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="dash-card-title mb-0"><i class="fa-solid fa-file-pen me-2"></i>Próximas avaliações</h6>
                <a href="<?= APP_URL ?>/avaliacoes" class="dash-card-link">Ver todas <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <ul class="list-unstyled dash-list">
                <?php foreach ($avaliacoesProximas as $a): ?>
                    <li>
                        <span class="assessment-dot" style="background: <?= htmlspecialchars($a['subject_color']) ?>"></span>
                        <div class="dash-list-content">
                            <div class="dash-list-title"><?= htmlspecialchars($a['title']) ?></div>
                            <div class="dash-list-meta"><?= htmlspecialchars($a['subject_name']) ?></div>
                        </div>
                        <span class="badge text-bg-danger-soft"><?= (int) $a['days_left'] === 0 ? 'Hoje' : 'Faltam ' . (int) $a['days_left'] . ' dias' ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($avaliacoesProximas)): ?>
                    <li class="text-muted small">Nenhuma avaliação agendada.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="dash-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="dash-card-title mb-0"><i class="fa-solid fa-bullseye me-2"></i>Metas</h6>
                <a href="<?= APP_URL ?>/metas" class="dash-card-link">Ver todas <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <ul class="list-unstyled dash-list-goals">
                <?php foreach ($metas as $g):
                    $percentual = $g['target_value'] > 0 ? min(100, round(($g['current_value'] / $g['target_value']) * 100)) : 0;
                ?>
                    <li>
                        <div class="goal-icon"><i class="fa-solid <?= $g['is_completed'] ? 'fa-circle-check' : 'fa-bullseye' ?>"></i></div>
                        <div class="flex-grow-1">
                            <div class="dash-list-title"><?= htmlspecialchars($g['title']) ?></div>
                            <div class="goal-progress-bar"><i style="width: <?= $percentual ?>%"></i></div>
                            <div class="dash-list-meta"><?= rtrim(rtrim(number_format($g['current_value'], 1, ',', '.'), '0'), ',') ?> / <?= rtrim(rtrim(number_format($g['target_value'], 1, ',', '.'), '0'), ',') ?> — <?= $percentual ?>%</div>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($metas)): ?>
                    <li class="text-muted small">Nenhuma meta cadastrada ainda.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- ===================== SESSÕES + CTA ===================== -->
<div class="row g-3">
    <div class="col-lg-7">
        <div class="dash-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="dash-card-title mb-0"><i class="fa-solid fa-stopwatch me-2"></i>Sessões de estudo</h6>
                <a href="<?= APP_URL ?>/foco" class="dash-card-link">Ver todas <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table sessions-table align-middle mb-0">
                    <thead>
                        <tr><th>Data</th><th>Matéria</th><th>Duração</th><th>Tipo</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessoesRecentes as $s): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($s['session_date'])) ?></td>
                                <td><?= htmlspecialchars($s['subject_name'] ?? '—') ?></td>
                                <td><?= $s['duration_minutes'] ?> min</td>
                                <td><span class="badge session-type-badge"><i class="fa-solid fa-circle"></i> <?= ucfirst($s['session_type']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($sessoesRecentes)): ?>
                            <tr><td colspan="4" class="text-muted small">Nenhuma sessão registrada ainda.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="cta-banner h-100">
            <div>
                <h5>Você consegue!</h5>
                <p>Disciplina hoje, resultados amanhã.</p>
                <a href="<?= APP_URL ?>/foco" class="btn btn-light btn-sm">Ir para o modo foco <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<script>
const dashboardData = {
    subjects: <?= json_encode(array_map(fn($s) => ['name' => $s['name'], 'color' => $s['color'], 'pct' => $s['total_tasks'] > 0 ? round(($s['done_tasks'] / $s['total_tasks']) * 100) : 0], $progressoMaterias)) ?>,
    weekly: <?= json_encode($minutosSemanais) ?>
};
</script>
<script src="<?= APP_URL ?>/assets/js/dashboard.js"></script>
