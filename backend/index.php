<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'connection/connection.php';  
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    $input = [];
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$isEmpleado = $_POST['isEmpleado'] ?? '0';
$usuario = trim($_POST['usuario'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

if (empty($usuario) || empty($contrasena)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
}

if ($isEmpleado === '1') {
    $stmt = $pdo->prepare("
        SELECT CI_E AS ci, id_rol AS rol, nom1, nom2, ap1, ap2, email, usuario
        FROM templeados
        WHERE usuario = ? AND contrasena = ? AND estado = 1
        LIMIT 1
    ");
    $stmt->execute([$usuario, $contrasena]);
    $userData = $stmt->fetch();

    if (!$userData) {
        $stmt = $pdo->prepare("
            SELECT CI_R AS ci, id_rol AS rol, nom1, nom2, ap1, ap2, email, usuario
            FROM trepartidor
            WHERE usuario = ? AND contrasena = ? AND estado = 1
            LIMIT 1
        ");
        $stmt->execute([$usuario, $contrasena]);
        $userData = $stmt->fetch();
    }
} else {
    $stmt = $pdo->prepare("
        SELECT CI_C AS ci, id_rol AS rol, nom1, nom2, ap1, ap2, email, usuario
        FROM tcliente
        WHERE usuario = ? AND contrasena = ? AND estado = 1
        LIMIT 1
    ");
    $stmt->execute([$usuario, $contrasena]);
    $userData = $stmt->fetch();
}

if (!$userData) {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas o usuario inactivo']);
    exit;
}

$nombreCompleto = trim("{$userData['nom1']} {$userData['nom2']} {$userData['ap1']} {$userData['ap2']}");
echo json_encode([
    'success' => true,
    'usuario' => [
        'ci'     => $userData['ci'],
        'rol'    => $userData['rol'],
        'nombre' => $nombreCompleto,
        'email'  => $userData['email'],
        'usuario'=> $userData['usuario']
    ]
    
]);