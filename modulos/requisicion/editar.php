<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

$errors = [];
$success = '';
$requisicion_data = [];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT 
                r.*, 
                d.nombre AS docente_nombre,
                f.funcionario AS funcionario_nombre
            FROM requisicion r
            LEFT JOIN docentes d ON r.id_docente = d.id
            LEFT JOIN funcionario f ON r.id_funcionario = f.id
            WHERE r.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $requisicion_data = $result->fetch_assoc();
    } else {
        $errors[] = "Requisicion no encontrada.";
    }

    $stmt->close();
} else {
    $errors[] = "No se proporcionó una requisicion válida.";
}

//Consultar instructor
$sql_instructor = "SELECT * FROM funcionario";
$result_instructor = $conn->query($sql_instructor);

//Consultar docente
$sql_docente = "SELECT * FROM docentes";
$result_docente = $conn->query($sql_docente);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['guardar_cambios'])) {

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

    $sql_update = "UPDATE requisicion
                    SET num_requisicion = ?, 
                        fecha_solicitud = ?, 
                        fecha_de_entrega = ?, 
                        grupo_encargado = ?, 
                        id_funcionario = ?, 
                        evento = ?, 
                        observaciones = ?, 
                        anulada = ?, 
                        id_docente = ?, 
                        practicantes = ?
                    WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("isssisssiii", $requisicion, $fecha_solicitud, $fecha_devolucion, $grupo_encargado, $funcionarios, $evento, $observaciones, $anulada, $docente, $practicantes, $id);
    $stmt->execute();

        $success = "✅ Cambios guardados correctamente.";
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
    <title>Editar Requisición</title>
</head>

<body class="dashAdm">
    <section class="module">
        <div>
            <h2>Editar Requisición</h2>

            <?php if(!empty($success)): ?>
                <div class="success"><?= $success ?></div>
            <?php endif; ?>

            <?php if(!empty($errors)): ?>
                <div class="errors">
                    <?php foreach($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        
            <?php if(!empty($requisicion_data)): ?>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= htmlspecialchars($requisicion_data['id']) ?>">

                <table>
                    <tr>
                        <td><label for="requisicion">N° requisición</label></td>
                        <td><input type="text" name="requisicion" id="requisicion" value="<?= htmlspecialchars($requisicion_data['num_requisicion']) ?>"></td>
                    </tr>

                    <tr>
                        <td><label for="docente">Docente</label></td>
                        <td>
                            <select name="docente" id="docente">
                                <option value="<?= $requisicion_data['id_docente'] ?>" selected>
                                    <?= htmlspecialchars($requisicion_data['docente_nombre']) ?>
                                </option>

                                <?php while($row = $result_docente->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>">
                                        <?= htmlspecialchars($row['nombre']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="fecha_solicitud">Fecha Solicitud</label></td>
                        <td><input type="text" name="fecha_solicitud" id="fecha_solicitud" value="<?= htmlspecialchars($requisicion_data['fecha_solicitud']) ?>"></td>
                    </tr>

                    <tr>
                        <td><label for="fecha_devolucion">Fecha Entrega</label></td>
                        <td><input type="text" name="fecha_devolucion" id="fecha_devolucion" value="<?= htmlspecialchars($requisicion_data['fecha_de_entrega']) ?>"></td>
                    </tr>

                    <tr>
                        <td><label for="grupo_encargado">Grupo Encargado</label></td>
                        <td><input type="text" name="grupo_encargado" id="grupo_encargado" value="<?= htmlspecialchars($requisicion_data['grupo_encargado']) ?>"></td>
                    </tr>

                    <tr>
                        <td><label for="practicantes">Practicantes</label></td>
                        <td><input type="text" name="practicantes" id="practicantes" value="<?= htmlspecialchars($requisicion_data['practicantes']) ?>"></td>
                    </tr>

                    <tr>
                        <td><label for="funcionarios">Funcionario</label></td>
                        <td>
                            <select name="funcionarios" id="funcionarios">
                                <option value="<?= $requisicion_data['id_funcionario'] ?>" selected>
                                    <?= htmlspecialchars($requisicion_data['funcionario_nombre']) ?>
                                </option>

                                <?php while($row = $result_instructor->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>">
                                        <?= htmlspecialchars($row['funcionario']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="evento">Evento</label></td>
                        <td><input type="text" name="evento" id="evento" value="<?= htmlspecialchars($requisicion_data['evento']) ?>"></td>
                    </tr>

                    <tr>
                        <td><label for="observaciones">Observaciones</label></td>
                        <td><input type="text" name="observaciones" id="observaciones" value="<?= htmlspecialchars($requisicion_data['observaciones']) ?>"></td>
                    </tr>

                    <tr>
                        <td><label for="anulada">Anulada</label></td>
                        <td><input type="text" name="anulada" id="anulada" value="<?= htmlspecialchars($requisicion_data['anulada']) ?>"></td>
                    </tr>
                </table>

                <button type="submit" name="guardar_cambios">Guardar Cambios</button>

            </form>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>