<?php
    $urlActual = $_SERVER['REQUEST_URI'];

    //Rutas de cada item del menu
    $paths = [
        'bajas' => '/modulos/bajas/index.php',
        'elementos' => '/modulos/elementos/index.php',
        'funcionarios' => '/modulos/funcionarios/index.php',
        'ingreso_elementos' => '/modulos/ingresoElementos/index.php',
        'instructores' => '/modulos/instructores/index.php',
        'inventario' => '/modulos/inventario/index.php',
        'requisicion' => '/modulos/requisicion/index.php',
        'traspasos' => '/modulos/traspasos/index.php',
        'devoluciones' => '/modulos/devoluciones/index.php'
    ];

    $bajas_active = (strpos($urlActual, $paths['bajas']) !== false);
    $elementos_active = (strpos($urlActual, $paths['elementos']) !== false);
    $funcionarios_active = (strpos($urlActual, $paths['funcionarios']) !== false);
    $ingreso_elementos_active = (strpos($urlActual, $paths['ingreso_elementos']) !== false);
    $instructores_active = (strpos($urlActual, $paths['instructores']) !== false);
    $inventario_active = (strpos($urlActual, $paths['inventario']) !== false);
    $requisicion_active =(strpos($urlActual, $paths['requisicion']) !== false);
    $traspasos_active = (strpos($urlActual, $paths['traspasos']) !== false);
    $devoluciones_active = (strpos($urlActual, $paths['devoluciones']) !== false);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div id="menuHeader">
        <h2>Menú</h2>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="icons" id="hamburgerIcon">
            <path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/>
        </svg>
    </div>

    <div class="btonesMenu">
        <button 
            <?php if($bajas_active): ?>class="active-menu-item"<?php endif; ?>
        >
            <a href="<?php echo BASE_URL; ?>/modulos/bajas/index.php">Bajas</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="icons">
                <path d="M256 0a256 256 0 1 0 0 512 256 256 0 1 0 0-512zM244.7 387.3l-104-104c-4.6-4.6-5.9-11.5-3.5-17.4s8.3-9.9 14.8-9.9l56 0 0-96c0-17.7 14.3-32 32-32l32 0c17.7 0 32 14.3 32 32l0 96 56 0c6.5 0 12.3 3.9 14.8 9.9s1.1 12.9-3.5 17.4l-104 104c-6.2 6.2-16.4 6.2-22.6 0z"/>
            </svg>
        </button>

        <button 
            <?php if($elementos_active): ?>class="active-menu-item"<?php endif; ?>
        >
            <a href="<?php echo BASE_URL; ?>/modulos/elementos/index.php">Elementos</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="icons">
                <path d="M256 0c11.2 0 21.7 5.9 27.4 15.5l96 160c5.9 9.9 6.1 22.2 .4 32.2S363.5 224 352 224l-192 0c-11.5 0-22.2-6.2-27.8-16.2s-5.5-22.3 .4-32.2l96-160C234.3 5.9 244.8 0 256 0zM128 272a112 112 0 1 1 0 224 112 112 0 1 1 0-224zm200 16l112 0c22.1 0 40 17.9 40 40l0 112c0 22.1-17.9 40-40 40l-112 0c-22.1 0-40-17.9-40-40l0-112c0-22.1 17.9-40 40-40z"/>
            </svg>
        </button>

        <button 
            <?php if($funcionarios_active): ?>class="active-menu-item"<?php endif; ?>
        >
            <a href="<?php echo BASE_URL; ?>/modulos/funcionarios/index.php">Funcionarios</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="icons">
                <path d="M399 384.2C376.9 345.8 335.4 320 288 320l-64 0c-47.4 0-88.9 25.8-111 64.2 35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/>
            </svg>
        </button>

        <button 
            <?php if($instructores_active): ?>class="active-menu-item"<?php endif; ?>
        >
            <a href="<?php echo BASE_URL; ?>/modulos/instructores/index.php">Instructores</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="icons">
                <path d="M192 80a56 56 0 1 0 0-112 56 56 0 1 0 0 112zM176 512l0-160c0-8.8 7.2-16 16-16s16 7.2 16 16l0 160c0 17.7 14.3 32 32 32s32-14.3 32-32l0-336 128 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-16 0 0-48 192 0 0 192-192 0 0-32-64 0 0 48c0 26.5 21.5 48 48 48l224 0c26.5 0 48-21.5 48-48l0-224c0-26.5-21.5-48-48-48L368 0c-26.5 0-48 21.5-48 48l0 64-122.7 0c-45.6 0-88.5 21.6-115.6 58.2L14.3 260.9c-10.5 14.2-7.6 34.2 6.6 44.8s34.2 7.6 44.8-6.6L112 236.7 112 512c0 17.7 14.3 32 32 32s32-14.3 32-32z"/>
            </svg>
        </button>

        <button 
            <?php if($ingreso_elementos_active): ?>class="active-menu-item"<?php endif; ?>
        >
            <a href="<?php echo BASE_URL; ?>/modulos/ingresoElementos/index.php">Ingreso de Elementos</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="icons">
                <path d="M448 205.8c-14.8 9.8-31.8 17.7-49.5 24-47 16.8-108.7 26.2-174.5 26.2S96.4 246.5 49.5 229.8c-17.6-6.3-34.7-14.2-49.5-24L0 288c0 44.2 100.3 80 224 80s224-35.8 224-80l0-82.2zm0-77.8l0-48C448 35.8 347.7 0 224 0S0 35.8 0 80l0 48c0 44.2 100.3 80 224 80s224-35.8 224-80zM398.5 389.8C351.6 406.5 289.9 416 224 416S96.4 406.5 49.5 389.8c-17.6-6.3-34.7-14.2-49.5-24L0 432c0 44.2 100.3 80 224 80s224-35.8 224-80l0-66.2c-14.8 9.8-31.8 17.7-49.5 24z"/>
            </svg>
        </button>

        <button 
            <?php if($inventario_active): ?>class="active-menu-item"<?php endif; ?>
        >
            <a href="<?php echo BASE_URL; ?>/modulos/inventario/index.php">Inventario</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="icons">
                <path d="M0 142.1L0 480c0 17.7 14.3 32 32 32s32-14.3 32-32l0-240c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32l0 240c0 17.7 14.3 32 32 32s32-14.3 32-32l0-337.9c0-27.5-17.6-52-43.8-60.7L303.2 5.1c-9.9-3.3-20.5-3.3-30.4 0L43.8 81.4C17.6 90.1 0 114.6 0 142.1zM464 256l-352 0 0 64 352 0 0-64zM112 416l352 0 0-64-352 0 0 64zm352 32l-352 0 0 64 352 0 0-64z"/>
            </svg>
        </button>

        <button 
            <?php if($requisicion_active): ?>class="active-menu-item"<?php endif; ?>
        >
            <a href="<?php echo BASE_URL; ?>/modulos/requisicion/index.php">Requisición</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="icons">
                <path d="M0 64C0 28.7 28.7 0 64 0L213.5 0c17 0 33.3 6.7 45.3 18.7L365.3 125.3c12 12 18.7 28.3 18.7 45.3L384 448c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 64zm208-5.5l0 93.5c0 13.3 10.7 24 24 24L325.5 176 208 58.5zM120 256c-13.3 0-24 10.7-24 24s10.7 24 24 24l144 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-144 0zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24l144 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-144 0z"/>
            </svg>
        </button>

        <button 
            <?php if($traspasos_active): ?>class="active-menu-item"<?php endif; ?>
        >
            <a href="<?php echo BASE_URL; ?>/modulos/traspasos/index.php">Traspasos</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="icons">
                <path d="M403.8 34.4c12-5 25.7-2.2 34.9 6.9l64 64c6 6 9.4 14.1 9.4 22.6s-3.4 16.6-9.4 22.6l-64 64c-9.2 9.2-22.9 11.9-34.9 6.9S384 204.9 384 192l0-32-32 0c-10.1 0-19.6 4.7-25.6 12.8l-32.4 43.2-40-53.3 21.2-28.3C293.3 110.2 321.8 96 352 96l32 0 0-32c0-12.9 7.8-24.6 19.8-29.6zM154 296l40 53.3-21.2 28.3C154.7 401.8 126.2 416 96 416l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32l64 0c10.1 0 19.6-4.7 25.6-12.8L154 296zM438.6 470.6c-9.2 9.2-22.9 11.9-34.9 6.9S384 460.9 384 448l0-32-32 0c-30.2 0-58.7-14.2-76.8-38.4L121.6 172.8c-6-8.1-15.5-12.8-25.6-12.8l-64 0c-17.7 0-32-14.3-32-32S14.3 96 32 96l64 0c30.2 0 58.7 14.2 76.8 38.4L326.4 339.2c6 8.1 15.5 12.8 25.6 12.8l32 0 0-32c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l64 64c6 6 9.4 14.1 9.4 22.6s-3.4 16.6-9.4 22.6l-64 64z"/>
            </svg>
        </button>

        <button 
            <?php if($devoluciones_active): ?>class="active-menu-item"<?php endif; ?>
        >
            <a href="<?php echo BASE_URL; ?>/modulos/devoluciones/index.php">Devoluciones</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="icons">
                <path d="M65.9 228.5c13.3-93 93.4-164.5 190.1-164.5 53 0 101 21.5 135.8 56.2 .2 .2 .4 .4 .6 .6l7.6 7.2-47.9 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-128c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 53.4-11.3-10.7C390.5 28.6 326.5 0 256 0 127 0 20.3 95.4 2.6 219.5 .1 237 12.2 253.2 29.7 255.7s33.7-9.7 36.2-27.1zm443.5 64c2.5-17.5-9.7-33.7-27.1-36.2s-33.7 9.7-36.2 27.1c-13.3 93-93.4 164.5-190.1 164.5-53 0-101-21.5-135.8-56.2-.2-.2-.4-.4-.6-.6l-7.6-7.2 47.9 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 320c-8.5 0-16.7 3.4-22.7 9.5S-.1 343.7 0 352.3l1 127c.1 17.7 14.6 31.9 32.3 31.7S65.2 496.4 65 478.7l-.4-51.5 10.7 10.1c46.3 46.1 110.2 74.7 180.7 74.7 129 0 235.7-95.4 253.4-219.5z"/>
            </svg>
        </button>
        
        <button>
            <a href="">Salir</a>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="icons">
                <path d="M160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0zM502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/>
            </svg>
        </button>
    </div>
</body>
</html>