<?php
/**
 * app/core/Router.php
 *
 * Router minimalista: mapeia rotas HTTP (método + caminho) para
 * Controller@metodo, suportando parâmetros dinâmicos como {id}.
 */

class Router
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->add('GET', $path, $action);
    }

    public function post(string $path, string $action): void
    {
        $this->add('POST', $path, $action);
    }

    private function add(string $method, string $path, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path'   => trim($path, '/'),
            'action' => $action,
        ];
    }

    /**
     * Resolve a requisição atual e despacha para o Controller correspondente.
     */
    public function dispatch(string $uri, string $method): void
    {
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');

        // Remove o prefixo até a pasta public/, caso o app esteja em subpasta
        $basePath = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($basePath !== '' && str_starts_with($uri, $basePath)) {
            $uri = trim(substr($uri, strlen($basePath)), '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->buildPattern($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->callAction($route['action'], array_values($matches));
                return;
            }
        }

        http_response_code(404);
        $viewPath = __DIR__ . '/../views/errors/404.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo '404 - Página não encontrada.';
        }
    }

    private function buildPattern(string $path): string
    {
        $pattern = preg_replace('/\{[a-zA-Z_]+\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function callAction(string $action, array $params): void
    {
        [$controllerName, $methodName] = explode('@', $action);
        $controllerClass = $controllerName;

        $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            echo "Controller {$controllerClass} não encontrado.";
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controllerClass) || !method_exists($controllerClass, $methodName)) {
            http_response_code(500);
            echo "Método {$methodName} não encontrado em {$controllerClass}.";
            return;
        }

        $controller = new $controllerClass();
        call_user_func_array([$controller, $methodName], $params);
    }
}
