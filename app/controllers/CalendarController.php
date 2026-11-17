<?php
/**
 * app/controllers/CalendarController.php
 *
 * Exibe o calendário (FullCalendar) agregando tarefas, avaliações,
 * sessões de estudo e compromissos/eventos do usuário. Também
 * permite criar/editar/excluir eventos diretamente pelo calendário.
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Assessment.php';
require_once __DIR__ . '/../models/StudySession.php';
require_once __DIR__ . '/../models/Subject.php';

class CalendarController extends Controller
{
    private Event $modeloEvento;

    public function __construct()
    {
        $this->modeloEvento = new Event();
    }

    public function index(): void
    {
        $usuario = $this->requireAuth();
        $materias = (new Subject())->forUser((int) $usuario['id']);

        $this->render('calendario/index', [
            'materias'  => $materias,
            'tokenCsrf' => $this->csrfToken(),
        ], 'app');
    }

    /**
     * Feed JSON no formato esperado pelo FullCalendar, agregando
     * tarefas (prazo), avaliações, sessões de estudo e eventos.
     */
    public function data(): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];

        $items = [];

        // Tarefas com prazo
        foreach ((new Task())->withDueDate($idUsuario) as $t) {
            $inicio = $t['due_date'] . ($t['due_time'] ? 'T' . $t['due_time'] : '');
            $items[] = [
                'id'    => 'task-' . $t['id'],
                'title' => '📝 ' . $t['title'],
                'start' => $inicio,
                'allDay' => empty($t['due_time']),
                'color' => $t['subject_color'] ?? '#2563eb',
                'extendedProps' => ['type' => 'tarefa', 'status' => $t['status']],
            ];
        }

        // Avaliações
        foreach ((new Assessment())->forUser($idUsuario) as $a) {
            $inicio = $a['assessment_date'] . ($a['assessment_time'] ? 'T' . $a['assessment_time'] : '');
            $rotulos = ['prova' => '📌 Prova', 'trabalho' => '📎 Trabalho', 'seminario' => '🎤 Seminário', 'projeto' => '🗂️ Projeto', 'outro' => '📄 Avaliação'];
            $items[] = [
                'id'    => 'assessment-' . $a['id'],
                'title' => ($rotulos[$a['assessment_type']] ?? '📄') . ': ' . $a['title'],
                'start' => $inicio,
                'allDay' => empty($a['assessment_time']),
                'color' => $a['subject_color'],
                'extendedProps' => ['type' => 'avaliacao'],
            ];
        }

        // Sessões de estudo
        foreach ((new StudySession())->forUser($idUsuario) as $s) {
            $items[] = [
                'id'    => 'session-' . $s['id'],
                'title' => '⏱️ ' . ($s['subject_name'] ?? 'Sessão de estudo') . ' (' . $s['duration_minutes'] . 'min)',
                'start' => $s['session_date'],
                'allDay' => true,
                'color' => $s['subject_color'] ?? '#7c3aed',
                'extendedProps' => ['type' => 'sessao'],
            ];
        }

        // Eventos/compromissos
        foreach ($this->modeloEvento->forUser($idUsuario) as $e) {
            $inicio = $e['event_date'] . ($e['start_time'] ? 'T' . $e['start_time'] : '');
            $fim = $e['end_time'] ? $e['event_date'] . 'T' . $e['end_time'] : null;
            $icones = ['compromisso' => '📅', 'lembrete' => '🔔', 'outro' => '🗒️'];
            $items[] = [
                'id'    => 'event-' . $e['id'],
                'title' => ($icones[$e['event_type']] ?? '📅') . ' ' . $e['title'],
                'start' => $inicio,
                'end'   => $fim,
                'allDay' => empty($e['start_time']),
                'color' => $e['subject_color'] ?? '#16a34a',
                'extendedProps' => ['type' => 'evento', 'editable_id' => $e['id']],
            ];
        }

        $this->json($items);
    }

    // -------------------------------------------------------------
    // CRUD de eventos (compromissos criados diretamente no calendário)
    // -------------------------------------------------------------

    public function store(): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();

        $dados = $this->validatedData();
        if ($dados === null) {
            $this->json(['success' => false, 'message' => 'Preencha os campos obrigatórios.'], 422);
        }

        $dados['user_id'] = (int) $usuario['id'];
        $id = $this->modeloEvento->create($dados);

        $this->json(['success' => true, 'id' => $id]);
    }

    public function update(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idEvento = (int) $id;

        if (!$this->modeloEvento->belongsToUser($idEvento, $idUsuario)) {
            $this->json(['success' => false, 'message' => 'Evento não encontrado.'], 404);
        }

        $dados = $this->validatedData();
        if ($dados === null) {
            $this->json(['success' => false, 'message' => 'Preencha os campos obrigatórios.'], 422);
        }

        $this->modeloEvento->update($idEvento, $dados);
        $this->json(['success' => true]);
    }

    public function destroy(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idEvento = (int) $id;

        if ($this->modeloEvento->belongsToUser($idEvento, $idUsuario)) {
            $this->modeloEvento->delete($idEvento);
            $this->json(['success' => true]);
        } else {
            $this->json(['success' => false, 'message' => 'Evento não encontrado.'], 404);
        }
    }

    private function validatedData(): ?array
    {
        $titulo = trim($_POST['title'] ?? '');
        $descricao = trim($_POST['description'] ?? '') ?: null;
        $idMateria = trim($_POST['subject_id'] ?? '') ?: null;
        $tipoEvento = $_POST['event_type'] ?? 'compromisso';
        $dataEvento = trim($_POST['event_date'] ?? '');
        $horaInicio = trim($_POST['start_time'] ?? '') ?: null;
        $horaFim = trim($_POST['end_time'] ?? '') ?: null;

        if ($titulo === '' || $dataEvento === '') {
            return null;
        }
        if (!in_array($tipoEvento, ['compromisso', 'lembrete', 'outro'], true)) {
            $tipoEvento = 'compromisso';
        }

        return [
            'title'       => $titulo,
            'description' => $descricao,
            'subject_id'  => $idMateria,
            'event_type'  => $tipoEvento,
            'event_date'  => $dataEvento,
            'start_time'  => $horaInicio,
            'end_time'    => $horaFim,
        ];
    }
}
