<?php

require_once __DIR__ . '/../core/Model.php';

class Note extends Model
{
    protected string $table = 'notes';

    public function search(int $idUsuario, string $termo = '', ?int $idMateria = null): array
    {
        $conditions = ['n.user_id = :user_id'];
        $params = ['user_id' => $idUsuario];

        if ($termo !== '') {
            $conditions[] = '(n.title LIKE :term_title OR n.content LIKE :term_content OR n.tags LIKE :term_tags)';
            $params['term_title'] = '%' . $termo . '%';
            $params['term_content'] = '%' . $termo . '%';
            $params['term_tags'] = '%' . $termo . '%';
        }

        if ($idMateria) {
            $conditions[] = 'n.subject_id = :subject_id';
            $params['subject_id'] = $idMateria;
        }

        $sql = "
            SELECT n.*, s.name AS subject_name, s.color AS subject_color
            FROM notes n
            LEFT JOIN subjects s ON s.id = n.subject_id
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY n.updated_at DESC
        ";

        return $this->query($sql, $params);
    }

    public function belongsToUser(int $id, int $idUsuario): bool
    {
        $linha = $this->queryOne('SELECT id FROM notes WHERE id = :id AND user_id = :user_id', [
            'id' => $id, 'user_id' => $idUsuario,
        ]);
        return $linha !== null;
    }
}
