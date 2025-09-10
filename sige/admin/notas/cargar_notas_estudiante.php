<?php
require_once('../../app/config.php');

$id_estudiante = $_GET['id_estudiante'] ?? null;
$id_lapso = $_GET['id_lapso'] ?? null;

if (!$id_estudiante || !$id_lapso) {
    echo '<div class="alert alert-danger">Faltan parámetros requeridos</div>';
    exit();
}

// Obtener las notas del estudiante para el lapso seleccionado
$sql_notas = "SELECT ne.*, m.nombre_materia 
              FROM notas_estudiantes ne
              JOIN materias m ON ne.id_materia = m.id_materia
              WHERE ne.id_estudiante = :id_estudiante 
              AND ne.id_lapso = :id_lapso";
$query_notas = $pdo->prepare($sql_notas);
$query_notas->bindParam(':id_estudiante', $id_estudiante);
$query_notas->bindParam(':id_lapso', $id_lapso);
$query_notas->execute();
$notas = $query_notas->fetchAll(PDO::FETCH_ASSOC);

if (empty($notas)) {
    echo '<div class="alert alert-warning">No se encontraron calificaciones para este lapso</div>';
} else {
    echo '<table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center">Materia</th>
                    <th class="text-center">Calificación</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($notas as $nota) {
        echo '<tr>
                <td class="text-center">'.htmlspecialchars($nota['nombre_materia']).'</td>
                <td class="text-center">'.number_format($nota['calificacion'], 2).'</td>
              </tr>';
    }
    
    echo '</tbody></table>';
}
?>