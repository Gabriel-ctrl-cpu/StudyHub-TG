<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Goal.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Assessment.php';

class NotificationController extends Controller
{
    public function index(): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];

        $this->syncNotifications($idUsuario);

        $model = new Notification();
        $notificacoes = $model->forUser($idUsuario, 8);
        $naoLidas = $model->unreadCount($idUsuario);

        $this->json([
            'unread' => $naoLidas,
            'items'  => array_map(function ($n) {
                return [
                    'id'      => $n['id'],
                    'title'   => $n['title'],
                    'message' => $n['message'],
                    'type'    => $n['notification_type'],
                    'isRead'  => (bool) $n['is_read'],
                    'date'    => $n['created_at'],
                ];
            }, $notificacoes),
        ]);
    }

    public function markAllRead(): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        (new Notification())->markAllRead((int) $usuario['id']);
        $this->json(['success' => true]);
    }

    private function syncNotifications(int $idUsuario): void
    {
        $modeloNotificacao = new Notification();

        // 1) Metas concluídas automaticamente nesta sincronização
        $metasRecemConcluidas = (new Goal())->syncAutoProgress($idUsuario);
        foreach ($metasRecemConcluidas as $meta) {
            $modeloNotificacao->notifyOnce(
                $idUsuario, 'meta', 'goal', (int) $meta['id'],
                'Meta atingida!',
                'Você concluiu a meta "' . $meta['title'] . '".'
            );
        }

        // 2) Tarefas atrasadas (prazo já vencido, ainda não concluídas)
        foreach ((new Task())->overdue($idUsuario) as $tarefa) {
            $modeloNotificacao->notifyOnce(
                $idUsuario, 'tarefa', 'task', (int) $tarefa['id'],
                'Tarefa atrasada',
                'A tarefa "' . $tarefa['title'] . '" está atrasada.'
            );
        }

        // 3) Avaliações agendadas para os próximos 3 dias
        foreach ((new Assessment())->upcomingWithinDays($idUsuario, 3) as $avaliacao) {
            $diasRestantes = (int) $avaliacao['days_left'];
            $textoQuando = $diasRestantes === 0 ? 'é hoje' : ($diasRestantes === 1 ? 'é amanhã' : "é em {$diasRestantes} dias");
            $modeloNotificacao->notifyOnce(
                $idUsuario, 'avaliacao', 'assessment', (int) $avaliacao['id'],
                'Avaliação se aproximando',
                $avaliacao['title'] . ' (' . $avaliacao['subject_name'] . ') ' . $textoQuando . '.'
            );
        }
    }
}
