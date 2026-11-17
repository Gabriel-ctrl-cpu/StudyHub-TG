<?php

class Controller
{
 
    protected function render(string $view, array $data = [], string $layout = 'app'): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die("View não encontrada: {$view}");
        }

        $content = function () use ($viewFile, $data) {
            extract($data);
            require $viewFile;
        };

        $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            $content();
        }
    }

    protected function renderPartial(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die("View não encontrada: {$view}");
        }
        require $viewFile;
    }

    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $path): void
    {
        $base = rtrim(APP_URL, '/');
        header('Location: ' . $base . '/' . ltrim($path, '/'));
        exit;
    }

    protected function requireAuth(): array
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        return [
            'id'   => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? '',
        ];
    }

    protected function currentUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    protected function flash(string $key, ?string $message = null)
    {
        if ($message !== null) {
            $_SESSION['flash'][$key] = $message;
            return null;
        }

        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    protected function old(string $key, string $default = ''): string
    {
        $value = $_SESSION['old'][$key] ?? $default;
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    protected function setOld(array $data): void
    {
        $_SESSION['old'] = $data;
    }

    protected function clearOld(): void
    {
        unset($_SESSION['old']);
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(419);
            die('Token de segurança inválido ou expirado. Recarregue a página e tente novamente.');
        }
    }

    protected function sanitize(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}
