<?php
include("./config/conection.php");
session_start();

//Redirigir
if(isset($_SESSION['user_id'])) {
    header('location: dashboard.php');
    exit();
}

$usuario_input = isset($_POST['usuario']) ? htmlspecialchars(trim($_POST['usuario'])) : '';
$contrasena_input = isset($_POST['pass']) ? $_POST['pass'] : '';
$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(empty($usuario_input) || empty($contrasena_input)) {
        $errors[] = "Por favor ingresar su usuario y contraseña.";
    } else {
        $sql = "SELECT id, usuario, rol, pass FROM usuarios WHERE usuario = ?";      

        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $usuario_input);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows === 1) {
                $user_data = $result->fetch_assoc();
                $hash_contrasena = $user_data['pass'];

                if(password_verify($contrasena_input, $hash_contrasena)) {
                    session_regenerate_id(true);
        
                    $_SESSION['usuario'] = $user_data['nombre'];
                    $_SESSION['rol'] = $user_data['rol'];
                    $_SESSION['user_id'] = $user_data['id'];
        
                    //REDIRECCIÓN SEGÚN EL ROL
                    switch ($user_data['rol']) {
                        case 'Super Admin':
                            header("Location: ./SuperAdmin/dashboard.php");
                            break;
                            
                        case 'Administrador':
                            header("Location: ./dashboard.php");
                            break;
                        
                        default:
                            header("Location: ./contratistas/dashboard.php");
                            break;
                    }
                    exit;
                } else {
                    $errors[] = "Correo o contraseña incorrectos.";
                }
            } else {
                $errors[] = "Correo o contraseña incorrectos.";
            }
            $stmt->close();
        } else {
            $errors[] = "Error interno del servidor.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./css/estilos.css">
    <title>Inicio de sesión</title>
</head>
<body class="start">
    <div class="form-container">
        <img src="./img/logo.jpg" alt="Logo Sena" class="logoSena">
    
        <div class="divForm">
            <h1>Inicio de Sesión</h1>

            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <img src="./img/error_equis.png" alt="" class="iconoErrors">
                    <?php foreach ($errors as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="text" id="usuario" name="usuario" placeholder="Usuario" required/>

                <!-- Contenedor del campo de contraseña con ícono de alternar visibilidad -->
                <div class="password-container">
                    <input type="password" id="pass" name="pass" placeholder="Contraseña" required/>
                    <span id="togglePassword" class="toggle-password fas fa-eye"></span>
                </div>

                <input type="submit" value="ENTRAR" class="btonEnter"/>
            </form>
        </div>
    </div>

    <script src="./js/index.js"></script>
</body>
</html>