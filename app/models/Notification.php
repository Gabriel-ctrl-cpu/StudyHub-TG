<?php

require_once __DIR__ . '/../core/Model.php';

class Notification extends Model
{
    protected string $table = 'notifications';

    public function forUser(int $idUsuario, int $limite = 8): array
    {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT " . (int) $limite;
        return $this->query($sql, ['user_id' => $idUsuario]);
    }

    public function unreadCount(int $idUsuario): int
    {
        $linha = $this->queryOne(
            'SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id AND is_read = 0',
            ['user_id' => $idUsuario]
        );
        return (int) ($linha['total'] ?? 0);
    }

    public function markAllRead(int $idUsuario): bool
    {
        return $this->execute(
            'UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0',
            ['user_id' => $idUsuario]
        );
    }

    public function notifyOnce(int $idUsuario, string $tipo, string $tipoRelacionado, int $idRelacionado, string $titulo, string $mensagem): void
    {
        $sql = "INSERT IGNORE INTO notifications (user_id, title, message, notification_type, related_type, related_id)
                VALUES (:user_id, :title, :message, :type, :related_type, :related_id)";
        $this->execute($sql, [
            'user_id'      => $idUsuario,
            'title'        => $titulo,
            'message'      => $mensagem,
            'type'         => $tipo,
            'related_type' => $tipoRelacionado,
            'related_id'   => $idRelacionado,
        ]);
    }
}
