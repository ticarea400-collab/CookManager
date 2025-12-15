<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

$errors = [];
$success = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $success = "✅ Cantidades ingresadas con éxito.";
            break;
    }
}

//Consultar elementos
$sql_elementos = "SELECT * FROM elementos";
$result_elementos = $conn->query($sql_elementos);

// Consultar inventario
$sql_inventario = "SELECT 
                        i.id, 
                        i.elemento_id, 
                        e.elementos,
                        i.cantidad 
                    FROM inventario i
                    LEFT JOIN elementos e ON i.elemento_id = e.id
                    WHERE cantidad >= 0 
                    ORDER BY elementos";
$result_inventario = $conn->query($sql_inventario);

//Registrar ingreso de cantidad en inventario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $elemento = isset($_POST['elemento']) ? intval($_POST['elemento']) : 0;
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

    if ($elemento <= 0) {
        $errors[] = "No se recibió un elemento válido.";
    } elseif ($cantidad <= 0) {
        $errors[] = "La cantidad ingresada debe ser mayor a 0.";
    } else {
        // Verificar si existe en inventario
        $sql_check = "SELECT cantidad FROM inventario WHERE elemento_id = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("i", $elemento);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            $errors[] = "El elemento seleccionado (ID = {$elemento}) no existe en inventario.";
        } else {

            // ACTUALIZAR INVENTARIO
            $fecha = date('Y-m-d');
            $sql_update = "UPDATE inventario 
                           SET fecha_ingreso = ?, cantidad = cantidad + ?
                           WHERE elemento_id = ?";

            $stmt_up = $conn->prepare($sql_update);

            if (!$stmt_up) {
                $errors[] = "Error al preparar UPDATE: " . $conn->error;
            } else {
                $stmt_up->bind_param("sii", $fecha, $cantidad, $elemento);

                if (!$stmt_up->execute()) {
                    $errors[] = "Error al ejecutar UPDATE: " . $stmt_up->error;
                } else {
                    $success = "✅ Cantidad ingresada correctamente.";
                }
                $stmt_up->close();
            }
        }

        // SOLO CERRAR $stmt SI EXISTE
        if (isset($stmt) && $stmt instanceof mysqli_stmt) {
            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
    <title>Ingresar Elementos</title>
</head>

<body class="dashAdm">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

    <section class="module">
        <div>
            <h2>Ingresar cantidades de un elemento</h2>

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
                        <?php while($row = $result_inventario->fetch_assoc()): ?>
                        <option value="<?= $row['elemento_id'] ?>">
                            <?= htmlspecialchars($row['elementos']) ?> (Disponible: <?= $row['cantidad'] ?>)
                        </option>
                        <?php endwhile; ?>
                </select>

                <label>Cantidad:</label>
                <input type="number" name="cantidad">

                <button type="submit" class="btn-baja">Agregar</button>
            </form>
        </div>
    </section>

</body>
</html>