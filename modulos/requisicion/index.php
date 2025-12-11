<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol(['Contratista', 'Administrador']);


$errors = [];
$success = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $success = "✅ Requisición creada con éxito.";
            break;
        case 'deleted':
            $success = "🗑️ Requisición eliminada correctamente.";
            break;
    }
}

//Crear requisición
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_requisicion'])) {

    $requisicion = intval($_POST['requisicion']);
    $docente = intval($_POST['docente']);
    $fecha_solicitud = trim($_POST['fecha_solicitud']);
    $fecha_devolucion = trim($_POST['fecha_devolucion']);
    $grupo_encargado = trim($_POST['grupo_encargado']);
    $practicantes = isset($_POST['practicantes']) ? intval($_POST['practicantes']) : 0;
    $funcionarios = intval($_POST['funcionarios']);
    $evento = trim($_POST['evento']);
    $observaciones = trim($_POST['observaciones']);
    $anulada = trim($_POST['anulada']);

    if(empty($requisicion) || empty($docente) || empty($fecha_solicitud) || empty($fecha_devolucion) || empty($funcionarios) || empty($evento)) {
        $errors[] = "El campo de requisición, docente, fechas, funcionarios y evento son obligatorios.";
    } else {

        // Verificar si existe
        $sql_verify = "SELECT * FROM requisicion WHERE num_requisicion = '$requisicion'";
        $result_verify = mysqli_query($conn, $sql_verify);

        if(mysqli_num_rows($result_verify) == 0 ) {

            $sql_insert = "INSERT INTO requisicion 
                (num_requisicion, id_docente, fecha_solicitud, fecha_de_entrega, grupo_encargado, practicantes, id_funcionario, evento, observaciones, anulada) 
                VALUES 
                ('$requisicion' , '$docente' , '$fecha_solicitud' , '$fecha_devolucion' , '$grupo_encargado' , '$practicantes' , '$funcionarios' , '$evento' , '$observaciones', $anulada)";
            
            mysqli_query($conn, $sql_insert);
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=created");
            exit;

        } else {
            $errors[] = "El número de requisición ya está registrado";
        }
    }
}

//Consultar instructor
$sql_instructor = "SELECT * FROM funcionario";
$result_instructor = $conn->query($sql_instructor);

//Consultar docente
$sql_docente = "SELECT * FROM instructores";
$result_docente = $conn->query($sql_docente);

//Consultar requisiciones
$sql_requisicion = " SELECT 
                            r.id,
                            r.num_requisicion,
                            r.id_instructor,
                            d.nombre AS docente_nombre,
                            r.fecha_solicitud,
                            r.fecha_de_entrega,
                            r.grupo_encargado,
                            r.practicantes,
                            r.id_funcionario,
                            f.funcionario AS funcionario_nombre,
                            r.evento,
                            r.observaciones,
                            r.anulada
                    FROM requisicion r
                    LEFT JOIN instructores d ON r.id_instructor = d.id
                    LEFT JOIN funcionario f ON r.id_funcionario = f.id
                    ORDER BY r.fecha_solicitud DESC";
$result_requisicion = $conn->query($sql_requisicion);
$requisicion = [];

if($result_requisicion && $result_requisicion->num_rows > 0) {
    while($row = $result_requisicion->fetch_assoc()) {
        $requisicion[] = $row;
    }
    $result_requisicion->free();
}

//Eliminar requisición
if(isset($_GET['action']) && $_GET['action'] === 'eliminar' && isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $sql = "DELETE FROM requisicion WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=deleted");
        exit;
    } else {
        $errors[] = "Error al eliminar la requisición: " . $stmt->error;
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
    <title>Requisición</title>
</head>

<body class="dashAdm">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

    <section class="module">
        <div>
            <h2>Ingresar Requisición</h2>

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
                <label>Número de requisición:</label>
                <input type="text" name="requisicion" required>

                <label>Instructores:</label>
                <select name="docente" required class="baja_item">
                    <option value="">-- Seleccione un instructores --</option>
                    <?php while($row = $result_docente->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Fecha de la solicitud:</label>
                <input type="date" name="fecha_solicitud" required>

                <label>Fecha de la devolución:</label>
                <input type="date" name="fecha_devolucion" required>

                <label>Grupo encargado:</label>
                <input type="text" name="grupo_encargado">

                <label>Practicantes:</label>
                <input type="checkbox" name="practicantes" value="1">

                <label>Funcionarios:</label>
                <select name="funcionarios" required class="baja_item">
                    <option value="">-- Seleccione un funcionario --</option>
                    <?php while($row = $result_instructor->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['funcionario']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Evento:</label>
                <input type="text" name="evento">

                <label>Observaciones:</label>
                <input type="text" name="observaciones"> 
                
                <label>Anulada:</label>
                <input type="text" name="anulada">

                <button type="submit" class="btn-baja" name="crear_requisicion">Agregar</button>
            </form>

            <div>
                <h2>Requisiciones</h2>

                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="🔍 Buscar número requisición." title="Escribe para filtrar los resultados">
                </div>
                
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>N° requisición</th>
                                <th>Docente</th>
                                <th>Fecha Solicitud</th>
                                <th>Fecha Entrega</th>
                                <th>Grupo Encargado</th>
                                <th>Practicantes</th>
                                <th>Funcionarios</th>
                                <th>Evento</th>
                                <th>Observaciones</th>
                                <th>Anulada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="table-scroll">
                        <table class="table-body" id="inventoryTable">
                            <tbody>
                                <?php foreach($requisicion as $req): ?>
                                <tr>
                                    <td><?= $req['num_requisicion'] ?></td>
                                    <td><?= htmlspecialchars($req['docente_nombre']) ?></td>
                                    <td><?= $req['fecha_solicitud'] ?></td>
                                    <td><?= $req['fecha_de_entrega'] ?></td>
                                    <td><?= $req['grupo_encargado'] ?></td>
                                    <td><?= $req['practicantes'] ?></td>
                                    <td><?= htmlspecialchars($req['funcionario_nombre']) ?></td>
                                    <td><?= $req['evento'] ?></td>
                                    <td><?= $req['observaciones'] ?></td>
                                    <td><?= $req['anulada'] ?></td>

                                    <td>
                                        <a href="editar.php?id=<?= $req['id'] ?>">
                                             <button type="button" class="edit">Editar</button>
                                        </a>
                                        <a href="?action=eliminar&id=<?= $req['id'] ?>" 
                                           onclick="return confirm('¿Seguro que deseas eliminar esta requisición?');">
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
        <form action="exportar_requisicion.php" method="post">
            <button type="submit" class="export-btn">📦 Exportar a Excel</button>
        </form>
    </div>

</body>
</html>
