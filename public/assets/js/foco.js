document.addEventListener('DOMContentLoaded', () => {
    const subjectSelect = document.getElementById('focusSubject');
    const modeButtons = document.querySelectorAll('.focus-mode-btn');
    const timeDisplay = document.getElementById('focusTimeDisplay');
    const phaseLabel = document.getElementById('focusPhaseLabel');
    const ringProgress = document.getElementById('focusRingProgress');
    const startBtn = document.getElementById('focusStartBtn');
    const pauseBtn = document.getElementById('focusPauseBtn');
    const resetBtn = document.getElementById('focusResetBtn');
    const finishBtn = document.getElementById('focusFinishBtn');
    const resultAlert = document.getElementById('focusResultAlert');

    if (!timeDisplay) return;

    const CIRCUMFERENCE = 2 * Math.PI * 100;
    ringProgress.style.strokeDasharray = `${CIRCUMFERENCE}`;

    let focusMinutes = 25;
    let breakMinutes = 5;
    let phase = 'foco'; // 'foco' | 'pausa'
    let totalSeconds = focusMinutes * 60;
    let remainingSeconds = totalSeconds;
    let timerId = null;
    let isRunning = false;
    let sessionStartedAt = null;

    function updateModeSelection(focus, brk) {
        focusMinutes = focus;
        breakMinutes = brk;
        resetTimer();
    }

    modeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            if (isRunning) return; // não trocar o modo com o cronômetro rodando
            modeButtons.forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');
            updateModeSelection(parseInt(btn.dataset.focus, 10), parseInt(btn.dataset.break, 10));
        });
    });

    function formatTime(totalSecs) {
        const m = Math.floor(totalSecs / 60).toString().padStart(2, '0');
        const s = (totalSecs % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    function render() {
        timeDisplay.textContent = formatTime(remainingSeconds);
        phaseLabel.textContent = phase === 'foco' ? 'FOCO' : 'PAUSA';
        const elapsedRatio = 1 - remainingSeconds / totalSeconds;
        ringProgress.style.strokeDashoffset = `${CIRCUMFERENCE * (1 - elapsedRatio)}`;
        ringProgress.classList.toggle('is-break', phase === 'pausa');
    }

    function resetTimer() {
        stopInterval();
        phase = 'foco';
        totalSeconds = focusMinutes * 60;
        remainingSeconds = totalSeconds;
        isRunning = false;
        sessionStartedAt = null;
        startBtn.classList.remove('d-none');
        pauseBtn.classList.add('d-none');
        finishBtn.classList.add('d-none');
        resultAlert.classList.add('d-none');
        render();
    }

    function stopInterval() {
        if (timerId) {
            clearInterval(timerId);
            timerId = null;
        }
    }

    function tick() {
        remainingSeconds--;
        if (remainingSeconds <= 0) {
            handlePhaseComplete();
            return;
        }
        render();
    }

    function handlePhaseComplete() {
        stopInterval();
        isRunning = false;
        playSound();

        if (phase === 'foco') {
            registerSession(focusMinutes, 'Você estudou por ' + focusMinutes + ' minutos. Hora da pausa!');
            phase = 'pausa';
            totalSeconds = breakMinutes * 60;
        } else {
            phase = 'foco';
            totalSeconds = focusMinutes * 60;
            sessionStartedAt = null;
        }
        remainingSeconds = totalSeconds;
        render();

        // Continua automaticamente para a próxima fase
        isRunning = true;
        if (phase === 'foco') sessionStartedAt = new Date().toISOString().slice(0, 19).replace('T', ' ');
        timerId = setInterval(tick, 1000);
        startBtn.classList.add('d-none');
        pauseBtn.classList.remove('d-none');
        finishBtn.classList.toggle('d-none', phase !== 'foco');
    }

    function playSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = ctx.createOscillator();
            const gain = ctx.createGain();
            oscillator.connect(gain);
            gain.connect(ctx.destination);
            oscillator.frequency.value = 880;
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.6);
            oscillator.start();
            oscillator.stop(ctx.currentTime + 0.6);
        } catch (e) {
            // Web Audio API indisponível: segue sem som.
        }
    }

    startBtn.addEventListener('click', () => {
        if (!subjectSelect.value) {
            subjectSelect.classList.add('is-invalid');
            subjectSelect.focus();
            return;
        }
        subjectSelect.classList.remove('is-invalid');
        subjectSelect.disabled = true;
        modeButtons.forEach((b) => (b.disabled = true));

        isRunning = true;
        if (phase === 'foco' && !sessionStartedAt) {
            sessionStartedAt = new Date().toISOString().slice(0, 19).replace('T', ' ');
        }
        timerId = setInterval(tick, 1000);
        startBtn.classList.add('d-none');
        pauseBtn.classList.remove('d-none');
        finishBtn.classList.toggle('d-none', phase !== 'foco');
    });

    pauseBtn.addEventListener('click', () => {
        stopInterval();
        isRunning = false;
        pauseBtn.classList.add('d-none');
        startBtn.classList.remove('d-none');
        startBtn.innerHTML = '<i class="fa-solid fa-play me-1"></i> Continuar';
    });

    resetBtn.addEventListener('click', () => {
        resetTimer();
        subjectSelect.disabled = false;
        modeButtons.forEach((b) => (b.disabled = false));
        startBtn.innerHTML = '<i class="fa-solid fa-play me-1"></i> Iniciar';
    });

    finishBtn.addEventListener('click', () => {
        if (phase !== 'foco') return;
        stopInterval();
        isRunning = false;

        const elapsedMinutes = Math.max(1, Math.round((totalSeconds - remainingSeconds) / 60));
        registerSession(elapsedMinutes, 'Sessão encerrada. Você estudou por ' + elapsedMinutes + ' minutos.');

        resetTimer();
        subjectSelect.disabled = false;
        modeButtons.forEach((b) => (b.disabled = false));
        startBtn.innerHTML = '<i class="fa-solid fa-play me-1"></i> Iniciar';
    });

    async function registerSession(durationMinutes, message) {
        if (!subjectSelect.value || durationMinutes <= 0) return;

        const body = new URLSearchParams({
            csrf_token: window.FOCUS_CONFIG.csrfToken,
            subject_id: subjectSelect.value,
            duration_minutes: durationMinutes,
            started_at: sessionStartedAt || '',
        });

        try {
            const response = await fetch(window.FOCUS_CONFIG.storeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body,
            });
            const result = await response.json();
            if (result.success) {
                resultAlert.textContent = message;
                resultAlert.classList.remove('d-none');
                setTimeout(() => window.location.reload(), 2200);
            }
        } catch (err) {
            // Falha de rede: a sessão não será registrada, mas o cronômetro continua funcionando.
        }
    }

    render();
});
