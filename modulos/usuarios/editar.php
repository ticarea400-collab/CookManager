<?php
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('SuperAdmin');

$errors = [];
$success = '';
$usuario_data = [];

// --- Verificar si se pasa un ID por GET ---
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario_data = $result->fetch_assoc();
    } else {
        $errors[] = "Usuario no encontrado.";
    }

    $stmt->close();
} else {
    $errors[] = "No se proporcionó un ID válido.";
}

// --- Si el formulario fue enviado ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_cambios'])) {
    $id = intval($_POST['id']);
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $usuario = trim($_POST['usuario']);
    $pass = $_POST['pass'];
    $rol = $_POST['rol'];

    if (empty($nombre_usuario) || empty($usuario) || empty($rol)) {
        $errors[] = "Los campos Nombre, Correo y Rol son obligatorios.";
    } elseif (!filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo no es válido.";
    } else {
        // Si se envió una nueva contraseña, la encripta
        if (!empty($pass)) {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre_usuario=?, usuario=?, pass=?, rol=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssi', $nombre_usuario, $usuario, $hashed_pass, $rol, $id);
        } else {
            // Si no cambia la contraseña, no la actualiza
            $sql = "UPDATE usuarios SET nombre_usuario=?, usuario=?, rol=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssi', $nombre_usuario, $usuario, $rol, $id);
        }

        if ($stmt->execute()) {
            header("Location: index.php?success=1");
            exit;
        } else {
            $errors[] = "Error al actualizar el usuario: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
    <title>Editar Usuario</title>
</head>

<body class="dashAdm">
    <section class="module">
        <div>
            <h2>Editar Usuario</h2>

            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($usuario_data)): ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($usuario_data['id']) ?>">

                <table>
                    <tr>
                        <td><label for="nombre_usuario">Nombre:</label></td>
                        <td><input type="text" name="nombre_usuario" id="nombre_usuario"
                                   value="<?= htmlspecialchars($usuario_data['nombre_usuario']) ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="usuario">Correo:</label></td>
                        <td><input type="email" name="usuario" id="usuario"
                                   value="<?= htmlspecialchars($usuario_data['usuario']) ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="pass">Contraseña (opcional):</label></td>
                        <td><input type="password" name="pass" id="pass" placeholder="Deja vacío para no cambiarla"></td>
                    </tr>
                    <tr>
                        <td><label for="rol">Rol:</label></td>
                        <td>
                            <select name="rol" id="rol" required>
                                <option value="SuperAdmin" <?= $usuario_data['rol'] === 'SuperAdmin' ? 'selected' : '' ?>>SuperAdmin</option>
                                <option value="Administrador" <?= $usuario_data['rol'] === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                                <option value="Contratista" <?= $usuario_data['rol'] === 'Contratista' ? 'selected' : '' ?>>Contratista</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <button type="submit" name="guardar_cambios">Guardar Cambios</button>
            </form>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
