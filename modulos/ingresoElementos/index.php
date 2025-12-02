<?php 
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

$errors = [];
$success = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $success = "✅ Usuario creado con éxito.";
            break;
        case 'deleted':
            $success = "🗑️ Usuario eliminado correctamente.";
            break;
    }
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

    <section class="modules">
        <div>
            <h2>Desde Ingreso De Elementos</h2>

            <form  method="POST" action="">
                <table>
                    <tbody>
                        <tr>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

    </section>

</body>
</html>