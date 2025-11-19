<?php
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

$errors = [];
$success = '';

// Registrar baja
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $elemento = trim($_POST['elemento']);
    $baja = (int) $_POST['baja'];

    // Obtener cantidad actual del elemento
    $sql_check = "SELECT cantidad FROM inventario WHERE elementos = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("s", $elemento);
    $stmt->execute();
    $stmt->bind_result($cantidad_actual);
    $stmt->fetch();
    $stmt->close();

    if (!isset($cantidad_actual)) {
        $errors[] = "El elemento seleccionado no existe en inventario.";
    } elseif ($baja <= 0) {
        $errors[] = "La cantidad a dar de baja debe ser mayor a 0.";
    } elseif ($baja > $cantidad_actual) {
        $errors[] = "No puedes dar de baja más de lo que hay en inventario.";
    } else {
        // Actualizar inventario
        $sql_update = "UPDATE inventario SET cantidad = cantidad - ? WHERE elementos = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("is", $baja, $elemento);
        $stmt->execute();
        $stmt->close();

        // Registrar baja en tabla "bajas"
        $fecha = date('Y-m-d');
        $sql_insert = "INSERT INTO bajas (elemento, baja, fecha)
               VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("iis", $elemento, $baja, $fecha);
        $stmt->execute();

        $success = "✅ Baja registrada correctamente.";
    }
}

// Consultar inventario
$sql_inventario = "SELECT id, elementos, cantidad 
                    FROM inventario 
                    WHERE cantidad > 0 
                    ORDER BY elementos";
$result_inventario = $conn->query($sql_inventario);

// Consultar historial de bajas
$sql_bajas = "
    SELECT b.id, i.elementos, b.baja, b.fecha
    FROM bajas b
    JOIN inventario i ON b.elemento = i.id
    ORDER BY b.fecha DESC
";
$result_bajas = $conn->query($sql_bajas);

$bajas = [];

if($sql_bajas && $result_bajas->num_rows > 0) {
    while($row = $result_bajas->fetch_assoc()) {
        $bajas[] = $row;
    }

    $result_bajas->free();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
<title>Bajas de Inventario</title>
</head>

<body class="dashAdm">
<section class="menu" id="mainMenu">
    <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
</section>

<section class="module">
    <div>
        <h2>Bajas de Inventario</h2>

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
                    <option value="<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['elementos']) ?> (Disponible: <?= $row['cantidad'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Cantidad a dar de baja:</label>
            <input type="number" name="baja" min="1" required >

            <button type="submit" class="btn-baja">Registrar Baja</button>
        </form>

        <div>
            <h2>Historial de Bajas Recientes</h2>

            <div class="table-wrapper">
                <?php if(!empty($bajas)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Elemento</th>
                            <th>Cantidad Bajada</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-scroll">
                    <table class="table-body">
                        <tbody>
                            <?php foreach($bajas as $inv): ?>
                            <tr>
                                <td><?= htmlspecialchars($inv['elementos']) ?></td>
                                <td><?= htmlspecialchars($inv['baja']) ?></td>
                                <td><?= htmlspecialchars($inv['fecha']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p>No hay bajas registradas en la base de datos.</p>
                <?php endif; ?>                            
            </div>
        </div>
    </div>
</section>
</body>
</html>
