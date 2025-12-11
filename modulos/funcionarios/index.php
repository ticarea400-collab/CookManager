<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol(['SuperAdmin', 'Administrador']);

$errors = [];
$success = '';

$rol = $_SESSION['rol'];

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $success = "✅ Funcionario creado con éxito.";
            break;
        case 'deleted':
            $success = "🗑️ Funcionario eliminado correctamente.";
            break;
    }
}

//Crear Funcionario
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_funcionario'])) {
    
    $nombre = trim($_POST['funcionario']);

    if(empty($nombre)) {
        $errors[] = "El campo es obligatorio";
    } else {

        $sql_insert = "INSERT INTO funcionario (funcionario) VALUES ('$nombre')";
        mysqli_query($conn, $sql_insert);
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=created");
        exit;
    }
}

//Mostrar Datos
$funcionarios = [];
$sql_select = "SELECT id, funcionario
                FROM funcionario
                ORDER BY funcionario";
$result = $conn->query($sql_select);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $funcionarios[] = $row;
    }
    $result->free();
}

//Eliminar funcionario
if(isset($_GET['action']) && $_GET['action'] === 'eliminar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM funcionario WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=deleted");
        exit; 
    } else {
        $errors[] = "Error al eliminar el funcionario: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
    <title>Funcionarios</title>
</head>

<body class="dashAdm">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

    <section class="module">
        <div>
            <h2>Ingresar Funcionario</h2>

            <?php if(!empty($errors)): ?>
                <div class="errors">
                    <ul>
                        <?php foreach($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif(!empty($success)): ?>
                <p class="success"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <form method="POST" class="form-baja">
                <label>Nombre del funcionario:</label>
                <input type="text" name="funcionario" required>

                <button type="submit" class="btn-baja" name="crear_funcionario">Agregar</button>
            </form>

            <div>
                <h2>Funcionarios Registrados</h2>
    
                <div class="table-wrapper">
                    <?php if(!empty($funcionarios)): ?>
                    <table class="table-head">
                        <thead>
                        <tr>
                            <th>Funcionario</th>
                            <?php if($rol === 'Administrador'): ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                    </table>
                    <div class="table-scroll">
                        <table class="table-body">
                            <tbody>
                                <?php foreach($funcionarios as $funcionario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($funcionario['funcionario']) ?></td>
    
                                    <?php if($rol === 'Administrador'): ?>
                                    <td>
                                        <a href="editar.php?id=<?= $funcionario['id'] ?>">
                                            <button type="button" class="edit">Editar</button> 
                                        </a>
                                        <a href="?action=eliminar&id=<?= $funcionario['id'] ?>"
                                            onclick="return confirm('¿Seguro que deseas eliminar esta requisición?');">
                                            <button type="button" class="eliminate">Eliminar</button>    
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <p>No hay funcionarios registrados.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</body>
</html>