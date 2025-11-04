<?php
// 1. CONSTANTES DE RUTAS (CLAVE PARA INCLUSIONES)
define('ROOT_PATH', dirname(__DIR__)); 
define('BASE_URL', 'http://localhost/COOKMANAGER'); 

// 2. CONSTANTES DE BASE DE DATOS Y CONEXIÓN
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME','hotel');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn->connect_error) {
    // Usamos ROOT_PATH para registrar errores en un archivo, si fuese necesario, o simplemente mostramos el error.
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");

?>