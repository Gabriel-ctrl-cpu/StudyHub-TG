<?php
/**
 * app/models/StudySession.php
 */

require_once __DIR__ . '/../core/Model.php';

class StudySession extends Model
{
    protected string $table = 'study_sessions';

    public function minutesToday(int $idUsuario): int
    {
        $linha = $this->queryOne(
            "SELECT COALESCE(SUM(duration_minutes), 0) AS total FROM study_sessions WHERE user_id = :user_id AND session_date = CURDATE()",
            ['user_id' => $idUsuario]
        );
        return (int) ($linha['total'] ?? 0);
    }

    /**
     * Minutos estudados por dia nos últimos N dias (para o gráfico semanal).
     */
    public function minutesByDay(int $idUsuario, int $dias = 7): array
    {
        $sql = "
            SELECT session_date, SUM(duration_minutes) AS total_minutes
            FROM study_sessions
            WHERE user_id = :user_id AND session_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY session_date
            ORDER BY session_date ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':days', $dias, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function recent(int $idUsuario, int $limite = 4): array
    {
        $sql = "
            SELECT ss.*, sub.name AS subject_name, sub.color AS subject_color
            FROM study_sessions ss
            LEFT JOIN subjects sub ON sub.id = ss.subject_id
            WHERE ss.user_id = :user_id
            ORDER BY ss.session_date DESC, ss.id DESC
            LIMIT " . (int) $limite . "
        ";
        return $this->query($sql, ['user_id' => $idUsuario]);
    }

    public function forUser(int $idUsuario): array
    {
        $sql = "
            SELECT ss.*, sub.name AS subject_name, sub.color AS subject_color
            FROM study_sessions ss
            LEFT JOIN subjects sub ON sub.id = ss.subject_id
            WHERE ss.user_id = :user_id
            ORDER BY ss.session_date DESC
        ";
        return $this->query($sql, ['user_id' => $idUsuario]);
    }

    public function minutesThisWeek(int $idUsuario): int
    {
        $linha = $this->queryOne(
            "SELECT COALESCE(SUM(duration_minutes), 0) AS total FROM study_sessions
             WHERE user_id = :user_id AND YEARWEEK(session_date, 1) = YEARWEEK(CURDATE(), 1)",
            ['user_id' => $idUsuario]
        );
        return (int) ($linha['total'] ?? 0);
    }

    public function minutesThisMonth(int $idUsuario): int
    {
        $linha = $this->queryOne(
            "SELECT COALESCE(SUM(duration_minutes), 0) AS total FROM study_sessions
             WHERE user_id = :user_id AND YEAR(session_date) = YEAR(CURDATE()) AND MONTH(session_date) = MONTH(CURDATE())",
            ['user_id' => $idUsuario]
        );
        return (int) ($linha['total'] ?? 0);
    }

    public function minutesTotal(int $idUsuario): int
    {
        $linha = $this->queryOne(
            'SELECT COALESCE(SUM(duration_minutes), 0) AS total FROM study_sessions WHERE user_id = :user_id',
            ['user_id' => $idUsuario]
        );
        return (int) ($linha['total'] ?? 0);
    }

    /**
     * Minutos estudados agrupados por semana, últimas N semanas (evolução semanal).
     */
    public function minutesByWeek(int $idUsuario, int $semanas = 6): array
    {
        $sql = "
            SELECT
                YEARWEEK(session_date, 1) AS year_week,
                MIN(session_date) AS week_start,
                SUM(duration_minutes) AS total_minutes
            FROM study_sessions
            WHERE user_id = :user_id
              AND session_date >= DATE_SUB(CURDATE(), INTERVAL :weeks WEEK)
            GROUP BY YEARWEEK(session_date, 1)
            ORDER BY year_week ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':weeks', $semanas, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
