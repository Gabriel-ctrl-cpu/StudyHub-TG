<?php
/**
 * app/models/Event.php
 */

require_once __DIR__ . '/../core/Model.php';

class Event extends Model
{
    protected string $table = 'events';

    public function forUser(int $idUsuario): array
    {
        return $this->query(
            'SELECT e.*, s.name AS subject_name, s.color AS subject_color
             FROM events e
             LEFT JOIN subjects s ON s.id = e.subject_id
             WHERE e.user_id = :user_id
             ORDER BY e.event_date ASC, e.start_time ASC',
            ['user_id' => $idUsuario]
        );
    }

    public function belongsToUser(int $id, int $idUsuario): bool
    {
        $linha = $this->queryOne('SELECT id FROM events WHERE id = :id AND user_id = :user_id', [
            'id' => $id, 'user_id' => $idUsuario,
        ]);
        return $linha !== null;
    }
}
