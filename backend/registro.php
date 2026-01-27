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

$CI_C       = trim($_POST['CI_C'] ?? '');
$usuario    = trim($_POST['usuario'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$nom1       = trim($_POST['nom1'] ?? '');
$nom2       = trim($_POST['nom2'] ?? '');
$ap1        = trim($_POST['ap1'] ?? '');
$ap2        = trim($_POST['ap2'] ?? '');
$email      = trim($_POST['email'] ?? '');
$telefono   = trim($_POST['telefono'] ?? '');
$direccion  = trim($_POST['direccion'] ?? '');
$sexo       = $_POST['sexo'] ?? null;       
$f_Na       = $_POST['f_Na'] ?? null;
$latitud  = $_POST['latitud'] ?? null;
$longitud = $_POST['longitud'] ?? null;
$id_rol     = 3;                            

if (empty($CI_C) || empty($usuario) || empty($contrasena) || empty($nom1) || empty($ap1) || empty($email) || empty($telefono) || empty($direccion)) {
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

$stmt = $pdo->prepare("SELECT CI_C FROM tcliente WHERE CI_C = ? OR usuario = ?");
$stmt->execute([$CI_C, $usuario]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'La cédula o usuario ya está registrado']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO tcliente (
            CI_C, id_rol, nom1, nom2, ap1, ap2, email, direccion, 
            f_Na, telefono, sexo, usuario, contrasena, 
            latitud, longitud,
            usuarioA, fechaA, estado
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, 
            ?, NOW(), 1
        )
    ");

    $stmt->execute([
        $CI_C,
        $id_rol,
        $nom1,
        $nom2 ?: null,
        $ap1,
        $ap2 ?: null,
        $email,
        $direccion,
        $f_Na ?: null,
        $telefono,
        $sexo !== '' ? $sexo : null,
        $usuario,
        $contrasena,     
        $latitud,
        $longitud,      
        'sistema_registro',    
    ]);

    echo json_encode(['success' => true, 'message' => 'Cliente registrado correctamente']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()]);
}