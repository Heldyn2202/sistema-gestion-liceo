<?php

// Ajustar rutas según tu estructura
include ('../../../app/config.php');
include ('../../../admin/layout/parte1.php');


// Obtener el periodo académico activo
$sql_gestion = "SELECT * FROM gestiones WHERE estado = 1";
$query_gestion = $pdo->prepare($sql_gestion);
$query_gestion->execute();
$gestion_activa = $query_gestion->fetch(PDO::FETCH_ASSOC);

if (!$gestion_activa) {
    $_SESSION['mensaje'] = "No hay un periodo académico activo configurado";
    header('Location: ' . APP_URL . '/admin');
    exit();
}

// Obtener todos los lapsos del periodo activo
$sql_lapsos = "SELECT * FROM lapsos WHERE id_gestion = :id_gestion ORDER BY fecha_inicio";
$query_lapsos = $pdo->prepare($sql_lapsos);
$query_lapsos->bindParam(':id_gestion', $gestion_activa['id_gestion']);
$query_lapsos->execute();
$lapsos = $query_lapsos->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <div class="content">
        <div class="container">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Lapsos Académicos</h1>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-success float-right" data-toggle="modal" data-target="#modal-create">
                                <i class="fas fa-plus"></i> Nuevo Lapso
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Periodo: <?= htmlspecialchars($gestion_activa['desde']) ?>-<?= htmlspecialchars($gestion_activa['hasta']) ?></h3>
                        </div>
                        <div class="card-body">
                            <table id="tabla_lapsos" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Nombre</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lapsos as $index => $lapso): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($lapso['nombre_lapso']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($lapso['fecha_inicio'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($lapso['fecha_fin'])) ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm edit-btn" 
                                                    data-id="<?= $lapso['id_lapso'] ?>"
                                                    data-nombre="<?= htmlspecialchars($lapso['nombre_lapso']) ?>"
                                                    data-inicio="<?= $lapso['fecha_inicio'] ?>"
                                                    data-fin="<?= $lapso['fecha_fin'] ?>">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear nuevo lapso -->
<div class="modal fade" id="modal-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-create" action="guardar_lapso.php" method="post">
                <div class="modal-header bg-success">
                    <h4 class="modal-title">Nuevo Lapso Académico</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_gestion" value="<?= $gestion_activa['id_gestion'] ?>">
                    <div class="form-group">
                        <label>Nombre del Lapso</label>
                        <input type="text" name="nombre_lapso" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar lapso -->
<div class="modal fade" id="modal-edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-edit" action="actualizar_lapso.php" method="post">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title">Editar Lapso Académico</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_lapso" id="edit_id">
                    <div class="form-group">
                        <label>Nombre del Lapso</label>
                        <input type="text" name="nombre_lapso" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="edit_inicio" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin" id="edit_fin" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../../admin/layout/parte2.php';
?>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#tabla_lapsos').DataTable({
        responsive: true,
        autoWidth: false,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        }
    });

    // Manejar clic en botón de editar
    $('.edit-btn').click(function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var inicio = $(this).data('inicio');
        var fin = $(this).data('fin');
        
        $('#edit_id').val(id);
        $('#edit_nombre').val(nombre);
        $('#edit_inicio').val(inicio);
        $('#edit_fin').val(fin);
        
        $('#modal-edit').modal('show');
    });

    // Validación de fechas en el formulario
    $('#form-create, #form-edit').submit(function(e) {
        var inicio = new Date($(this).find('input[name="fecha_inicio"]').val());
        var fin = new Date($(this).find('input[name="fecha_fin"]').val());
        
        if (fin < inicio) {
            alert('La fecha de fin no puede ser anterior a la fecha de inicio');
            e.preventDefault();
            return false;
        }
        return true;
    });
});
</script>