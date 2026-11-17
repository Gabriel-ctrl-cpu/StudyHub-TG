<?php
/**
 * app/models/Assessment.php
 */

require_once __DIR__ . '/../core/Model.php';

class Assessment extends Model
{
    protected string $table = 'assessments';

    public function upcoming(int $idUsuario, int $limite = 4): array
    {
        $sql = "
            SELECT a.*, s.name AS subject_name, s.color AS subject_color,
                   DATEDIFF(a.assessment_date, CURDATE()) AS days_left
            FROM assessments a
            JOIN subjects s ON s.id = a.subject_id
            WHERE a.user_id = :user_id AND a.assessment_date >= CURDATE()
            ORDER BY a.assessment_date ASC
            LIMIT " . (int) $limite . "
        ";
        return $this->query($sql, ['user_id' => $idUsuario]);
    }

    public function forUser(int $idUsuario): array
    {
        $sql = "
            SELECT a.*, s.name AS subject_name, s.color AS subject_color
            FROM assessments a
            JOIN subjects s ON s.id = a.subject_id
            WHERE a.user_id = :user_id
            ORDER BY a.assessment_date ASC
        ";
        return $this->query($sql, ['user_id' => $idUsuario]);
    }

    public function belongsToUser(int $id, int $idUsuario): bool
    {
        $linha = $this->queryOne('SELECT id FROM assessments WHERE id = :id AND user_id = :user_id', [
            'id' => $id, 'user_id' => $idUsuario,
        ]);
        return $linha !== null;
    }

    /**
     * Avaliações agendadas dentro dos próximos N dias (para notificações).
     */
    public function upcomingWithinDays(int $idUsuario, int $dias = 3): array
    {
        $sql = "
            SELECT a.*, s.name AS subject_name,
                   DATEDIFF(a.assessment_date, CURDATE()) AS days_left
            FROM assessments a
            JOIN subjects s ON s.id = a.subject_id
            WHERE a.user_id = :user_id
              AND a.status = 'agendado'
              AND a.assessment_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':days', $dias, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
