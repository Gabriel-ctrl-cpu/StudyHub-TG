<?php
/**
 * config/database.php
 *
 * Configuração central de conexão com o banco de dados MySQL via PDO.
 * Ajuste as constantes abaixo conforme o ambiente (XAMPP por padrão).
 */

// ---------------------------------------------------------------------
// Credenciais do banco de dados
// ---------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub');
define('DB_USER', 'root');
define('DB_PASS', '');       // XAMPP: senha padrão do root é vazia
define('DB_CHARSET', 'utf8mb4');

// ---------------------------------------------------------------------
// Configurações gerais da aplicação
// ---------------------------------------------------------------------
define('APP_NAME', 'StudyHub');
define('APP_URL', 'http://localhost/studyhub/public');
define('APP_ENV', 'development'); // development | production

// Exibir erros apenas em desenvolvimento
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

/**
 * Retorna uma instância única (singleton) de PDO conectada ao banco.
 *
 * @return PDO
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (APP_ENV === 'development') {
                die('Erro de conexão com o banco de dados: ' . $e->getMessage());
            }
            die('Erro ao conectar ao banco de dados. Tente novamente mais tarde.');
        }
    }

    return $pdo;
}
