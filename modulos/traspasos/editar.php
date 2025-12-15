<?php
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');
include_once('../../includes/return_back.php');

verificar_rol('Administrador');

$errors = [];
$success = '';
$traspaso_data = [];

/* =========================
   1. OBTENER TRASPASO
========================= */
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT * FROM traspaso WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $traspaso_data = $result->fetch_assoc();
    } else {
        $errors[] = "Traspaso no encontrado.";
    }
    $stmt->close();
} else {
    $errors[] = "ID no válido.";
}

/* =========================
   2. ACTUALIZAR TRASPASO
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_cambios'])) {

    $id = intval($_POST['id']);
    $elemento = intval($_POST['elemento']);
    $baja = intval($_POST['baja']);
    $fecha = $_POST['fecha'];
    $funcionario = intval($_POST['funcionario']);
    $id_instructor = intval($_POST['id_instructor']);

    if (
        empty($elemento) || empty($baja) || empty($fecha)
        || empty($funcionario) || empty($id_instructor)
    ) {
        $errors[] = "Todos los campos son obligatorios.";
    } else {

        $sql_update = "UPDATE traspaso 
                       SET elemento = ?, baja = ?, fecha = ?, funcionario = ?, id_instructor = ?
                       WHERE id = ?";

        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param(
            "iisiii",
            $elemento,
            $baja,
            $fecha,
            $funcionario,
            $id_instructor,
            $id
        );

        if ($stmt->execute()) {
            $success = "✅ Traspaso actualizado correctamente.";
        } else {
            $errors[] = "Error al actualizar el traspaso.";
        }
        $stmt->close();
    }
}

/* =========================
   3. DATOS PARA SELECTS
========================= */
$elementos = $conn->query("SELECT id, elementos FROM elementos");
$funcionarios = $conn->query("SELECT id, funcionario FROM funcionario");
$instructores = $conn->query("SELECT id, nombre FROM instructores");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
    <title>Editar traspaso</title>
</head>

<body class="dashAdm">
<button type="button" class="btn-back"
    onclick="window.location.href='<?= $_SESSION['return_to'] ?? 'index.php' ?>'">
    ⬅ Volver
</button>

<section class="module">
    <div>
        <h2>Editar Traspaso</h2>

        <?php if (!empty($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($traspaso_data)): ?>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?= $traspaso_data['id'] ?>">

            <table>
                <tr>
                    <td><label>Elemento:</label></td>
                    <td>
                        <select name="elemento" required>
                            <?php while ($row = $elementos->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>"
                                    <?= $row['id'] == $traspaso_data['elemento'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['elementos']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><label>Cantidad:</label></td>
                    <td>
                        <input type="number" name="baja" value="<?= $traspaso_data['baja'] ?>" required>
                    </td>
                </tr>

                <tr>
                    <td><label>Fecha:</label></td>
                    <td>
                        <input type="date" name="fecha" value="<?= $traspaso_data['fecha'] ?>" required>
                    </td>
                </tr>

                <tr>
                    <td><label>Funcionario:</label></td>
                    <td>
                        <select name="funcionario" required>
                            <?php while ($row = $funcionarios->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>"
                                    <?= $row['id'] == $traspaso_data['funcionario'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['funcionario']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><label>Instructor:</label></td>
                    <td>
                        <select name="id_instructor" required>
                            <?php while ($row = $instructores->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>"
                                    <?= $row['id'] == $traspaso_data['id_instructor'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <button type="submit" name="guardar_cambios">Guardar Cambios</button>
        </form>
        <?php endif; ?>

    </div>
</section>
</body>
</html>
