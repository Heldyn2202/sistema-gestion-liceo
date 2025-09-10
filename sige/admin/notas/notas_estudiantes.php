<?php 
define('BASE_PATH', dirname(__DIR__, 2)); // 2 niveles arriba de /app/
// Resultado: C:\xampp\htdocs\proyectonuevo\sige
include('../../app/config.php');
include('../../admin/layout/parte1.php');


// Obtener el periodo académico activo
$sql_gestion = "SELECT * FROM gestiones WHERE estado = 1 LIMIT 1";
$query_gestion = $pdo->prepare($sql_gestion);
$query_gestion->execute();
$gestion_activa = $query_gestion->fetch(PDO::FETCH_ASSOC);

// Obtener los lapsos del periodo activo
$sql_lapsos = "SELECT * FROM lapsos WHERE id_gestion = :id_gestion ORDER BY fecha_inicio";
$query_lapsos = $pdo->prepare($sql_lapsos);
$query_lapsos->bindParam(':id_gestion', $gestion_activa['id_gestion']);
$query_lapsos->execute();
$lapsos = $query_lapsos->fetchAll(PDO::FETCH_ASSOC);

// Variables de filtro
$id_lapso_filtro = isset($_GET['lapso']) ? $_GET['lapso'] : null;
$id_grado_filtro = isset($_GET['grado']) ? $_GET['grado'] : null;
$id_seccion_filtro = isset($_GET['seccion']) ? $_GET['seccion'] : null;

// Obtener todas las materias activas
$sql_materias = "SELECT * FROM materias WHERE estado = 1";
$query_materias = $pdo->prepare($sql_materias);
$query_materias->execute();
$materias = $query_materias->fetchAll(PDO::FETCH_ASSOC);

// Obtener estudiantes según filtros - CONSULTA MODIFICADA
$sql_estudiantes = "SELECT e.*, i.grado as id_grado, g.grado as nombre_grado, i.nombre_seccion, i.id_seccion
                    FROM estudiantes e
                    JOIN inscripciones i ON e.id_estudiante = i.id_estudiante
                    JOIN grados g ON i.grado = g.id_grado
                    WHERE i.id_gestion = :id_gestion";

if ($id_grado_filtro) {
    $sql_estudiantes .= " AND i.grado = :grado";
}
if ($id_seccion_filtro) {
    $sql_estudiantes .= " AND i.id_seccion = :seccion";
}

$query_estudiantes = $pdo->prepare($sql_estudiantes);
$query_estudiantes->bindParam(':id_gestion', $gestion_activa['id_gestion']);

if ($id_grado_filtro) {
    $query_estudiantes->bindParam(':grado', $id_grado_filtro);
}
if ($id_seccion_filtro) {
    $query_estudiantes->bindParam(':seccion', $id_seccion_filtro);
}

$query_estudiantes->execute();
$estudiantes = $query_estudiantes->fetchAll(PDO::FETCH_ASSOC);

// Obtener grados y secciones para los filtros
$sql_grados = "SELECT * FROM grados WHERE estado = 1";
$query_grados = $pdo->prepare($sql_grados);
$query_grados->execute();
$grados = $query_grados->fetchAll(PDO::FETCH_ASSOC);

$sql_secciones = "SELECT * FROM secciones WHERE id_gestion = :id_gestion AND estado = 1";
$query_secciones = $pdo->prepare($sql_secciones);
$query_secciones->bindParam(':id_gestion', $gestion_activa['id_gestion']);
$query_secciones->execute();
$secciones = $query_secciones->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Gestión de Notas Académicas</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Filtros de Búsqueda</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="get" action="">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Lapso Académico</label>
                                            <select name="lapso" class="form-control select2">
                                                <option value="">Todos los Lapsos</option>
                                                <?php foreach ($lapsos as $lapso): ?>
                                                    <option value="<?= $lapso['id_lapso'] ?>" <?= ($id_lapso_filtro == $lapso['id_lapso']) ? 'selected' : '' ?>>
                                                        <?= $lapso['nombre_lapso'] ?> (<?= date('d/m/Y', strtotime($lapso['fecha_inicio'])) ?> - <?= date('d/m/Y', strtotime($lapso['fecha_fin'])) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Grado</label>
                                            <select name="grado" class="form-control select2" id="grado">
                                                <option value="">Todos los Grados</option>
                                                <?php foreach ($grados as $grado): ?>
                                                    <option value="<?= $grado['id_grado'] ?>" <?= ($id_grado_filtro == $grado['id_grado']) ? 'selected' : '' ?>>
                                                        <?= $grado['grado'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Sección</label>
                                            <select name="seccion" class="form-control select2" id="seccion">
                                                <option value="">Todas las Secciones</option>
                                                <?php foreach ($secciones as $seccion): ?>
                                                    <option value="<?= $seccion['id_seccion'] ?>" <?= ($id_seccion_filtro == $seccion['id_seccion']) ? 'selected' : '' ?>>
                                                        <?= $seccion['nombre_seccion'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top: 32px">
                                            <button type="submit" class="btn btn-primary">Filtrar</button>
                                            <a href="notas_estudiantes.php" class="btn btn-default">Limpiar</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Listado de Estudiantes</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="tabla_notas" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Estudiante</th>
                                        <th>Cedula</th>
                                        <th>Grado</th>
                                        <th>Sección</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($estudiantes as $index => $estudiante): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($estudiante['nombres'] . ' ' . htmlspecialchars($estudiante['apellidos'])) ?></td>
                                            <td><?= htmlspecialchars($estudiante['cedula']) ?></td>
                                            <td><?= htmlspecialchars($estudiante['nombre_grado']) ?></td>
                                            <td><?= htmlspecialchars($estudiante['nombre_seccion']) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="editar_notas.php?id_estudiante=<?= $estudiante['id_estudiante'] ?>&grado=<?= $estudiante['id_grado'] ?>&seccion=<?= $estudiante['id_seccion'] ?>" 
                                                       class="btn btn-primary btn-sm" title="Ver Notas">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
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

<?php
include('../../admin/layout/parte2.php');
include('../../layout/mensajes.php');
?>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#tabla_notas').DataTable({
        "responsive": true,
        "autoWidth": false,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });

    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Cargar secciones según el grado seleccionado
    $('#grado').change(function() {
        var gradoId = $(this).val();
        if (gradoId) {
            $.ajax({
                url: 'cargar_secciones.php',
                type: 'GET',
                data: {grado_id: gradoId},
                success: function(data) {
                    $('#seccion').html(data);
                }
            });
        } else {
            $('#seccion').html('<option value="">Todas las Secciones</option>');
        }
    });
});
</script>