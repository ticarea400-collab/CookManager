<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

$errors = [];
$success = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $success = "✅ Traspaso creado con éxito.";
            break;
        case 'deleted':
            $success = "🗑️ Traspaso eliminado correctamente.";
            break;
    }
}

//Crear traspaso
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_traspaso'])) {

    $elemento = intval($_POST['elemento']);
    $baja = intval($_POST['baja']);
    $fecha = trim($_POST['fecha']);
    $funcionario = intval($_POST['funcionario']);
    $id_instructor = intval($_POST['id_instructor']);

    if(empty($elemento) || empty($baja) || empty($fecha) || empty($funcionario) || empty($id_instructor)) {
        $errors[] = "Todos los campos son obligatorios";
    } else {

        // 1. Obtener cantidad actual del inventario
        $sql_check = "SELECT cantidad FROM inventario WHERE elemento_id = $elemento";
        $result_check = $conn->query($sql_check);
        $data = $result_check->fetch_assoc();

        $cantidad_actual = $data['cantidad'];

        // 2. Validar cantidad suficiente
        if ($baja > $cantidad_actual) {
            $errors[] = "La cantidad solicitada supera el inventario disponible.";
        } else {

            // 3. Restar inventario
            $nueva_cantidad = $cantidad_actual - $baja;

            $sql_update = "UPDATE inventario SET cantidad = $nueva_cantidad WHERE elemento_id = $elemento";
            $conn->query($sql_update);

            // 4. Insertar traspaso
            $sql_insert = "INSERT INTO traspaso (elemento, baja, fecha, funcionario, id_instructor)
                           VALUES ('$elemento', '$baja', '$fecha', '$funcionario', '$id_instructor')";
            
            mysqli_query($conn, $sql_insert);

            header("Location: " . $_SERVER['PHP_SELF'] . "?success=created");
            exit;
        }
    }
}


//Consultar elementos
$sql_elementos = "SELECT * FROM elementos 
                    ORDER BY elementos ASC";
$result_elementos = $conn->query($sql_elementos);

//Consultar funcionario
$sql_funcionario = "SELECT * FROM funcionario
                    ORDER BY funcionario ASC";
$result_funcionario = $conn->query($sql_funcionario);

//Consultar instructor
$sql_instructor = "SELECT * FROM instructores
                    ORDER BY nombre ASC";
$result_instructor = $conn->query($sql_instructor);

//Consultar traspasos
$sql_traspasos = "SELECT
                    t.id,
                    t.elemento,
                    e.elementos AS elemento_nombre,
                    t.baja,
                    t.fecha,
                    t.funcionario,
                    f.funcionario AS funcionario_nombre,
                    t.id_instructor,
                    d.nombre AS docente_nombre
                FROM traspaso t
                LEFT JOIN elementos e ON t.elemento = e.id
                LEFT JOIN funcionario f ON t.funcionario = f.id
                LEFT JOIN instructores d ON t.id_instructor = d.id
                ORDER BY t.fecha DESC";
$result_traspasos = $conn->query($sql_traspasos);
$traspasos = [];

if($result_traspasos && $result_traspasos->num_rows > 0) {
    while ($row = $result_traspasos->fetch_assoc()) {
        $traspasos[] = $row;
    }
    $result_traspasos->free();
}

//Eliminar traspaso
if (isset($_GET['action']) && $_GET['action'] === 'eliminar' && isset($_GET['id'])) {
    
    $id = intval($_GET['id']);
    $sql = "DELETE FROM traspaso WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=deleted");
        exit;
    } else {
        $errors[] = "Error al eliminar el traspaso: " . $stmt->error;
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
    <title>Traspaso</title>
</head>

<body class="dashAdm">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

    <section class="module">
        <div>
            <h2>Desde Traspasos</h2>

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
                <label>Elemento:</label>
                <select name="elemento" required class="baja_item">
                    <option value="">-- Seleccione un elemento --</option>
                    <?php while($row = $result_elementos->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['elementos']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Cantidad:</label>
                <input type="number" name="baja" id="baja" required>

                <label>Fecha:</label>
                <input type="date" name="fecha" id="fecha" required>

                <label>Funcionario:</label>
                <select name="funcionario" id="funcionario">
                    <option value="">-- Seleccione un funcionario --</option>
                    <?php while ($row = $result_funcionario->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['funcionario']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Instructor:</label>
                <select name="id_instructor" id="id_instructor">
                    <option value="">-- Seleccione un instructor --</option>
                    <?php while ($row = $result_instructor->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" class="btn-baja" name="crear_traspaso">Agregar</button>
            </form>

            <div>
                <h2>Traspasos</h2>

                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="🔍 Buscar traspaso por elemento." title="Escribe para filtrar los resultados">
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Elemento</th>
                                <th>Cantidad</th>
                                <th>Fecha</th>
                                <th>Funcionario</th>
                                <th>Instructor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="table-scroll">
                        <table class="table-body" id="inventoryTable">
                            <tbody>
                                <?php foreach($traspasos as $trasp): ?>
                                <tr>
                                    <td><?= htmlspecialchars($trasp['elemento_nombre']) ?></td>
                                    <td><?= $trasp['baja'] ?></td>
                                    <td><?= $trasp['fecha'] ?></td>
                                    <td><?= htmlspecialchars($trasp['funcionario_nombre']) ?></td>
                                    <td><?= htmlspecialchars($trasp['docente_nombre']) ?></td>
                                    <td>
                                        <a href="editar.php?id=<?= $trasp['id'] ?>">
                                            <button type="button" class="edit">Editar</button>
                                        </a>
                                        <a href="?action=eliminar&id=<?= $trasp['id'] ?>"
                                            onclick="return confirm('¿Seguro que deseas eliminar este traspaso?');">
                                            <button type="button" class="eliminate">Eliminar</button>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="export-container">
        <form action="exportar_traspasos.php" method="post">
            <button type="submit" class="export-btn">📦 Exportar a Excel</button>
        </form>
    </div>

</body>
</html> 