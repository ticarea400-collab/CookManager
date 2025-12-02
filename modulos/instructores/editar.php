<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

$errors = [];
$success = '';
$instrucotr_data = [];

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT id, nombre, curso FROM docentes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('id', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $instructor_data = $result->fetch_assoc();
    } else {
        $errors[] = "Instructor no encontrado.";
    }

    $stmt->close();
} else {
    $errors[] = "No se proporciono un ID válido";
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(isset($_POST['guardar_cambios'])) {
        $id = intval($_POST['id']);
        $nombre = trim(($_POST['nombre']));
        $curso = trim($_POST['curso']);

        $sql_update = "UPDATE docentes
                        SET nombre = ?, curso = ?
                        WHERE id = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt->bind_param("ssi", $nombre, $curso, $id);
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
    <title>Editar instructor</title>
</head>

<body class="dashAdmin">
    <section class="module">
        <div>
            <h2>Editar Instructor</h2>

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

            <form method="POST" action="">
                <input type="hidden" name="id" value="">

                <table>
                    <tr>
                        <td><label for="nombre">Nombre:</label></td>
                        <td><input type="text" name="nombre" id="nombre" value=""></td>

                        <td><label for="curso">Curso:</label></td>
                        <td><input type="text" name="curso" id="curso" value=""></td>
                    </tr>
                </table>

                <button type="submit" name="guardar_Cambios">Guardar Cambios</button>
            </form>
    
        </div>
    </section>
</body>
</html>