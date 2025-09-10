<?php  
include('../app/config.php');  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
    $email = $_POST['email'];  
    $password = $_POST['password'];  

    $sql = "SELECT * FROM usuarios WHERE email = :email";  
    $query = $pdo->prepare($sql);  
    $query->bindParam(':email', $email);  
    $query->execute();  
    $usuario = $query->fetch(PDO::FETCH_ASSOC);  

    if ($usuario) {  
        if ($usuario['estado'] == '0') {
            session_start();  
            $_SESSION['mensaje'] = "El usuario está inactivo. Contacte al administrador.";  
            $_SESSION['icono'] = "warning";
            header('Location:' . APP_URL . "/login/login.php");  
            exit;  
        } else {
            if (password_verify($password, $usuario['password'])) {  
                session_start();  
                
                // AÑADE ESTAS LÍNEAS CLAVE:
                $_SESSION['usuario_id'] = $usuario['id_usuario']; 
                $_SESSION['rol_id'] = $usuario['rol_id'];  // ← ESTA ES LA LÍNEA MÁS IMPORTANTE
                $_SESSION['sesion_email'] = $email;
                
                $_SESSION['mensaje'] = "Bienvenido al sistema SIGE";  
                $_SESSION['icono'] = "success";  
                
                // Redirección inteligente según rol
                if ($usuario['rol_id'] == 1) { // Admin
                    header('Location:' . APP_URL . "/admin");
                } else { // Docente u otros roles
                    header('Location:' . APP_URL . "/admin/notas");
                }
                exit;  
            } else {  
                session_start();  
                $_SESSION['mensaje'] = "Error de Usuario o Contraseña";  
                $_SESSION['icono'] = "error";  
                header('Location:' . APP_URL . "/login/login.php");  
                exit;  
            }  
        }
    } else {  
        session_start();  
        $_SESSION['mensaje'] = "Error de Usuario o Contraseña";  
        $_SESSION['icono'] = "error";  
        header('Location:' . APP_URL . "/login/login.php");  
        exit;  
    }  
} else {  
    header('Location:' . APP_URL . "/login/login.php");  
    exit;  
}  
?>