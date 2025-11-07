<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('SuperAdmin');

$errors = [];
$success = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $success = "✅ Usuario creado con éxito.";
            break;
        case 'deleted':
            $success = "🗑️ Usuario eliminado correctamente.";
            break;
    }
}


if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    //Obtener datos
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $usuario = trim($_POST['usuario']);
    $pass = $_POST['pass'];
    $rol = $_POST['rol'];

    if(empty($nombre_usuario) || empty($usuario) || empty($pass) || empty($rol)) {
        $errors[] = "Todos los campos son obligatorios";
    } elseif (!filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo electrónico no es válido.";
    } elseif (!in_array($rol, ['SuperAdmin', 'Administrador', 'Contratista'])){
        $errors[] = "El rol seleccionado no es válido.";
    }

    if(empty($errors)) {
        $hash_pass = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre_usuario, usuario, pass, rol) VALUES (?, ?, ?, ?)";

        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('ssss', $nombre_usuario, $usuario, $hash_pass, $rol);

            if($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=created");
                exit;
            } else {
                if ($conn->errno == 1062) {
                    $errors[] = "El usuario/correo ya existe.";
                } else {
                    $errors[] = "Error al crear el usuario: " . $stmt->error;
                }
            }
            $stmt->close();
        } else {
            $errors[] = "Error al preparar la consulta de inserción: " . $conn->error;
        }
    }
}

//Eliminar usuario con GET
if (isset($_GET['action']) && $_GET['action'] === 'eliminar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id === $_SESSION['user_id']) {
        $errors[] = "No puedes eliminar tu propio usuario.";
    } else {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            $success = "Usuario eliminado correctamente.";
    
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=deleted");
                exit;
            }
        } else {
            $errors[] = "Error al eliminar el usuario: " . $stmt->error;
        }

        $stmt->close();
    }
}


//Mostrar Datos
$usuarios = [];
$sql_select = "SELECT id, nombre_usuario, usuario, rol 
                FROM usuarios
                ORDER BY rol, nombre_usuario";
$result = $conn->query($sql_select);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    $result->free();
}

$conn->close();

$roles_disponibles = ['SuperAdmin', 'Administrador', 'Contratista'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
    <title>Bajas</title>
</head>

<body class="dashAdm">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

    <section class="module">
        <div>
            <h2>Crear Usuario</h2>

            <?php if(!empty($errors)): ?>
                <div class="errors">
                    <p>🚨 Error(es):</p>
                    <ul>
                        <?php foreach($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif (!empty($success)): ?>
                <div>
                    <p> <?= htmlspecialchars($success) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <table>
                    <tbody>
                        <tr>
                            <td><label for="nombre_usuario">Nombre:</label></td>
                            <td><input type="text" id="nombre_usuario" name="nombre_usuario" value="<?= htmlspecialchars($nombre_usuario ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="usuario">Correo:</label></td>
                            <td><input type="email" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario ?? '') ?>" required></td>
                        </tr>
                        <tr>
                            <td><label for="pass">Contraseña:</label></td>
                            <td><input type="password" id="pass" name="pass" required></td>
                        </tr>
                        <tr>
                            <td><label for="rol">Rol:</label></td>
                            <td>
                                <select name="rol" id="rol" required>
                                    <option value="">--Seleccione un Rol</option>
                                    <?php foreach($roles_disponibles as $r): ?>
                                        <option value="<?= $r ?>" <?= (isset($rol) && $rol === $r) ? 'selected' : ''?>>
                                            <?= htmlspecialchars($r) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" name="crear_usuario">Crear Usuario</button>
            </form>
        </div>
    </section>

    <section class="module">
    <div>
        <h2>Usuarios Registrados (<?= count($usuarios) ?>)</h2>
        
        <div class="table-scroll">
            <?php if(!empty($usuarios)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($usuarios as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['nombre_usuario'])?></td>
                        <td><?= htmlspecialchars($user['usuario']) ?></td>
                        <td><?= htmlspecialchars($user['rol']) ?></td>
                        <td>
                            <a href="editar.php?id=<?= $user['id']?>">
                                <button type="button" class="edit">Editar</button>
                            </a> 
                            <a href="?action=eliminar&id=<?= $user['id'] ?>" 
                            class="eliminate" 
                            onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                <button type="button" class="eliminate">Eliminar</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No hay usuarios registrados en la base de datos.</p>
            <?php endif; ?>
        </div>
    </div>
    </section>

    <script>
        if (window.location.search.includes('success=')) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>

</body>
</html>