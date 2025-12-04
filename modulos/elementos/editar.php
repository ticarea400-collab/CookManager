<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

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

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(isset($_POST['guardar_cambios'])) {

        $id = intval($_POST['id']);
        $elemento = trim($_POST['elemento']);
        $tipo = trim($_POST['tipo']);
        $clase = trim($_POST['clase']);

        $sql_update = "UPDATE elementos
                        SET elementos = ?, tipo_elemento = ?, clase = ?
                        WHERE id = ?";

        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssi", $elemento, $tipo, $clase, $id);
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
                        <td><select name="tipo" id="tipo">
                            <option value="ASEO" <?= $elemento_data['tipo_elemento'] === 'ASEO' ? 'selected' : '' ?>> Aseo</option>
                            <option value="BATERIA COCINA" <?= $elemento_data['tipo_elemento'] === 'BATERIA COCINA' ? 'selected' : '' ?>> Bateria Cocina </option>
                            <option value="COCINA" <?= $elemento_data['tipo_elemento'] === 'COCINA' ? 'selected' : '' ?>> Cocina </option>
                            <option value="CONSUMO" <?= $elemento_data['tipo_elemento'] === 'CONSUMO' ? 'selected' : '' ?>> Consumo </option>
                            <option value="CRISTALERIA" <?= $elemento_data['tipo_elemento'] === 'CRISTALERIA' ? 'selected' : '' ?>> Cristaleria </option>
                            <option value="CUBIERTERA" <?= $elemento_data['tipo_elemento'] === 'CUBIERTERA' ? 'selected' : '' ?>> Cubiertera </option>
                            <option value="CUBIERTERIA" <?= $elemento_data['tipo_elemento'] === 'CUBIERTERIA' ? 'selected' : '' ?>> Cubierteria </option>
                            <option value="CUBIERTOS" <?= $elemento_data['tipo_elemento'] === 'CUBIERTOS' ? 'selected' : '' ?>> Cubiertos </option>
                            <option value="DEVOLUTIVO " <?= $elemento_data['tipo_elemento'] === 'DEVOLUTIVO' ? 'selected' : '' ?>> Devolutivo </option>
                            <option value="ELECTO PLATA" <?= $elemento_data['tipo_elemento'] === 'ELECTO PLATA' ? 'selected' : '' ?>> Electo plata</option>
                            <option value="EQUIPOS" <?= $elemento_data['tipo_elemento'] === 'EQUIPOS' ? 'selected' : '' ?>> Equipos </option>
                            <option value="PORCELANA" <?= $elemento_data['tipo_elemento'] === 'PORCELANA' ? 'selected' : '' ?>> Porcelana </option>
                            <option value="UTENSILIOS DE COCINA" <?= $elemento_data['tipo_elemento'] === 'UTENSILIOS DE COCINA' ? 'selected' : '' ?>> Utensilios de cocina </option>
                            <option value="UTENSILIOS DE MESA" <?= $elemento_data['tipo_elemento'] === 'UTENSILIOS DE MESA' ? 'selected' : '' ?>> Utensilios de mesa</option>
                            <option value="VAJILLA" <?= $elemento_data['tipo_elemento'] === 'VAJILLA' ? 'selected' : '' ?>> Vajilla </option>
                        </select></td>
                    </tr>
                    <tr>
                        <td><label for="clase">Clase:</label></td>
                        <td>
                            <select name="clase" id="clase">
                                <option value="ASEO" <?= $elemento_data['clase'] === 'ASEO' ? 'selected' : '' ?>> Aseo</option>
                                <option value="CONSUMO" <?= $elemento_data['clase'] === 'CONSUMO' ? 'selected' : '' ?>> Consumo</option>
                                <option value="DEVOLUTIVO" <?= $elemento_data['clase'] === 'DEVOLUTIVO' ? 'selected' : '' ?>> Devolutivo</option>
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