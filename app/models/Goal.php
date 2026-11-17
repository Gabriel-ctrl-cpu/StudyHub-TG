<?php
/**
 * app/models/Goal.php
 */

require_once __DIR__ . '/../core/Model.php';

class Goal extends Model
{
    protected string $table = 'goals';

    public function forUser(int $idUsuario, int $limite = null): array
    {
        $sql = 'SELECT * FROM goals WHERE user_id = :user_id ORDER BY is_completed ASC, deadline ASC';
        if ($limite) {
            $sql .= ' LIMIT ' . (int) $limite;
        }
        return $this->query($sql, ['user_id' => $idUsuario]);
    }

    public function completionPercentage(int $idUsuario): int
    {
        $linha = $this->queryOne(
            'SELECT COUNT(*) AS total, SUM(is_completed) AS done FROM goals WHERE user_id = :user_id',
            ['user_id' => $idUsuario]
        );
        $total = (int) ($linha['total'] ?? 0);
        $concluidas  = (int) ($linha['done'] ?? 0);
        return $total > 0 ? (int) round(($concluidas / $total) * 100) : 0;
    }

    /**
     * Recalcula automaticamente o progresso das metas do tipo "horas"
     * (a partir das sessões de estudo reais) e "tarefas" (a partir das
     * tarefas concluídas), sempre que o usuário abre a tela de Metas —
     * elimina a necessidade de atualizar manualmente esses dois tipos.
     * Metas do tipo "exercicios" e "personalizada" continuam manuais,
     * pois não existe uma tabela de origem para calculá-las sozinhas.
     */
    /**
     * @return array Metas que acabaram de ser concluídas nesta chamada
     *               (para o Controller poder gerar uma notificação, por exemplo).
     */
    public function syncAutoProgress(int $idUsuario): array
    {
        $metas = $this->query(
            "SELECT * FROM goals WHERE user_id = :user_id AND goal_type IN ('horas', 'tarefas') AND is_completed = 0",
            ['user_id' => $idUsuario]
        );

        $recemConcluidas = [];

        foreach ($metas as $meta) {
            if ($meta['goal_type'] === 'horas') {
                $novoValor = $this->hoursStudiedSince($idUsuario, $meta['subject_id'], $meta['created_at']);
            } else {
                $novoValor = $this->tasksCompletedSince($idUsuario, $meta['subject_id'], $meta['created_at']);
            }

            $metaConcluida = $novoValor >= (float) $meta['target_value'] ? 1 : 0;

            if ($novoValor != $meta['current_value'] || $metaConcluida != $meta['is_completed']) {
                $this->update((int) $meta['id'], [
                    'current_value' => $novoValor,
                    'is_completed'  => $metaConcluida,
                ]);

                if ($metaConcluida && !$meta['is_completed']) {
                    $meta['current_value'] = $novoValor;
                    $recemConcluidas[] = $meta;
                }
            }
        }

        return $recemConcluidas;
    }

    private function hoursStudiedSince(int $idUsuario, ?int $idMateria, string $desde): float
    {
        $sql = "SELECT COALESCE(SUM(duration_minutes), 0) AS total FROM study_sessions
                WHERE user_id = :user_id AND created_at >= :since";
        $params = ['user_id' => $idUsuario, 'since' => $desde];
        if ($idMateria) {
            $sql .= ' AND subject_id = :subject_id';
            $params['subject_id'] = $idMateria;
        }
        $linha = $this->queryOne($sql, $params);
        return round(((int) ($linha['total'] ?? 0)) / 60, 1);
    }

    private function tasksCompletedSince(int $idUsuario, ?int $idMateria, string $desde): int
    {
        $sql = "SELECT COUNT(*) AS total FROM tasks
                WHERE user_id = :user_id AND status = 'concluida' AND completed_at >= :since";
        $params = ['user_id' => $idUsuario, 'since' => $desde];
        if ($idMateria) {
            $sql .= ' AND subject_id = :subject_id';
            $params['subject_id'] = $idMateria;
        }
        $linha = $this->queryOne($sql, $params);
        return (int) ($linha['total'] ?? 0);
    }
}
