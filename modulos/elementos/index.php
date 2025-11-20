<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

$errors = [];
$success = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'deleted':
            $success = "🗑️Elemento eliminado correctamente.";
            break;
    }
}

//Consultar elementos
$sql_elementos = "SELECT 
                        e.id,
                        e.elementos,
                        t.nombre_tipo_elemento AS tipo_elemento,
                        e.clase
                FROM elementos e
                LEFT JOIN tipo_elemento t 
                ON e.tipo_elemento = t.id
                ORDER BY elementos";

$result_elementos = $conn->query($sql_elementos);

$elementos = [];

if($sql_elementos && $result_elementos->num_rows > 0) {
    while($row = $result_elementos->fetch_assoc()){
        $elementos[] = $row;
    }

    $result_elementos->free();
}

$conn->close();

//Eliminar elemento
if(isset($_GET['action']) && $_GET['action'] === 'eliminar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM elementos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if($stmt->execute()) {
        $success = "Elemento eliminado correctamente.";

        if($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=deleted");
            exit;
        }
    } else {
        $errors[] = "Error al eliminar el elemento " . $stmt->error;
    }
    $stmt->clone();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
    <title>Bajas</title>
</head>

<body class="dashAdm">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

<section class="module">
    <div>
        <h2>Elementos</h2>

        <!--  Barra de búsqueda -->
        <div class="search-bar">
            <input 
                type="text" 
                id="searchInput" 
                placeholder="🔍 Buscar elemento." 
                title="Escribe para filtrar los resultados">
        </div>

        <div class="table-wrapper">
            <?php if(!empty($elementos)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Elemento</th>
                            <th>Tipo</th>
                            <th>Clase</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-scroll">
                    <table class="table-body" id="inventoryTable">
                        <tbody>
                            <?php foreach($elementos as $elemento): ?>
                            <tr>
                                <td><?= htmlspecialchars($elemento['elementos']) ?></td>
                                <td><?= htmlspecialchars($elemento['tipo_elemento']) ?></td>
                                <td><?= htmlspecialchars($elemento['clase']) ?></td>
                                <td>
                                    <a href="editar.php?id=<?= $elemento['id'] ?>">
                                        <button type="button" class="edit">Editar</button>
                                    </a>
                                    <a href="?action=eliminar&id=<?= $elemento['id'] ?>"
                                        class="eliminate"
                                        onclick="return confirm('¿Seguro que deseas eliminar este elemento?');">
                                        <button type="button" class="eliminate">Eliminar</button>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No hay elementos registrados</p>
            <?php endif; ?>
        </div>        
    </div>
</section>

<div class="export-container">
    <form action="exportar_elementos.php" method="post">
        <button type="submit" class="export-btn">📦 Exportar a Excel</button>
    </form>
</div>

<script src="<?php echo BASE_URL; ?>/js/index.js"></script>

</body>
</html>