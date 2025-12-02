<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

$errors = [];
$success = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $success = "✅ Instructor creado con éxito.";
            break;
        case 'deleted':
            $success = "🗑️ Instructor eliminado correctamente.";
            break;
    }
}

//Crear instructores
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_instructor'])) {
    //Obtener datos
    $nombre = trim($_POST['nombre']);
    $curso = trim($_POST['curso']);

    if(empty($nombre) || empty($curso)) {
        $errors[] = "Todos los campos son obligatorios";
    } else {
        $sql_verify = "SELECT * FROM docentes WHERE nombre = '$nombre'";
        $result_verify = mysqli_query($conn, $sql_verify);
        
        if(mysqli_num_rows($result_verify) == 0) {
            $sql_insert = "INSERT INTO docentes (nombre) VALUES ('$nombre')";
            mysqli_query($conn, $sql_insert);
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=created");
        } else {
            $errors[] = "El instructor ya esta registrado";
        }
    }
}

//Consultar instructores
$sql_docentes = "SELECT id, nombre, curso
                FROM docentes
                ORDER BY nombre";
$result_docentes = $conn->query($sql_docentes);

$docentes = [];

if($sql_docentes && $result_docentes->num_rows > 0) {
    while($row = $result_docentes->fetch_assoc()) {
        $docentes[] = $row;
    }
    $result_docentes->free();
}

//Eliminar instructor
if(isset($_GET['action']) && $_GET['action'] === 'eliminar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM docentes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if($stmt->execute()) {
        $success = "Elemento eliminado correctamente.";

        if($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=deleted");
            exit;
        }
    } else {
        $errors[] = "Error al eliminar el instructor " . $stmt->error; 
    }
    $stmt->close();
}
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
            <h2>Ingresar nuevo instructor</h2>

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
                <label>Nombre del instructor:</label>
                <input type="text" name="nombre" required>

                <label>Curso:</label>
                <input type="text" name="curso">

                <button type="submit" class="btn-baja" name="crear_instructor">Agregar</button>
            </form>

            <div>
                <h2>Instructores registrados</h2>
    
                <!--  Barra de búsqueda -->
                <div class="search-bar">
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="🔍 Buscar instructor." 
                        title="Escribe para filtrar los resultados">
                </div>

                <div class="table-wrapper">
                    <?php if(!empty($docentes)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Curso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="table-scroll">
                        <table class="table-body" id="inventoryTable">
                            <tbody>
                                <?php foreach($docentes as $docente):?>
                                <tr>
                                    <td><?= htmlspecialchars($docente['nombre']) ?></td>
                                    <td><?= htmlspecialchars($docente['curso']) ?></td>
                                    <td>
                                        <a href="editar.php?id=<?= $docente['id'] ?>">
                                            <button type="button" class="edit">Editar</button>
                                        </a>
                                        <a href="?action=eliminar&id=<?= $docente['id'] ?>"
                                            class="eliminate"
                                            onclick="return confirm('¿Seguro que deseas eliminar este instructor?');">
                                            <button type="button" class="eliminate">Eliminar</button>    
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <p>No hay instructores registrados</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>

<script src="<?php echo BASE_URL; ?>/js/index.js"></script>

</body>
</html>