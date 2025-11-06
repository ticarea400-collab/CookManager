<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. VALIDACIÓN DE ACCESO Y ROL

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'SuperAdmin') {
    header("Location: /index.php"); 

    exit;
}

include_once('../config/conection.php');

$nombre_user = $_SESSION['nombre_usuario'] ?? 'Usuario Desconocido';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
    <title>SuperAdmin</title>
</head>

<body class="start">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

    <h1>Bienvenido, <?= htmlspecialchars($nombre_user) ?></h1>
    <p>Gestión y control del sistema</p>

    
    <script src="<?php echo BASE_URL; ?>/js/index.js"></script>
    
</body>
</html>