<?php
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('SuperAdmin', 'Administrador');

// Configurar cabeceras para exportar Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=Usuarios_" . date('Y-m-d') . ".xls.html");
header("Pragma: no-cache");
header("Expires: 0");

// Consulta de los datos (solo con cantidad > 0)
$sql = "SELECT nombre_usuario, usuario, rol
        FROM usuarios
        ORDER BY rol, nombre_usuario";
$result = $conn->query($sql);

// Encabezado de la tabla
echo "<table border='1'>";
echo "<thead>
        <tr style='background-color:#f0a500; color:#fff;'>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>rol</th>
        </tr>
      </thead>
      <tbody>";

// Filas de la tabla
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['nombre_usuario']) . "</td>
                <td>" . htmlspecialchars($row['usuario']) . "</td>
                <td>" . htmlspecialchars($row['rol']) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3'>No hay usuarios disponibles</td></tr>";
}

echo "</tbody></table>";

$conn->close();
exit;
?>
