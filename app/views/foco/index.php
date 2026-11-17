<div class="page-header">
    <h1>Modo Foco</h1>
    <p>Estude sem distrações com o método Pomodoro.</p>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="focus-card">
            <div class="focus-subject-select">
                <label for="focusSubject" class="form-label">Matéria</label>
                <select id="focusSubject" class="form-select">
                    <option value="">Selecione uma matéria...</option>
                    <?php foreach ($materias as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="focus-mode-toggle">
                <button type="button" class="focus-mode-btn active" data-focus="25" data-break="5">25 min foco / 5 min pausa</button>
                <button type="button" class="focus-mode-btn" data-focus="50" data-break="10">50 min foco / 10 min pausa</button>
            </div>

            <div class="focus-timer-wrap">
                <svg class="focus-ring" viewBox="0 0 220 220">
                    <circle cx="110" cy="110" r="100" class="focus-ring-bg"></circle>
                    <circle cx="110" cy="110" r="100" class="focus-ring-progress" id="focusRingProgress"></circle>
                </svg>
                <div class="focus-timer-center">
                    <span id="focusPhaseLabel">FOCO</span>
                    <strong id="focusTimeDisplay">25:00</strong>
                </div>
            </div>

            <div class="focus-controls">
                <button type="button" class="btn btn-primary btn-focus" id="focusStartBtn"><i class="fa-solid fa-play me-1"></i> Iniciar</button>
                <button type="button" class="btn btn-outline-secondary btn-focus d-none" id="focusPauseBtn"><i class="fa-solid fa-pause me-1"></i> Pausar</button>
                <button type="button" class="btn btn-outline-secondary btn-focus" id="focusResetBtn"><i class="fa-solid fa-rotate-left me-1"></i> Reiniciar</button>
                <button type="button" class="btn btn-outline-danger btn-focus d-none" id="focusFinishBtn"><i class="fa-solid fa-flag-checkered me-1"></i> Finalizar</button>
            </div>

            <div class="alert alert-success mt-3 d-none" id="focusResultAlert"></div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="dash-card h-100">
            <h6 class="dash-card-title mb-3"><i class="fa-solid fa-clock-rotate-left me-2"></i>Sessões recentes</h6>
            <ul class="list-unstyled dash-list" id="recentSessionsList">
                <?php foreach ($sessoesRecentes as $s): ?>
                    <li>
                        <span class="dot" style="background: <?= htmlspecialchars($s['subject_color'] ?? '#94a3b8') ?>; width:8px; height:8px; border-radius:50%; display:inline-block;"></span>
                        <div class="dash-list-content">
                            <div class="dash-list-title"><?= htmlspecialchars($s['subject_name'] ?? 'Sem matéria') ?></div>
                            <div class="dash-list-meta"><?= date('d/m/Y', strtotime($s['session_date'])) ?></div>
                        </div>
                        <span class="dash-list-time"><?= $s['duration_minutes'] ?> min</span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($sessoesRecentes)): ?>
                    <li class="text-muted small">Nenhuma sessão registrada ainda.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<script>
window.FOCUS_CONFIG = {
    csrfToken: <?= json_encode($tokenCsrf) ?>,
    storeUrl: <?= json_encode(APP_URL . '/foco/sessao') ?>,
};
</script>
<script src="<?= APP_URL ?>/assets/js/foco.js"></script>
