<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('SuperAdmin', 'Administrador');

$errors = [];
$success = '';

//Mostrar Datos
$funcionarios = [];
$sql_select = "SELECT funcionario, activo
                FROM funcionario
                ORDER BY funcionario";
$result = $conn->query($sql_select);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $funcionarios[] = $row;
    }
    $result->free();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
    <title>Funcionarios</title>
</head>

<body class="dashAdm">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

    <section class="module">
        <div>
            <h2>Funcionarios Registrados</h2>

            <?php if(!empty($errors)): ?>
                <div class="errors">
                    <p>🚨 Error(es):</p>
                    <ul>
                        <?php foreach($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif (!empty($success)): ?>
                <div>
                    <p> <?= htmlspecialchars($success) ?></p>
                </div>
            <?php endif; ?>

            <div class="table-wrapper">
                <?php if(!empty($funcionarios)): ?>
                <table class="table-head">
                    <thead>
                    <tr>
                        <th>Funcionario</th>
                        <th>Estado</th>
                    </tr>
                    </thead>
                </table>
                <div class="table-scroll">
                    <table class="table-body">
                        <tbody>
                            <?php foreach($funcionarios as $funcionario): ?>
                            <tr>
                                <td><?= htmlspecialchars($funcionario['funcionario']) ?></td>
                                <td><?= htmlspecialchars($funcionario['activo']) ?></td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p>No hay funcionarios registrados.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</body>
</html>