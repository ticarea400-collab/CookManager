<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('SuperAdmin', 'Administrador');

$errors = [];
$success = '';

//Mostrar Datos
$elementos = [];
$sql_select = "SELECT elementos, cantidad, fecha_de_ingreso
                FROM inventario
                ORDER BY elementos";
$result = $conn->query($sql_select);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $elementos[] = $row;
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
    <title>Inventario</title>
</head>

<body class="dashAdm">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

    <section class="module">
        <div>
            <h2>Inventario</h2>

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
                    <p><?= htmlspecialchars($success) ?></p>
                </div>
            <?php endif; ?>

            <!--  Barra de búsqueda -->
            <div class="search-bar">
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="🔍 Buscar elemento." 
                    title="Escribe para filtrar los resultados">
            </div>

            <div class="table-wrapper">
                <?php if(!empty($elementos)):?>
                    <table class="table-head">
                        <thead>
                            <tr>
                                <th>Elemento</th>
                                <th>Cantidad</th>
                                <th>Fecha de entrada</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="table-scroll">
                        <table class="table-body" id="inventoryTable">
                            <tbody>
                                <?php foreach($elementos as $elemento): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($elemento['elementos']) ?></td>
                                        <td><?= htmlspecialchars($elemento['cantidad']) ?></td>
                                        <td><?= htmlspecialchars($elemento['fecha_de_ingreso']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No hay elementos en el inventario.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <div class="export-container">
        <form action="exportar_inventario.php" method="post">
            <button type="submit" class="export-btn">📦 Exportar a Excel</button>
        </form>
    </div>

</body>
</html>