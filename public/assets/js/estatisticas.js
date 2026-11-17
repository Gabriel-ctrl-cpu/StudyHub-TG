document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.STATS_DATA === 'undefined') return;

    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(30,41,59,0.06)';
    const textColor = isDark ? '#9aa3b8' : '#6b7280';
    const dateFmt = new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: '2-digit' });

    function lastNDates(n) {
        const dates = [];
        const today = new Date();
        for (let i = n - 1; i >= 0; i--) {
            const d = new Date(today);
            d.setDate(today.getDate() - i);
            dates.push(d);
        }
        return dates;
    }

    const dailyEl = document.getElementById('chartDaily');
    if (dailyEl) {
        const dates = lastNDates(14);
        const labels = dates.map((d) => dateFmt.format(d));
        const values = dates.map((d) => {
            const iso = d.toISOString().slice(0, 10);
            const found = window.STATS_DATA.dailyMinutes.find((m) => m.session_date === iso);
            return found ? Math.round((parseInt(found.total_minutes, 10) / 60) * 100) / 100 : 0;
        });

        new Chart(dailyEl, {
            type: 'bar',
            data: { labels, datasets: [{ label: 'Horas', data: values, backgroundColor: '#2563eb', borderRadius: 5, maxBarThickness: 22 }] },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor, maxRotation: 0, autoSkip: true } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true },
                },
            },
        });
    }

    const subjectsEl = document.getElementById('chartSubjects');
    if (subjectsEl) {
        const data = window.STATS_DATA.subjectsTime;
        if (data.length) {
            new Chart(subjectsEl, {
                type: 'pie',
                data: {
                    labels: data.map((s) => s.name),
                    datasets: [{ data: data.map((s) => Math.round((s.minutes / 60) * 10) / 10), backgroundColor: data.map((s) => s.color), borderWidth: 0 }],
                },
                options: { plugins: { legend: { position: 'bottom', labels: { color: textColor, boxWidth: 12 } } } },
            });
        } else {
            subjectsEl.parentElement.insertAdjacentHTML('beforeend', '<p class="text-muted small text-center mt-4">Nenhuma sessão de estudo registrada ainda.</p>');
        }
    }

    const tasksEl = document.getElementById('chartTasks');
    if (tasksEl) {
        const dates = lastNDates(14);
        const labels = dates.map((d) => dateFmt.format(d));
        const values = dates.map((d) => {
            const iso = d.toISOString().slice(0, 10);
            const found = window.STATS_DATA.completedByDay.find((c) => c.completed_date === iso);
            return found ? parseInt(found.total, 10) : 0;
        });

        new Chart(tasksEl, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Tarefas concluídas', data: values,
                    borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,0.12)',
                    fill: true, tension: 0.3, pointRadius: 3,
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor, maxRotation: 0, autoSkip: true } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor, precision: 0 }, beginAtZero: true },
                },
            },
        });
    }

    const weeklyEl = document.getElementById('chartWeekly');
    if (weeklyEl) {
        const weeks = window.STATS_DATA.weeklyEvolution;
        const labels = weeks.map((w) => 'Sem. ' + dateFmt.format(new Date(w.week_start + 'T00:00:00')));
        const values = weeks.map((w) => Math.round((parseInt(w.total_minutes, 10) / 60) * 100) / 100);

        new Chart(weeklyEl, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Horas estudadas', data: values,
                    borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,0.12)',
                    fill: true, tension: 0.3, pointRadius: 3,
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true },
                },
            },
        });
    }
});
