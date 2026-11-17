<?php

require_once __DIR__ . '/../core/Model.php';

class Task extends Model
{
    protected string $table = 'tasks';

    public function countByStatus(int $idUsuario, string $situacao): int
    {
        $linha = $this->queryOne(
            'SELECT COUNT(*) AS total FROM tasks WHERE user_id = :user_id AND status = :status',
            ['user_id' => $idUsuario, 'status' => $situacao]
        );
        return (int) ($linha['total'] ?? 0);
    }

    public function countAll(int $idUsuario): int
    {
        $linha = $this->queryOne('SELECT COUNT(*) AS total FROM tasks WHERE user_id = :user_id', ['user_id' => $idUsuario]);
        return (int) ($linha['total'] ?? 0);
    }

    public function upcoming(int $idUsuario, int $limite = 4): array
    {
        $sql = "
            SELECT t.*, s.name AS subject_name, s.color AS subject_color
            FROM tasks t
            LEFT JOIN subjects s ON s.id = t.subject_id
            WHERE t.user_id = :user_id AND t.status IN ('pendente', 'em_andamento')
            ORDER BY (t.due_date IS NULL), t.due_date ASC, t.due_time ASC
            LIMIT " . (int) $limite . "
        ";
        return $this->query($sql, ['user_id' => $idUsuario]);
    }

    public function belongsToUser(int $id, int $idUsuario): bool
    {
        $linha = $this->queryOne('SELECT id FROM tasks WHERE id = :id AND user_id = :user_id', [
            'id' => $id, 'user_id' => $idUsuario,
        ]);
        return $linha !== null;
    }

    /**
     * Todas as tarefas do usuário que possuem prazo definido (para o calendário).
     */
    public function withDueDate(int $idUsuario): array
    {
        $sql = "
            SELECT t.*, s.name AS subject_name, s.color AS subject_color
            FROM tasks t
            LEFT JOIN subjects s ON s.id = t.subject_id
            WHERE t.user_id = :user_id AND t.due_date IS NOT NULL
        ";
        return $this->query($sql, ['user_id' => $idUsuario]);
    }

    public function overdue(int $idUsuario): array
    {
        $sql = "
            SELECT * FROM tasks
            WHERE user_id = :user_id
              AND status IN ('pendente', 'em_andamento')
              AND due_date IS NOT NULL AND due_date < CURDATE()
        ";
        return $this->query($sql, ['user_id' => $idUsuario]);
    }

    public function completedByDay(int $idUsuario, int $dias = 7): array
    {
        $sql = "
            SELECT DATE(completed_at) AS completed_date, COUNT(*) AS total
            FROM tasks
            WHERE user_id = :user_id
              AND status = 'concluida'
              AND completed_at IS NOT NULL
              AND completed_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(completed_at)
            ORDER BY completed_date ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':days', $dias, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function completionRate(int $idUsuario): int
    {
        $linha = $this->queryOne(
            "SELECT COUNT(*) AS total, SUM(status = 'concluida') AS done FROM tasks WHERE user_id = :user_id",
            ['user_id' => $idUsuario]
        );
        $total = (int) ($linha['total'] ?? 0);
        $concluidas  = (int) ($linha['done'] ?? 0);
        return $total > 0 ? (int) round(($concluidas / $total) * 100) : 0;
    }


    public function filtered(int $idUsuario, array $filtros = []): array
    {
        $conditions = ['t.user_id = :user_id'];
        $params = ['user_id' => $idUsuario];

        $periodo = $filtros['period'] ?? 'todas';
        if ($periodo === 'hoje') {
            $conditions[] = 't.due_date = CURDATE()';
        } elseif ($periodo === 'semana') {
            $conditions[] = 't.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)';
        }

        if (!empty($filtros['status'])) {
            $conditions[] = 't.status = :status';
            $params['status'] = $filtros['status'];
        }

        if (!empty($filtros['priority'])) {
            $conditions[] = 't.priority = :priority';
            $params['priority'] = $filtros['priority'];
        }

        if (!empty($filtros['subject_id'])) {
            $conditions[] = 't.subject_id = :subject_id';
            $params['subject_id'] = $filtros['subject_id'];
        }

        if (!empty($filtros['search'])) {
            $conditions[] = 't.title LIKE :search';
            $params['search'] = '%' . $filtros['search'] . '%';
        }

        $sql = "
            SELECT t.*, s.name AS subject_name, s.color AS subject_color
            FROM tasks t
            LEFT JOIN subjects s ON s.id = t.subject_id
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY
                FIELD(t.status, 'em_andamento', 'pendente', 'concluida', 'cancelada'),
                (t.due_date IS NULL), t.due_date ASC, t.due_time ASC
        ";

        return $this->query($sql, $params);
    }

    public static function computeStatus(array $tarefa): array
    {
        $hoje = date('Y-m-d');

        if ($tarefa['status'] === 'cancelada') {
            return ['label' => 'Cancelada', 'css' => 'cancelada'];
        }

        if ($tarefa['status'] === 'concluida') {
            if (!empty($tarefa['due_date']) && !empty($tarefa['completed_at'])
                && substr($tarefa['completed_at'], 0, 10) > $tarefa['due_date']) {
                return ['label' => 'Concluída com atraso', 'css' => 'concluida-atraso'];
            }
            return ['label' => 'Concluída', 'css' => 'concluida'];
        }

        // pendente ou em_andamento
        if (!empty($tarefa['due_date']) && $tarefa['due_date'] < $hoje) {
            return ['label' => 'Atrasada', 'css' => 'atrasada'];
        }

        return $tarefa['status'] === 'em_andamento'
            ? ['label' => 'Em andamento', 'css' => 'em_andamento']
            : ['label' => 'Pendente', 'css' => 'pendente'];
    }
}
