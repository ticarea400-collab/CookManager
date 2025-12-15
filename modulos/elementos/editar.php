<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');
include_once('../../includes/return_back.php');

verificar_rol('Administrador');

$errors = [];
$success = '';
$elemento_data = [];

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM elementos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $elemento_data = $result->fetch_assoc();
    } else {
        $errors[] = "Elemento no encontrado.";
    }

    $stmt->close();
} else {
    $errors[] = "No se proporcionó un ID válido.";
}

// Consultar tipo_elemento
$sql_tipo = "SELECT * FROM tipo_elemento";
$result_tipo = $conn->query($sql_tipo);

// Consultar clase
$sql_clase = "SELECT * FROM clase";
$result_clase = $conn->query($sql_clase);


if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(isset($_POST['guardar_cambios'])) {

        $id = intval($_POST['id']);
        $elemento = trim($_POST['elemento']);
        $tipo = intval($_POST['tipo']);
        $clase = intval($_POST['clase']);

        $sql_update = "UPDATE elementos
                        SET elementos = ?, tipo_elemento = ?, clase = ?
                        WHERE id = ?";

        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("siii", $elemento, $tipo, $clase, $id);
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
    <title>Editar Elemento</title>
</head>

<body class="dashAdm">
    <button type="button" class="btn-back"
        onclick="window.location.href='<?= $_SESSION['return_to'] ?? 'index.php' ?>'">
        ⬅ Volver
    </button>

    <section class="module">
        <div>
            <h2>Editar Elemento</h2>
            
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

            
            <?php if(!empty($elemento_data)): ?>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= htmlspecialchars($elemento_data['id']) ?>">
            
                <table>
                    <tr>
                        <td><label for="elemento">Elemento:</label></td>
                        <td><input type="text" name="elemento" id="elemento" value="<?= htmlspecialchars($elemento_data['elementos']) ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="tipo">Tipo:</label></td>
                        <td>
                            <select name="tipo" id="tipo">
                            <?php while($row = $result_tipo->fetch_assoc()): ?>
                                <option 
                                    value="<?= $row['id'] ?>"
                                    <?= $row['id'] == $elemento_data['tipo_elemento'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($row['nombre_tipo_elemento']) ?>
                                </option>
                            <?php endwhile; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="clase">Clase:</label></td>
                        <td>
                            <select name="clase" id="clase">
                                <?php while($row = $result_clase->fetch_assoc()): ?>
                                    <option 
                                        value="<?= $row['id'] ?>"
                                        <?= $row['id'] == $elemento_data['clase'] ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($row['clase_elemento']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <button type="submit" name="guardar_cambios">Guardar Cambios</button>

            </form>
            <?php endif;?>
        </div>
    </section>
    
</body>
</html>