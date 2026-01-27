<?php
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['lat']) || !isset($_GET['lon'])) {
    echo json_encode(['error' => 'Faltan parámetros']);
    exit;
}

$lat = $_GET['lat'];
$lon = $_GET['lon'];

$url = "https://nominatim.openstreetmap.org/reverse?" .
       "format=json&lat={$lat}&lon={$lon}&addressdetails=1";

$opts = [
    "http" => [
        "header" => "User-Agent: FoodCourt-App\r\n"
    ]
];

$context = stream_context_create($opts);
$response = @file_get_contents($url, false, $context);

echo $response ?: json_encode(['error' => 'No se pudo obtener la dirección']);
