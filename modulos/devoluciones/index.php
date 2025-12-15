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
    $instructor = intval($_POST['instructor']);
    $fecha_solicitud = trim($_POST['fecha_solicitud']);
    $fecha_devolucion = trim($_POST['fecha_devolucion']);
    $grupo_encargado = trim($_POST['grupo_encargado']);
    $practicantes = isset($_POST['practicantes']) ? intval($_POST['practicantes']) : 0;
    $funcionarios = intval($_POST['funcionarios']);
    $evento = trim($_POST['evento']);
    $observaciones = trim($_POST['observaciones']);
    $anulada = trim($_POST['anulada']);

    if(empty($requisicion) || empty($instructor) || empty($fecha_solicitud) || empty($fecha_devolucion) || empty($funcionarios) || empty($evento)) {
        $errors[] = "El campo de requisición, instructor, fechas, funcionarios y evento son obligatorios.";
    } else {

        // Verificar si existe
        $sql_verify = "SELECT * FROM requisicion WHERE num_requisicion = '$requisicion'";
        $result_verify = mysqli_query($conn, $sql_verify);

        if(mysqli_num_rows($result_verify) == 0 ) {

            $sql_insert = "
            INSERT INTO requisicion 
            (num_requisicion, id_instructor, fecha_solicitud, fecha_de_entrega, grupo_encargado, practicantes, id_funcionario, evento, observaciones, anulada)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql_insert);
            $stmt->bind_param(
                "iisssiisss",
                $requisicion,
                $instructor,
                $fecha_solicitud,
                $fecha_devolucion,
                $grupo_encargado,
                $practicantes,
                $funcionarios,
                $evento,
                $observaciones,
                $anulada
            );
            $stmt->execute();
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=created");
            exit;

        } else {
            $errors[] = "El número de requisición ya está registrado";
        }
    }
}

//Consultar instructor
$sql_funcionario = "SELECT * FROM funcionario";
$result_funcionario = $conn->query($sql_funcionario);

//Consultar docente
$sql_instructor = "SELECT * FROM instructores";
$result_instructor = $conn->query($sql_instructor);

//Consultar requisiciones
$sql_requisicion = " SELECT 
                            r.id,
                            r.num_requisicion,
                            r.id_instructor,
                            d.nombre AS instructor_nombre,
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
    <title>Devoluciones</title>
</head>

<body class="dashAdm">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

    <section class="module">
        <div>
            <h2>Devoluciones</h2>
            
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

            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="🔍 Buscar número requisición." title="Escribe para filtrar los resultados">
            </div>

            <?php 
                $modo = 'devoluciones';
                include(ROOT_PATH . '/includes/requisiciones_table.php');
            ?>    
        </div>

    </section>

</body>
</html>