<?php
/**
 * app/controllers/FocusController.php
 *
 * Modo Foco: exibe o cronômetro (Pomodoro) e registra automaticamente
 * a sessão de estudo no banco quando o usuário finaliza um ciclo.
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../models/StudySession.php';

class FocusController extends Controller
{
    public function index(): void
    {
        $usuario = $this->requireAuth();
        $materias = (new Subject())->forUser((int) $usuario['id']);
        $sessoesRecentes = (new StudySession())->recent((int) $usuario['id'], 6);

        $this->render('foco/index', [
            'materias'       => $materias,
            'sessoesRecentes' => $sessoesRecentes,
            'tokenCsrf'      => $this->csrfToken(),
        ], 'app');
    }

    /**
     * Registra (via AJAX) a sessão de estudo concluída no Modo Foco.
     */
    public function store(): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();

        $idMateria = trim($_POST['subject_id'] ?? '');
        $duracao  = (int) ($_POST['duration_minutes'] ?? 0);

        if ($idMateria === '' || $duracao <= 0) {
            $this->json(['success' => false, 'message' => 'Selecione uma matéria e informe uma duração válida.'], 422);
        }

        $modeloMateria = new Subject();
        if (!$modeloMateria->belongsToUser((int) $idMateria, (int) $usuario['id'])) {
            $this->json(['success' => false, 'message' => 'Matéria inválida.'], 422);
        }

        $modeloSessao = new StudySession();
        $id = $modeloSessao->create([
            'user_id'          => (int) $usuario['id'],
            'subject_id'       => (int) $idMateria,
            'duration_minutes' => $duracao,
            'session_type'     => 'foco',
            'session_date'     => date('Y-m-d'),
            'started_at'       => $_POST['started_at'] ?? date('Y-m-d H:i:s'),
            'finished_at'      => date('Y-m-d H:i:s'),
        ]);

        $this->json(['success' => true, 'id' => $id, 'duration' => $duracao]);
    }
}
