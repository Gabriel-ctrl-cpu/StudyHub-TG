<div class="page-header">
    <h1>Estatísticas</h1>
    <p>Sua análise pessoal de estudos — esses dados são só seus.</p>
</div>

<!-- ===================== HORAS ESTUDADAS ===================== -->
<h6 class="stats-section-title">Horas estudadas</h6>
<div class="row g-3 mb-4">
    <?php
    $cardsHoras = [
        ['label' => 'Hoje', 'minutes' => $minutosHoje, 'icon' => 'fa-calendar-day'],
        ['label' => 'Esta semana', 'minutes' => $minutosSemana, 'icon' => 'fa-calendar-week'],
        ['label' => 'Este mês', 'minutes' => $minutosMes, 'icon' => 'fa-calendar'],
        ['label' => 'Total', 'minutes' => $minutosTotal, 'icon' => 'fa-infinity'],
    ];
    foreach ($cardsHoras as $c): ?>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon bg-blue-soft text-blue"><i class="fa-solid <?= $c['icon'] ?>"></i></div>
                <div class="summary-value"><?= intdiv($c['minutes'], 60) ?>h <?= $c['minutes'] % 60 ?>min</div>
                <div class="summary-label"><?= $c['label'] ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ===================== TAREFAS ===================== -->
<h6 class="stats-section-title">Tarefas</h6>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="summary-icon bg-purple-soft text-purple"><i class="fa-solid fa-list"></i></div>
            <div class="summary-value"><?= $tarefasCriadas ?></div>
            <div class="summary-label">Criadas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="summary-icon bg-green-soft text-green"><i class="fa-solid fa-check"></i></div>
            <div class="summary-value"><?= $tarefasConcluidas ?></div>
            <div class="summary-label">Concluídas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="summary-icon bg-orange-soft text-orange"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="summary-value"><?= $tarefasPendentes ?></div>
            <div class="summary-label">Pendentes</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="summary-icon bg-blue-soft text-blue"><i class="fa-solid fa-percent"></i></div>
            <div class="summary-value"><?= $taxaConclusao ?>%</div>
            <div class="summary-label">Taxa de conclusão</div>
        </div>
    </div>
</div>

<!-- ===================== GRÁFICOS ===================== -->
<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="dash-card h-100">
            <h6 class="dash-card-title mb-3"><i class="fa-solid fa-chart-column me-2"></i>Horas estudadas por dia (últimos 14 dias)</h6>
            <canvas id="chartDaily" height="220"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="dash-card h-100">
            <h6 class="dash-card-title mb-3"><i class="fa-solid fa-chart-pie me-2"></i>Tempo por matéria</h6>
            <canvas id="chartSubjects" height="220"></canvas>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="dash-card h-100">
            <h6 class="dash-card-title mb-3"><i class="fa-solid fa-list-check me-2"></i>Tarefas concluídas por dia</h6>
            <canvas id="chartTasks" height="220"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="dash-card h-100">
            <h6 class="dash-card-title mb-3"><i class="fa-solid fa-arrow-trend-up me-2"></i>Evolução semanal (horas)</h6>
            <canvas id="chartWeekly" height="220"></canvas>
        </div>
    </div>
</div>

<script>
window.STATS_DATA = {
    dailyMinutes: <?= json_encode($minutosDiarios) ?>,
    subjectsTime: <?= json_encode(array_map(fn($s) => ['name' => $s['name'], 'color' => $s['color'], 'minutes' => (int) $s['minutes_total']], array_filter($tempoPorMateria, fn($s) => (int) $s['minutes_total'] > 0))) ?>,
    completedByDay: <?= json_encode($concluidasPorDia) ?>,
    weeklyEvolution: <?= json_encode($evolucaoSemanal) ?>,
};
</script>
<script src="<?= APP_URL ?>/assets/js/estatisticas.js"></script>
