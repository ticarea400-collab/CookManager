<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

$errors = [];
$success = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $success = "✅ Elemento ingresado con éxito.";
            break;
        case 'deleted':
            $success = "🗑️ Elemento eliminado correctamente.";
            break;
    }
}

//Consultar tipo
$sql_tipo = "SELECT * FROM tipo_elemento";
$result_tipo = $conn->query($sql_tipo);

//Consultar clase
$sql_clase = "SELECT * FROM clase";
$result_clase = $conn->query($sql_clase);

//Registrar elemento
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_elementos'])) {
    $elementos = trim($_POST['elementos']);
    $tipo_elemento = intval($_POST['tipo_elemento']);
    $clase = intval($_POST['clase']);

    if(empty($elementos) || empty($tipo_elemento) || empty($clase)) {
        $errors[] = "Todos los campos son obligatorios";
    } else {
        //Verificar si existe
        $sql_verify = "SELECT * FROM elementos WHERE elementos = '$elementos'";
        $result_verify = mysqli_query($conn, $sql_verify);

        if(mysqli_num_rows($result_verify) == 0) {
            $sql_insert = "INSERT INTO elementos (elementos, tipo_elemento, clase) VALUES ('$elementos' , '$tipo_elemento' , '$clase')";

            mysqli_query($conn, $sql_insert);
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=created");
            exit;
        } else {
            $errors[] = "El elemento ya esta registrado";
        }
    }
}

//Consultar elementos
$sql_elementos = "SELECT 
                        e.id,
                        e.elementos,
                        t.nombre_tipo_elemento AS tipo_elemento,
                        c.clase_elemento AS clase_elemento
                  FROM elementos e
                  LEFT JOIN tipo_elemento t 
                    ON e.tipo_elemento = t.id
                  LEFT JOIN clase c
                    ON e.clase = c.id
                  ORDER BY e.elementos ASC";
$result_elementos = $conn->query($sql_elementos);

$elementos = [];

if($sql_elementos && $result_elementos->num_rows > 0) {
    while($row = $result_elementos->fetch_assoc()){
        $elementos[] = $row;
    }

    $result_elementos->free();
}

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
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
    <title>Elementos</title>
</head>

<body class="dashAdm">
    <section class="menu" id="mainMenu">
        <?php include(ROOT_PATH . '/includes/menuHamb.php') ?>
    </section>

<section class="module">
    <div>
        <h2>Ingresar Elemento</h2>

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

        
        <form  method="POST" class="form-baja">
            <label>Nombre:</label>
            <input type="text" name="elementos" required>

            <label>Tipo</label>
            <select name="tipo_elemento" required class="baja_item">
                <option value="">-- Seleccione un tipo --</option>
                <?php while($row = $result_tipo->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre_tipo_elemento']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Clase:</label>
            <select name="clase" class="baja_item" required>
                <option value="">-- Seleccione clase --</option>
                <?php while($row = $result_clase->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['clase_elemento']) ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="btn-baja" name="crear_elementos">Agregar</button>

        </form>
        
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
                                    <td><?= htmlspecialchars($elemento['clase_elemento']) ?></td>
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
    </div>
</section>

<div class="export-container">
    <form action="exportar_elementos.php" method="post">
        <button type="submit" class="export-btn">📦 Exportar a Excel</button>
    </form>
</div>

</body>
</html>