<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificar_rol($roles_permitidos) {
    if(!isset($_SESSION['user_id'])) {
        header("Location: /index.php");
        exit();
    }

    $rol_usuario_actual = $_SESSION['rol'];

    if (!is_array($roles_permitidos)) {
        $roles_permitidos = [$roles_permitidos];
    }

    if(!in_array($rol_usuario_actual, $roles_permitidos)) {
        switch ($rol_usuario_actual) {
            case 'SuperAdmin':
                $redireccion = '/SuperAdmin/dashboard.php';
                break;
            case 'Administrador':
                $redireccion = '/dashboard.php';
                break;
            case 'Contratista':
                $redireccion = '/contratistas/dashboard.php'; // Ajusta la ruta si es necesario
                break;
            default:
                $redireccion = '../index.php'; // Página genérica de error
                break;
        }

        header("Location: " . $redireccion);
        exit();
    }
}

?>