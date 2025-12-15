<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Guardar la página anterior SOLO si viene de otra página
if (!empty($_SERVER['HTTP_REFERER'])) {
    $_SESSION['return_to'] = $_SERVER['HTTP_REFERER'];
}

?>
