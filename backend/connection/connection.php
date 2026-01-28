<?php

// Cargar variables de entorno (Railway las provee directamente, no necesita .env)
$host    = $_ENV['DB_HOST']    ?? 'localhost';          // fallback local
$db      = $_ENV['DB_NAME']    ?? 'FoodCourtv1';
$user    = $_ENV['DB_USER']    ?? 'root';
$pass    = $_ENV['DB_PASS']    ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

// Para desarrollo local (opcional: puedes usar un .env real con vlucas/phpdotenv si quieres)
if (file_exists(__DIR__ . '/.env')) {
    require_once __DIR__ . '/vendor/autoload.php'; // si instalas phpdotenv
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $host    = $_ENV['DB_HOST']    ?? $host;
    $db      = $_ENV['DB_NAME']    ?? $db;
    $user    = $_ENV['DB_USER']    ?? $user;
    $pass    = $_ENV['DB_PASS']    ?? $pass;
    $charset = $_ENV['DB_CHARSET'] ?? $charset;
}

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // En producción (Railway) no mostramos detalles del error
    http_response_code(500);
    if (isset($_ENV['RAILWAY_ENVIRONMENT'])) {
        // Estamos en Railway → respuesta limpia
        echo json_encode([
            'success' => false,
            'message' => 'Error interno del servidor'
        ]);
    } else {
        // Local → mostramos más info para depurar
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos',
            'debug' => $e->getMessage()
        ]);
    }
    exit;
}