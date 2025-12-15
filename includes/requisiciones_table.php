<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>N° requisición</th>
                <th>Docente</th>
                <th>Fecha Solicitud</th>
                <th>Fecha Entrega</th>
                <th>Grupo Encargado</th>
                <th>Practicantes</th>
                <th>Funcionarios</th>
                <th>Evento</th>
                <th>Observaciones</th>
                <th>Anulada</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<div class="table-scroll">
    <table class="table-body" id="inventoryTable">
        <tbody>
            <?php foreach($requisicion as $req): ?>
            <tr>
                <td><?= $req['num_requisicion'] ?></td>
                <td><?= htmlspecialchars($req['instructor_nombre']) ?></td>
                <td><?= $req['fecha_solicitud'] ?></td>
                <td><?= $req['fecha_de_entrega'] ?></td>
                <td><?= $req['grupo_encargado'] ?></td>
                <td><?= $req['practicantes'] ?></td>
                <td><?= htmlspecialchars($req['funcionario_nombre']) ?></td>
                <td><?= $req['evento'] ?></td>
                <td><?= $req['observaciones'] ?></td>
                <td><?= $req['anulada'] ?></td>

                <td>
                    <?php if($modo === 'requisicion'): ?>
                        <a href="editar.php?id=<?= $req['id'] ?>">
                            <button class="edit">Editar</button>
                        </a>
                        <a href="?action=eliminar&id=<?= $req['id'] ?>"
                        onclick="return confirm('¿Eliminar?');">
                            <button class="eliminate">Eliminar</button>
                        </a>

                    <?php elseif($modo === 'devoluciones'): ?>
                        <a href="pendientes.php?id=<?= $req['id'] ?>">
                            <button class="edit">Añadir pendientes</button>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
