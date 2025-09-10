<?php
$seccion_id = $_GET['id'];
include ('../../../app/config.php');
include ('../../../admin/layout/parte1.php');
include ('../../../app/controllers/secciones/datos_secciones.php'); // Asegúrate de que este archivo obtenga los datos correctamente

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
                            <h1 class="m-0">Editar Sección</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?=APP_URL;?>/admin">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?=APP_URL;?>/admin/secciones">Secciones</a></li>
                                <li class="breadcrumb-item">Editar Sección</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-success">
                        <div class="card-body">
                            <form action="<?=APP_URL;?>/app/controllers/secciones/update.php" method="post">
                                <input type="hidden" name="id_seccion" value="<?=$seccion_id;?>"> <!-- Asegúrate de que este campo esté presente -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Turno</label>
                                            <select name="turno" class="form-control" required>
                                                <option value="Mañana" <?= $turno == "Mañana" ? 'selected' : ''; ?>>Mañana</option>
                                                <option value="Tarde" <?= $turno == "Tarde" ? 'selected' : ''; ?>>Tarde</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Cupos</label>
                                            <input value="<?=$capacidad;?>" type="number" name="capacidad" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Grado</label>
                                            <select name="id_grado" id="id_grado" class="form-control" required onchange="checkGrado()">
                                                <option value="">Seleccione un grado</option>
                                                <?php
                                                // Lógica para llenar el select de grados
                                                $sql_grados = "SELECT * FROM grados";
                                                $query_grados = $pdo->prepare($sql_grados);
                                                $query_grados->execute();
                                                $grados = $query_grados->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($grados as $grado) {
                                                    $selected = ($id_grado == $grado['id_grado']) ? 'selected' : '';
                                                    echo "<option value='{$grado['id_grado']}' $selected>{$grado['grado']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Periodo Académico</label>
                                            <?php if ($gestion_activa): ?>
                                                <input type="text" class="form-control" value="Desde: <?=$gestion_activa['desde'];?> Hasta: <?=$gestion_activa['hasta'];?>" readonly> <!-- Campo de texto para el periodo académico activo -->
                                            <?php else: ?>
                                                <input type="text" class="form-control" value="No hay periodo activo" readonly>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Nombre Sección</label>
                                            <input type="text" name="nombre_seccion" id="nombre_seccion" class="form-control" value="<?=$nombre_seccion;?>" required placeholder="Escriba el nombre de la sección"> <!-- Campo de texto para el nombre de la sección -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Estado</label>
                                            <select name="estado" class="form-control" required>
                                                <option value="1" <?= $estado == "1" ? 'selected' : ''; ?>>ACTIVO</option>
                                                <option value="0" <?= $estado == "0" ? 'selected' : ''; ?>>INACTIVO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <center>
                                                <button type="submit" class="btn btn-success">Actualizar</button>
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
    const seccionInput = document.getElementById('nombre_seccion');
    const gradoSeleccionado = gradoSelect.options[gradoSelect.selectedIndex].text;

  
}
</script>

<?php
include('../../../admin/layout/parte2.php');
include ('../../../layout/mensajes.php');
?>