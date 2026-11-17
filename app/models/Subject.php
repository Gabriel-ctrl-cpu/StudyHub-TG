<?php
/**
 * app/models/Subject.php
 */

require_once __DIR__ . '/../core/Model.php';

class Subject extends Model
{
    protected string $table = 'subjects';

    public function forUser(int $idUsuario, bool $incluirArquivadas = false): array
    {
        $sql = 'SELECT * FROM subjects WHERE user_id = :user_id';
        if (!$incluirArquivadas) {
            $sql .= ' AND is_archived = 0';
        }
        $sql .= ' ORDER BY name ASC';
        return $this->query($sql, ['user_id' => $idUsuario]);
    }

    public function belongsToUser(int $id, int $idUsuario): bool
    {
        $linha = $this->queryOne('SELECT id FROM subjects WHERE id = :id AND user_id = :user_id', [
            'id' => $id, 'user_id' => $idUsuario,
        ]);
        return $linha !== null;
    }

    /**
     * Progresso de cada matéria: percentual de tarefas concluídas e minutos estudados.
     */
    public function progressByUser(int $idUsuario): array
    {
        $sql = "
            SELECT
                s.id, s.name, s.color,
                COUNT(DISTINCT t.id) AS total_tasks,
                COUNT(DISTINCT CASE WHEN t.status = 'concluida' THEN t.id END) AS done_tasks,
                COALESCE(SUM(DISTINCT ss.minutes_total), 0) AS minutes_total
            FROM subjects s
            LEFT JOIN tasks t ON t.subject_id = s.id AND t.user_id = s.user_id
            LEFT JOIN (
                SELECT subject_id, SUM(duration_minutes) AS minutes_total
                FROM study_sessions
                WHERE user_id = :user_id_sessions
                GROUP BY subject_id
            ) ss ON ss.subject_id = s.id
            WHERE s.user_id = :user_id_subjects AND s.is_archived = 0
            GROUP BY s.id, s.name, s.color
            ORDER BY s.name ASC
        ";
        return $this->query($sql, ['user_id_sessions' => $idUsuario, 'user_id_subjects' => $idUsuario]);
    }
}
