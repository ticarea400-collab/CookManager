<?php
    include('itemMenu.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos.css">
</head>
<body>
    <div id="menuHeader">
        <h1>Menú</h1>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="iconHamb" id="hamburgerIcon">
            <path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/>
        </svg>
    </div>

    <div class="btonesMenu">
        <?php foreach($items as $item): ?>
        <button <?php if($item['active']): ?>class="active-menu-item"<?php endif; ?>>
            <a href="<?php echo $item['url']; ?>" class="menu-link"><?php echo $item["nombre"]; ?></a>
            <?php echo $item["icon"]; ?>
        </button>
        <?php endforeach;?>
    </div>

    <script src="<?php echo BASE_URL; ?>/js/index.js"></script>
</body>
</html>