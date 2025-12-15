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
$sql = " SELECT 
                r.id,
                r.num_requisicion,
                r.id_instructor,
                d.nombre AS instructor_nombre,
                r.fecha_solicitud,
                r.fecha_de_entrega,
                r.grupo_encargado,
                r.practicantes,
                r.id_funcionario,
                f.funcionario AS funcionario_nombre,
                r.evento,
                r.observaciones,
                r.anulada
        FROM requisicion r
        LEFT JOIN instructores d ON r.id_instructor = d.id
        LEFT JOIN funcionario f ON r.id_funcionario = f.id
        ORDER BY r.fecha_solicitud DESC";
$result = $conn->query($sql);

// Encabezado de la tabla
echo "<table border='1'>";
echo "<thead>
        <tr style='background-color:#f0a500; color:#fff;'>
            <th>N° requisición</th>
            <th>Instructor</th>
            <th>Fecha Solicitud</th>
            <th>Fecha Entrega</th>
            <th>Grupo Encargado</th>
            <th>Practicantes</th>
            <th>Funcionarios</th>
            <th>Evento</th>
            <th>Observaciones</th>
            <th>Anulada</th>
        </tr>
      </thead>
      <tbody>";

// Filas de la tabla
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['num_requisicion'] . "</td>
                <td>" . htmlspecialchars($row['instructor_nombre']) . "</td>
                <td>" . $row['fecha_solicitud'] . "</td>
                <td>" . $row['fecha_de_entrega'] . "</td>
                <td>" . $row['grupo_encargado'] . "</td>
                <td>" . $row['practicantes'] . "</td>
                <td>" . htmlspecialchars($row['funcionario_nombre']) . "</td>
                <td>" . $row['evento'] . "</td>
                <td>" . $row['observaciones'] . "</td>
                <td>" . $row['anulada'] . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3'>No hay requisición disponibles</td></tr>";
}

echo "</tbody></table>";

$conn->close();
exit;
?>
