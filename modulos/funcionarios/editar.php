<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');
include_once('../../includes/return_back.php');

verificar_rol('Administrador');

$errors = [];
$success = '';
$funcionario_data = [];

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM funcionario WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $funcionario_data = $result->fetch_assoc();
    } else {
        $errors[] = "Elemento no encontrado.";
    }
    $stmt->close();
} else {
    $errors[] = "No se proporcionó un ID válido.";
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if(isset($_POST['guardar_cambios'])) {
        $id = intval($_POST['id']);
        $nombre = trim($_POST['nombre']);

        $sql_update = "UPDATE funcionario
                        SET funcionario = ?
                        WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("si", $nombre, $id);
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
    <title>Editar funcionario</title>
</head>
<body class="dashAdm">
    <button type="button" class="btn-back"
        onclick="window.location.href='<?= htmlspecialchars($_SESSION['return_to'] ?? '/CookManager/index.php');  ?>'">
        ⬅ Volver
    </button>
    
    <section class="module">
        <div>
            <h2>Editar funcionario</h2>

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

            <?php if(!empty($funcionario_data)): ?>
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($funcionario_data['id']) ?>">

                <table>
                    <tr>
                        <td><label for="nombre">Nombre:</label></td>
                        <td><input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($funcionario_data['funcionario']) ?>"></td>
                    </tr>
                </table>

                <button type="submit" name="guardar_cambios">Guardar Cambios</button>
            </form>
            <?php endif; ?>
        </div>
    </section>
    
</body>
</html>