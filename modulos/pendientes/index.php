<?php
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol(['Administrador', 'Contratista']);

$errors = [];
$success = '';

// ===============================
// REGISTRAR DEVOLUCIÓN
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['devolver'])) {

    $id_pendiente = intval($_POST['id_pendiente']);
    $cantidad_devuelta = intval($_POST['cantidad_devuelta']);

    if ($cantidad_devuelta <= 0) {
        $errors[] = "La cantidad debe ser mayor a 0.";
    } else {

        // Obtener pendiente actual
        $sql = "SELECT cantidad FROM pendientes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_pendiente);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            $errors[] = "Pendiente no encontrado.";
        } else {
            $data = $res->fetch_assoc();
            $cantidad_actual = $data['cantidad'];

            if ($cantidad_devuelta > $cantidad_actual) {
                $errors[] = "No puedes devolver más de lo pendiente.";
            } else {
                $nueva_cantidad = $cantidad_actual - $cantidad_devuelta;

                if ($nueva_cantidad == 0) {
                    // Eliminar pendiente si queda en 0
                    $sql_del = "DELETE FROM pendientes WHERE id = ?";
                    $stmt_del = $conn->prepare($sql_del);
                    $stmt_del->bind_param("i", $id_pendiente);
                    $stmt_del->execute();
                } else {
                    // Actualizar cantidad pendiente
                    $sql_up = "UPDATE pendientes SET cantidad = ? WHERE id = ?";
                    $stmt_up = $conn->prepare($sql_up);
                    $stmt_up->bind_param("ii", $nueva_cantidad, $id_pendiente);
                    $stmt_up->execute();
                }

                $success = "✅ Devolución registrada correctamente.";
            }
        }
    }
}

// ===============================
// CONSULTAR PENDIENTES GENERALES
// ===============================
$sql_pendientes = "
    SELECT 
        p.id,
        p.cantidad,
        p.fecha,
        r.num_requisicion,
        e.elementos AS elemento_nombre
    FROM pendientes p
    INNER JOIN requisicion r ON p.id_requisicion = r.id
    INNER JOIN elementos e ON p.id_elemento = e.id
    WHERE p.cantidad > 0
    ORDER BY r.num_requisicion ASC, p.fecha DESC
";

$result = $conn->query($sql_pendientes);
$pendientes = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pendientes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pendientes Generales</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
</head>

<body class="dashAdm">
<section class="menu" id="mainMenu">
    <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
</section>

<section class="module">
    <div>
        <h2>Pendientes Generales</h2>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>N° Requisición</th>
                        <th>Elemento</th>
                        <th>Cantidad Pendiente</th>
                        <th>Fecha</th>
                        <th>Devolver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendientes)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">
                                No hay pendientes registrados
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendientes as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['num_requisicion']) ?></td>
                                <td><?= htmlspecialchars($p['elemento_nombre']) ?></td>
                                <td><?= $p['cantidad'] ?></td>
                                <td><?= $p['fecha'] ?></td>
                                <td>
                                    <form method="POST" class="pending">
                                        <input type="hidden" name="id_pendiente" value="<?= $p['id'] ?>">
                                        <input 
                                            type="number" 
                                            name="cantidad_devuelta" 
                                            min="1" 
                                            max="<?= $p['cantidad'] ?>" 
                                            required
                                            class="return"
                                        >
                                        <button type="submit" name="devolver" class="btn-baja">
                                            ✔
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>

</body>
</html>
