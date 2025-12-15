<?php
include_once('../../config/conection.php');
include_once('../../config/verificar_acceso.php');

verificar_rol('Administrador');

// Configurar cabeceras para exportar Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=Requisicion_" . date('Y-m-d') . ".xls.html");
header("Pragma: no-cache");
header("Expires: 0");

// Consulta de los datos (solo con cantidad > 0)
$sql = "SELECT
            t.id,
            t.elemento,
            e.elementos AS elemento_nombre,
            t.baja,
            t.fecha,
            t.funcionario,
            f.funcionario AS funcionario_nombre,
            t.id_instructor,
            d.nombre AS docente_nombre
        FROM traspaso t
        LEFT JOIN elementos e ON t.elemento = e.id
        LEFT JOIN funcionario f ON t.funcionario = f.id
        LEFT JOIN instructores d ON t.id_instructor = d.id
        ORDER BY t.fecha DESC";
$result = $conn->query($sql);

// Encabezado de la tabla
echo "<table border='1'>";
echo "<thead>
        <tr style='background-color:#f0a500; color:#fff;'>
            <th>Elemento</th>
            <th>Cantidad</th>
            <th>Fecha</th>
            <th>Funcionario</th>
            <th>Instructor</th>
        </tr>
      </thead>
      <tbody>";

// Filas de la tabla
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['elemento_nombre'] . "</td>
                <td>" . htmlspecialchars($row['baja']) . "</td>
                <td>" . $row['fecha'] . "</td>
                <td>" . $row['funcionario_nombre'] . "</td>
                <td>" . $row['docente_nombre'] . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3'>No hay requisición disponibles</td></tr>";
}

echo "</tbody></table>";

$conn->close();
exit;
?>
