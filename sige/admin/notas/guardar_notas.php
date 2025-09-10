<?php
require_once('../../app/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_estudiante = $_POST['id_estudiante'];
    $id_grado = $_POST['id_grado'];
    $id_lapso = $_POST['id_lapso'];
    $materias = $_POST['materias'] ?? [];
    $calificaciones = $_POST['calificaciones'] ?? [];

    try {
        $pdo->beginTransaction();

        foreach ($materias as $index => $id_materia) {
            if (!empty($id_materia) && isset($calificaciones[$index])) {
                $calificacion = $calificaciones[$index];
                
                // Verificar si ya existe una nota para esta combinación
                $sql_check = "SELECT id_nota FROM notas_estudiantes 
                              WHERE id_estudiante = :id_estudiante 
                              AND id_materia = :id_materia 
                              AND id_lapso = :id_lapso";
                $stmt_check = $pdo->prepare($sql_check);
                $stmt_check->bindParam(':id_estudiante', $id_estudiante);
                $stmt_check->bindParam(':id_materia', $id_materia);
                $stmt_check->bindParam(':id_lapso', $id_lapso);
                $stmt_check->execute();
                
                if ($stmt_check->rowCount() > 0) {
                    // Actualizar nota existente
                    $sql_update = "UPDATE notas_estudiantes 
                                  SET calificacion = :calificacion
                                  WHERE id_estudiante = :id_estudiante 
                                  AND id_materia = :id_materia 
                                  AND id_lapso = :id_lapso";
                    $stmt_update = $pdo->prepare($sql_update);
                } else {
                    // Insertar nueva nota
                    $sql_update = "INSERT INTO notas_estudiantes 
                                  (id_estudiante, id_materia, id_lapso, calificacion)
                                  VALUES (:id_estudiante, :id_materia, :id_lapso, :calificacion)";
                    $stmt_update = $pdo->prepare($sql_update);
                }
                
                $stmt_update->bindParam(':id_estudiante', $id_estudiante);
                $stmt_update->bindParam(':id_materia', $id_materia);
                $stmt_update->bindParam(':id_lapso', $id_lapso);
                $stmt_update->bindParam(':calificacion', $calificacion);
                $stmt_update->execute();
            }
        }

        $pdo->commit();
        $_SESSION['mensaje'] = "Notas guardadas correctamente";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error al guardar las notas: " . $e->getMessage();
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>