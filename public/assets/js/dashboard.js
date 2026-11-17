/**
 * assets/js/dashboard.js
 * Renderiza os gráficos da dashboard (rosca de progresso por matéria e
 * barras de horas estudadas por dia) usando Chart.js e os dados
 * injetados pela view em `dashboardData`.
 */
document.addEventListener('DOMContentLoaded', () => {
    if (typeof dashboardData === 'undefined') return;

    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(30,41,59,0.06)';
    const textColor = isDark ? '#9aa3b8' : '#6b7280';

    // ---------------- Rosca de progresso por matéria ----------------
    const donutEl = document.getElementById('progressDonut');
    if (donutEl && dashboardData.subjects.length) {
        new Chart(donutEl, {
            type: 'doughnut',
            data: {
                labels: dashboardData.subjects.map((s) => s.name),
                datasets: [{
                    data: dashboardData.subjects.map((s) => s.pct),
                    backgroundColor: dashboardData.subjects.map((s) => s.color),
                    borderWidth: 0,
                }],
            },
            options: {
                cutout: '72%',
                plugins: { legend: { display: false } },
            },
        });
    }

    // ---------------- Barras: horas estudadas por dia ----------------
    const weeklyEl = document.getElementById('weeklyChart');
    if (weeklyEl) {
        const days = [];
        const today = new Date();
        const totals = [];
        const weekdayFmt = new Intl.DateTimeFormat('pt-BR', { weekday: 'short' });

        for (let i = 6; i >= 0; i--) {
            const d = new Date(today);
            d.setDate(today.getDate() - i);
            const iso = d.toISOString().slice(0, 10);
            days.push(weekdayFmt.format(d).replace('.', ''));

            const found = dashboardData.weekly.find((w) => w.session_date === iso);
            totals.push(found ? Math.round((parseInt(found.total_minutes, 10) / 60) * 100) / 100 : 0);
        }

        new Chart(weeklyEl, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [{
                    label: 'Horas estudadas',
                    data: totals,
                    backgroundColor: '#2563eb',
                    borderRadius: 6,
                    maxBarThickness: 32,
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor, stepSize: 2 }, beginAtZero: true },
                },
            },
        });
    }
});
