<?php

require_once __DIR__ . '/../core/Model.php';

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->queryOne('SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
    }

    public function emailExists(string $email, ?int $idExcluido = null): bool
    {
        if ($idExcluido) {
            $linha = $this->queryOne(
                'SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1',
                ['email' => $email, 'id' => $idExcluido]
            );
        } else {
            $linha = $this->queryOne('SELECT id FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
        }
        return $linha !== null;
    }

    public function register(string $nome, string $email, string $senha, ?string $curso, ?string $instituicao): int
    {
        return $this->create([
            'name'        => $nome,
            'email'       => $email,
            'password'    => password_hash($senha, PASSWORD_BCRYPT),
            'course'      => $curso,
            'institution' => $instituicao,
        ]);
    }

    public function verifyPassword(string $senhaTextoPuro, string $hash): bool
    {
        return password_verify($senhaTextoPuro, $hash);
    }

    public function updatePassword(int $id, string $novaSenha): bool
    {
        return $this->update($id, [
            'password' => password_hash($novaSenha, PASSWORD_BCRYPT),
        ]);
    }

    public function updateTheme(int $id, string $tema): bool
    {
        $tema = in_array($tema, ['light', 'dark'], true) ? $tema : 'light';
        return $this->update($id, ['theme' => $tema]);
    }
}
