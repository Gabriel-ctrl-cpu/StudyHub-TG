/**
 * assets/js/calendario.js
 * Inicializa o FullCalendar consumindo o feed JSON do back-end
 * (tarefas, avaliações, sessões e eventos) e gerencia o modal de
 * criação/edição/exclusão de compromissos via Fetch API.
 */
document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    const eventModalEl = document.getElementById('eventModal');
    const eventModal = new bootstrap.Modal(eventModalEl);
    const eventForm = document.getElementById('eventForm');
    const deleteBtn = document.getElementById('deleteEventBtn');
    const modalTitle = document.getElementById('eventModalTitle');
    const csrfToken = eventForm.querySelector('[name="csrf_token"]').value;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'pt-br',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek',
        },
        buttonText: { today: 'Hoje', month: 'Mês', week: 'Semana', list: 'Lista' },
        events: window.APP_URL + '/calendario/eventos',
        dateClick(info) {
            resetForm();
            document.getElementById('event_date').value = info.dateStr;
            modalTitle.textContent = 'Novo compromisso';
            deleteBtn.classList.add('d-none');
            eventModal.show();
        },
        eventClick(info) {
            const props = info.event.extendedProps;
            if (props.type !== 'evento') {
                return; // Somente eventos/compromissos criados pelo usuário são editáveis aqui.
            }
            resetForm();
            modalTitle.textContent = 'Editar compromisso';
            document.getElementById('event_id').value = props.editable_id;
            document.getElementById('event_title').value = info.event.title.replace(/^(📅|🔔|🗒️)\s*/, '');
            document.getElementById('event_date').value = info.event.startStr.slice(0, 10);
            if (info.event.start) {
                const t = info.event.start.toTimeString().slice(0, 5);
                if (!info.event.allDay) document.getElementById('event_start').value = t;
            }
            deleteBtn.classList.remove('d-none');
            eventModal.show();
        },
    });

    calendar.render();

    function resetForm() {
        eventForm.reset();
        document.getElementById('event_id').value = '';
    }

    document.getElementById('newEventBtn').addEventListener('click', () => {
        resetForm();
        modalTitle.textContent = 'Novo compromisso';
        deleteBtn.classList.add('d-none');
    });

    eventForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('event_id').value;
        const url = id ? `${window.APP_URL}/calendario/eventos/${id}` : `${window.APP_URL}/calendario/eventos`;
        const formData = new FormData(eventForm);

        try {
            const response = await fetch(url, { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                eventModal.hide();
                calendar.refetchEvents();
            } else {
                alert(result.message || 'Não foi possível salvar o compromisso.');
            }
        } catch (err) {
            alert('Erro de conexão. Tente novamente.');
        }
    });

    deleteBtn.addEventListener('click', async () => {
        const id = document.getElementById('event_id').value;
        if (!id || !window.confirm('Excluir este compromisso?')) return;

        try {
            const response = await fetch(`${window.APP_URL}/calendario/eventos/${id}/excluir`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(csrfToken),
            });
            const result = await response.json();
            if (result.success) {
                eventModal.hide();
                calendar.refetchEvents();
            }
        } catch (err) {
            alert('Erro de conexão. Tente novamente.');
        }
    });
});
