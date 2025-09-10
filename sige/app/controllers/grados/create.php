<?php
include ('../../../app/config.php');

// Iniciar la sesión
session_start();

// Verificar si los datos del formulario están definidos
if (isset($_POST['nivel']) && isset($_POST['grado'])) {
    // Obtener los datos del formulario
    $nivel = $_POST['nivel'];
    $grado = $_POST['grado']; // Este ahora será un texto como "Primer Nivel" o "Primer Grado"

    // Definir el estado del registro (puedes ajustarlo según tus necesidades)
    $estado_de_registro = 1; // 1 para activo, 0 para inactivo

    // Preparar la sentencia SQL para insertar los datos
    $sentencia = $pdo->prepare('INSERT INTO grados (nivel, grado, estado) VALUES (:nivel, :grado, :estado)');

    // Vincular los parámetros
    $sentencia->bindParam(':nivel', $nivel);
    $sentencia->bindParam(':grado', $grado);
    $sentencia->bindParam(':estado', $estado_de_registro);

    // Ejecutar la sentencia
    if ($sentencia->execute()) {
        // Si la inserción es exitosa
        $_SESSION['mensaje'] = "Se registró el grado correctamente .";
        $_SESSION['icono'] = "success";
        header('Location: ' . APP_URL . "/admin/configuraciones/grados"); // Redirigir a la lista de grados
        exit();
    } else {
        // Si hay un error al registrar
        $_SESSION['mensaje'] = "Error: no se pudo registrar en la base de datos, comuníquese con el administrador.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . "/admin/configuraciones/grados/create.php"); // Redirigir de vuelta al formulario
        exit();
    }
} else {
    // Si no se enviaron los datos correctamente
    $_SESSION['mensaje'] = "Error: datos del formulario no enviados correctamente.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . "/admin/configuraciones/grados/create.php"); // Redirigir de vuelta al formulario
    exit();
}
?>