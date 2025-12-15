<?php
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');
include_once('../../includes/return_back.php');

verificar_rol(['Administrador', 'Contratista']);

$errors = [];
$success = '';

/* ===============================
   1. Validar requisición
================================ */
if (!isset($_GET['id'])) {
    die("ID de requisición no válido");
}

$id_requisicion = intval($_GET['id']);

/* Obtener datos de la requisición */
$sql_req = "SELECT num_requisicion FROM requisicion WHERE id = ?";
$stmt = $conn->prepare($sql_req);
$stmt->bind_param("i", $id_requisicion);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Requisición no encontrada");
}

$requisicion = $result->fetch_assoc();
$stmt->close();

/* ===============================
   2. Guardar pendiente
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_pendiente'])) {

    $elemento = intval($_POST['elemento']);
    $cantidad = intval($_POST['cantidad']);

    if ($elemento <= 0 || $cantidad <= 0) {
        $errors[] = "Elemento y cantidad deben ser válidos.";
    } else {

        $sql_insert = "INSERT INTO pendientes (id_requisicion, id_elemento, cantidad)
                       VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("iii", $id_requisicion, $elemento, $cantidad);

        if ($stmt->execute()) {
            $success = "Pendiente agregado correctamente.";
        } else {
            $errors[] = "Error al guardar pendiente.";
        }

        $stmt->close();
    }
}

/* ===============================
   3. Consultar elementos
================================ */
$sql_elementos = "SELECT id, elementos FROM elementos ORDER BY elementos";
$result_elementos = $conn->query($sql_elementos);

/* ===============================
   4. Consultar pendientes existentes
================================ */
$sql_pendientes = "
    SELECT 
        p.id,
        e.elementos,
        p.cantidad,
        p.fecha
    FROM pendientes p
    INNER JOIN elementos e ON p.id_elemento = e.id
    WHERE p.id_requisicion = ?
    ORDER BY p.fecha DESC
";
$stmt = $conn->prepare($sql_pendientes);
$stmt->bind_param("i", $id_requisicion);
$stmt->execute();
$result_pendientes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Pendientes</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
</head>

<body class="dashAdm">
<button type="button" class="btn-back"
    onclick="window.location.href='<?= $_SESSION['return_to'] ?? 'index.php' ?>'">
    ⬅ Volver
</button>

<section class="module">
<div>

    <h2>Añadir Pendientes</h2>

    <p><strong>N° Requisición:</strong> <?= htmlspecialchars($requisicion['num_requisicion']) ?></p>

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

    <form method="POST" class="form-baja">

        <label>Elemento:</label>
        <select name="elemento" required>
            <option value="">-- Seleccione un elemento --</option>
            <?php while ($el = $result_elementos->fetch_assoc()): ?>
                <option value="<?= $el['id'] ?>">
                    <?= htmlspecialchars($el['elementos']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Cantidad pendiente:</label>
        <input type="number" name="cantidad" min="1" required>

        <button type="submit" name="agregar_pendiente" class="btn-baja">
            ➕ Agregar pendiente
        </button>

    </form>

    <!-- ================= TABLA PENDIENTES ================= -->
    <h2>Pendientes de esta requisición</h2>

    <?php if ($result_pendientes->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Elemento</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($p = $result_pendientes->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($p['elementos']) ?></td>
                    <td><?= $p['cantidad'] ?></td>
                    <td><?= $p['fecha'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay pendientes registrados.</p>
    <?php endif; ?>

</div>
</section>
</body>
</html>

<?php $stmt->close(); ?>
