<?php
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

$errors = [];
$success = '';

// Registrar baja
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Validaciones básicas y lectura segura del POST
    $elemento = isset($_POST['elemento']) ? intval($_POST['elemento']) : 0;
    $baja = isset($_POST['baja']) ? intval($_POST['baja']) : 0;

    if ($elemento <= 0) {
        $errors[] = "No se recibió un elemento válido. Valor recibido: " . htmlspecialchars($_POST['elemento'] ?? 'NULL');
    } elseif ($baja <= 0) {
        $errors[] = "La cantidad a dar de baja debe ser mayor a 0.";
    } else {
        // 2) Preparar consulta para comprobar existencia en inventario
        $sql_check = "SELECT cantidad FROM inventario WHERE elemento_id = ?";
        $stmt = $conn->prepare($sql_check);
        if (!$stmt) {
            $errors[] = "Error al preparar consulta: " . $conn->error;
        } else {
            $stmt->bind_param("i", $elemento);
            if (!$stmt->execute()) {
                $errors[] = "Error al ejecutar consulta: " . $stmt->error;
            } else {
                $res = $stmt->get_result();
                if (!$res || $res->num_rows === 0) {
                    // Aquí vemos exactamente qué id no encontró
                    $errors[] = "El elemento seleccionado (id = {$elemento}) no existe en inventario.";
                } else {
                    $row = $res->fetch_assoc();
                    $cantidad_actual = (int)$row['cantidad'];

                    if ($baja > $cantidad_actual) {
                        $errors[] = "No puedes dar de baja más de lo que hay en inventario (disponible: {$cantidad_actual}).";
                    } else {
                        // 3) Update inventario
                        $sql_update = "UPDATE inventario SET cantidad = cantidad - ? WHERE elemento_id = ?";
                        $stmt_up = $conn->prepare($sql_update);
                        if (!$stmt_up) {
                            $errors[] = "Error al actualizar: " . $conn->error;
                        } else {
                            $stmt_up->bind_param("ii", $baja, $elemento);
                            if (!$stmt_up->execute()) {
                                $errors[] = "Error al actualizar inventario: " . $stmt_up->error;
                            } else {
                                // 4) Insertar baja (asegúrate que la FK de 'bajas.elemento' corresponda al id que insertas)
                                $fecha = date('Y-m-d');
                                $sql_insert = "INSERT INTO bajas (elemento, baja, fecha) VALUES (?, ?, ?)";
                                $stmt_ins = $conn->prepare($sql_insert);
                                if (!$stmt_ins) {
                                    $errors[] = "Error al preparar INSERT: " . $conn->error;
                                } else {
                                    $stmt_ins->bind_param("iis", $elemento, $baja, $fecha);
                                    if (!$stmt_ins->execute()) {
                                        $errors[] = "Error al insertar baja: " . $stmt_ins->error;
                                    } else {
                                        $success = "✅ Baja registrada correctamente.";
                                    }
                                    $stmt_ins->close();
                                }
                            }
                            $stmt_up->close();
                        }
                    }
                }
                $res->free();
            }
            $stmt->close();
        }
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
                    WHERE cantidad > 0 
                    ORDER BY elementos";
$result_inventario = $conn->query($sql_inventario);

// Consultar historial de bajas
$sql_bajas = "SELECT 
                    b.id,
                    e.elementos AS nombre_elemento,
                    b.baja,
                    b.fecha
                FROM bajas b
                LEFT JOIN elementos e ON b.elemento = e.id
                ORDER BY b.fecha DESC";
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
                    <option value="<?= $row['elemento_id'] ?>">
                        <?= htmlspecialchars($row['elementos']) ?> (Disponible: <?= $row['cantidad'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Cantidad a dar de baja:</label>
            <input type="number" name="baja" min="1" required>

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
                                <td><?= htmlspecialchars($inv['nombre_elemento']) ?></td>
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

<div class="export-container">
    <form action="exportar_bajas.php" method="post">
        <button type="submit" class="export-btn">📦 Exportar a Excel</button>
    </form>
</div>
</body>
</html>
