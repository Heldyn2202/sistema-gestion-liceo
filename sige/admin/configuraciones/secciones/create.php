<?php
include ('../../../app/config.php');
include ('../../../admin/layout/parte1.php');

// Obtener los grados para llenar el select
$sql_grados = "SELECT * FROM grados";
$query_grados = $pdo->prepare($sql_grados);
$query_grados->execute();
$grados = $query_grados->fetchAll(PDO::FETCH_ASSOC);

// Obtener el periodo académico activo
$sql_gestiones = "SELECT * FROM gestiones WHERE estado = 1 ORDER BY desde DESC LIMIT 1"; // Suponiendo que el estado 1 es activo
$query_gestiones = $pdo->prepare($sql_gestiones);
$query_gestiones->execute();
$gestion_activa = $query_gestiones->fetch(PDO::FETCH_ASSOC);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Crear nueva Sección</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?=APP_URL;?>/admin">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?=APP_URL;?>/admin/configuraciones/secciones">Secciones</a></li>
                                <li class="breadcrumb-item">Crear nueva Sección</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-body">
                            <form action="<?=APP_URL;?>/app/controllers/secciones/create.php" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Turno</label>
                                            <select name="turno" id="turno" class="form-control" required>
                                                <option value="">Seleccione un turno</option>
                                                <option value="Mañana">Mañana</option>
                                                <option value="Tarde">Tarde</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Cupos</label>
                                            <input type="number" name="capacidad" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Grado</label>
                                            <select name="id_grado" id="id_grado" class="form-control" required onchange="checkGrado()">
                                                <option value="">Seleccione un grado</option>
                                                <?php foreach ($grados as $grado) { ?>
                                                    <option value="<?=$grado['id_grado'];?>"><?=$grado['grado'];?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Periodo Académico</label>
                                            <?php if ($gestion_activa): ?>
                                                <input type="text" class="form-control" value="Desde: <?=$gestion_activa['desde'];?> Hasta: <?=$gestion_activa['hasta'];?>" readonly> <!-- Campo de texto para el periodo académico activo -->
                                                <input type="hidden" name="id_gestion" value="<?=$gestion_activa['id_gestion'];?>"> <!-- Campo oculto para el ID del periodo académico -->
                                            <?php else: ?>
                                                <input type="text" class="form-control" value="No hay periodo activo" readonly>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Sección</label>
                                            <input type="text" name="nombre_seccion" id="seccion" class="form-control" required placeholder="Escriba la sección"> <!-- Campo de texto para la sección -->
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <center>
                                                <button type="submit" class="btn btn-primary">Registrar</button>
                                                <a href="<?=APP_URL;?>/admin/configuraciones/secciones" class="btn btn-secondary">Cancelar</a>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
function checkGrado() {
    const gradoSelect = document.getElementById('id_grado');
    const seccionInput = document.getElementById('seccion');
    const gradoSeleccionado = gradoSelect.options[gradoSelect.selectedIndex].text;

    // Aquí puedes agregar lógica adicional si es necesario
    // Por ejemplo, podrías mostrar un mensaje o realizar alguna validación
}
</script>

<?php
include ('../../../admin/layout/parte2.php');
include ('../../../layout/mensajes.php');
?>