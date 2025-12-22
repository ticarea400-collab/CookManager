<?php
// 1. CONSTANTES DE RUTAS
define('ROOT_PATH', dirname(__DIR__)); 
define('BASE_URL', 'http://10.1.251.155/CookManager'); // Pon aquí la IP que encontraste

// 2. CONEXIÓN (IMPORTANTE: El host es 172.17.0.1)
define('DB_SERVER', '172.17.0.1'); 
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'S0P0RT3S3N4'); // La que usaste en "docker run"
define('DB_NAME', 'hotel');
define('DB_PORT', 3307);

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

if($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>