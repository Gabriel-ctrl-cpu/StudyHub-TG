<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub');
define('DB_USER', 'root');
define('DB_PASS', '');     
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'StudyHub');
define('APP_URL', 'http://localhost/studyhub/public');
define('APP_ENV', 'development'); // development | production

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

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
