<?php
include('../../app/config.php');
include('../../admin/layout/parte1.php');

// Obtener lista de profesores
$sql_profesores = "SELECT * FROM profesores ORDER BY apellidos, nombres";
$query_profesores = $pdo->prepare($sql_profesores);
$query_profesores->execute();
$profesores = $query_profesores->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container-fluid">
        <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h3 class="m-0">Listado de Profesores</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?= APP_URL; ?>/admin">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="<?= APP_URL; ?>/admin/profesores">Profesores</a></li>
                                <li class="breadcrumb-item active">Listado de Profesores</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Listado de Profesores</h3>
                            <div class="card-tools">
                                <a href="crear_profesor.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Nuevo Profesor
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Cédula</th>
                                        <th>Nombres</th>
                                        <th>Apellidos</th>
                                        <th>Especialidad</th>
                                        <th>Teléfono</th>
                                        <th>Usuario</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($profesores as $profesor): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($profesor['cedula']); ?></td>
                                        <td><?= htmlspecialchars($profesor['nombres']); ?></td>
                                        <td><?= htmlspecialchars($profesor['apellidos']); ?></td>
                                        <td><?= htmlspecialchars($profesor['especialidad']); ?></td>
                                        <td><?= htmlspecialchars($profesor['telefono']); ?></td>
                                        <td><?= htmlspecialchars($profesor['usuario']); ?></td>
                                        <td>
                                            <?php if ($profesor['estado'] == 1): ?>
                                                <span class="badge badge-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="ver_profesor.php?id=<?= $profesor['id_profesor']; ?>" class="btn btn-sm btn-info" title="Ver Carnet">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar_profesor.php?id=<?= $profesor['id_profesor']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($profesor['estado'] == 1): ?>
                                                <a href="cambiar_estado.php?id=<?= $profesor['id_profesor']; ?>&estado=0" class="btn btn-sm btn-secondary" title="Inhabilitar" onclick="return confirm('¿Está seguro de inhabilitar este profesor?')">
                                                    <i class="fas fa-toggle-off"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="cambiar_estado.php?id=<?= $profesor['id_profesor']; ?>&estado=1" class="btn btn-sm btn-success" title="Habilitar" onclick="return confirm('¿Está seguro de habilitar este profesor?')">
                                                    <i class="fas fa-toggle-on"></i>
                                                </a>
                                            <?php endif; ?>
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

<!-- Modal para mostrar el carnet -->
<div class="modal fade" id="modalCarnet" tabindex="-1" role="dialog" aria-labelledby="modalCarnetLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCarnetLabel">Carnet del Profesor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenidoCarnet">
                <!-- Aquí se cargará el contenido del carnet -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">Imprimir</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para cargar el carnet del profesor en el modal
function verCarnet(idProfesor) {
    $.ajax({
        url: 'ver_carnet.php',
        type: 'GET',
        data: {id: idProfesor},
        success: function(response) {
            $('#contenidoCarnet').html(response);
            $('#modalCarnet').modal('show');
        },
        error: function() {
            alert('Error al cargar el carnet del profesor');
        }
    });
}
</script>

<?php
include('../../admin/layout/parte2.php');
?>