<?php
include ('../../../app/config.php');



// Verificar si las claves existen en $_POST
if (isset($_POST['id_seccion']) && isset($_POST['turno']) && isset($_POST['capacidad']) && isset($_POST['id_grado']) && isset($_POST['nombre_seccion']) && isset($_POST['estado'])) {
    $id_seccion = $_POST['id_seccion'];
    $turno = $_POST['turno'];
    $capacidad = $_POST['capacidad'];
    $id_grado = $_POST['id_grado'];
    $nombre_seccion = $_POST['nombre_seccion'];
    $estado = $_POST['estado'];

    // Preparar la sentencia SQL para actualizar la sección
    $sentencia = $pdo->prepare('UPDATE secciones
    SET turno = :turno,
        capacidad = :capacidad,
        id_grado = :id_grado,
        nombre_seccion = :nombre_seccion,
        estado = :estado
    WHERE id_seccion = :id_seccion');

    // Vincular los parámetros
    $sentencia->bindParam(':id_seccion', $id_seccion);
    $sentencia->bindParam(':turno', $turno);
    $sentencia->bindParam(':capacidad', $capacidad);
    $sentencia->bindParam(':id_grado', $id_grado);
    $sentencia->bindParam(':nombre_seccion', $nombre_seccion);
    $sentencia->bindParam(':estado', $estado);

    // Ejecutar la sentencia
    if ($sentencia->execute()) {
        session_start();
        $_SESSION['mensaje'] = "Se actualizó la sección de manera correcta.";
        $_SESSION['icono'] = "success";
        header('Location: ' . APP_URL . "/admin/configuraciones/secciones");
        exit();
    } else {
        session_start();
        $_SESSION['mensaje'] = "Error: no se pudo actualizar, comuníquese con el administrador.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . "/admin/configuraciones/secciones/edit.php?id=" . $id_seccion); // Redirigir a la página de edición
        exit();
    }
} else {
    // Manejo de error si las claves no están definidas
    session_start();
    $_SESSION['mensaje'] = "Error: datos incompletos, por favor verifique.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . "/admin/configuraciones/secciones");
    exit();
}
?>