<?php
include('../app/config.php');
session_start();

// Prevenir caching de la página de login
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificar si la imagen de fondo existe
$fondo_path = __DIR__ . '/../img/fondo3.jpg';
$fondo_url = APP_URL . '/img/fondo3.jpg';
$fondo_existe = file_exists($fondo_path);

// Si no existe, intentar con fondo2.jpg como respaldo
if (!$fondo_existe) {
    $fondo_path = __DIR__ . '/../img/fondo2.jpg';
    $fondo_url = APP_URL . '/img/fondo2.jpg';
    $fondo_existe = file_exists($fondo_path);
}

// Si ninguna imagen existe, usar un color de fondo sólido
$estilo_fondo = $fondo_existe 
    ? "background-image: url('" . htmlspecialchars($fondo_url, ENT_QUOTES) . "');" 
    : "background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);";

// Procesar recuperación de contraseña si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recovery_method'])) {
    $email = trim($_POST['email'] ?? '');
    $recovery_method = $_POST['recovery_method'];
    
    // Validar que los campos requeridos estén presentes
    if (empty($email) || empty($recovery_method)) {
        $_SESSION['mensaje'] = "Todos los campos son requeridos";
        $_SESSION['icono'] = "error";
        header('Location: login.php');
        exit;
    }

    // Validar método de recuperación
    if (!in_array($recovery_method, ['email', 'security'])) {
        $_SESSION['mensaje'] = "Método de recuperación inválido";
        $_SESSION['icono'] = "error";
        header('Location: login.php');
        exit;
    }
    
    // VALIDACIÓN: Verificar que el email no esté vacío
    if (empty($email)) {
        $_SESSION['mensaje'] = "Por favor ingresa tu correo electrónico";
        $_SESSION['icono'] = "error";
        header('Location: login.php');
        exit;
    }
    
    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['mensaje'] = "Por favor ingresa un correo electrónico válido";
        $_SESSION['icono'] = "error";
        header('Location: login.php');
        exit;
    }
    
    // Buscar usuario por email
    $sql = "SELECT * FROM usuarios WHERE email = :email AND estado = 1";
    $query = $pdo->prepare($sql);
    $query->bindParam(':email', $email);
    $query->execute();
    $usuario = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        if ($recovery_method === 'email') {
            // Generar token de recuperación
            $token = bin2hex(random_bytes(32));
            $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Guardar token en la base de datos
            $sql_token = "UPDATE usuarios SET token_recuperacion = :token, expiracion_token = :expiracion WHERE id_usuario = :id";
            $query_token = $pdo->prepare($sql_token);
            $query_token->bindParam(':token', $token);
            $query_token->bindParam(':expiracion', $expiracion);
            $query_token->bindParam(':id', $usuario['id_usuario']);
            
            if ($query_token->execute()) {
                // Crear enlace de recuperación
                $enlace_recuperacion = APP_URL . "/login/restablecer.php?token=$token&email=" . urlencode($email);
                
                // ENVÍO REAL DE EMAIL
                $asunto = "Recuperación de Contraseña - " . APP_NAME;
                $cuerpo = crearTemplateRecuperacion($enlace_recuperacion, $email);
                
                if (enviarEmail($email, $asunto, $cuerpo)) {
                    $_SESSION['mensaje'] = "✅ <strong>¡Correo enviado exitosamente!</strong><br><br>"
                                         . "Se ha enviado un enlace de recuperación a:<br>"
                                         . "<span style='color: #2d5f7e; font-weight: bold; background: #f8f9fa; padding: 5px 10px; border-radius: 5px;'>" 
                                         . htmlspecialchars($email) 
                                         . "</span><br><br>"
                                         . "• Revisa tu bandeja de entrada<br>"
                                         . "• Si no lo encuentras, revisa la carpeta de spam<br>"
                                         . "• El enlace expira en 1 hora";
                    $_SESSION['icono'] = "success";
                    $_SESSION['titulo'] = "✅ Correo enviado";
                    $_SESSION['email_enviado'] = $email; // Guardar email para JS
                } else {
                    $_SESSION['mensaje'] = "❌ <strong>Error al enviar el correo</strong><br><br>"
                                         . "No se pudo enviar el email a:<br>"
                                         . "<span style='color: #dc3545; font-weight: bold;'>" 
                                         . htmlspecialchars($email) 
                                         . "</span><br><br>"
                                         . "Por favor contacta al administrador del sistema.";
                    $_SESSION['icono'] = "error";
                    $_SESSION['titulo'] = "❌ Error de envío";
                }
            } else {
                $_SESSION['mensaje'] = "Error al generar el enlace de recuperación.";
                $_SESSION['icono'] = "error";
            }
            
        } elseif ($recovery_method === 'security') {
            // Verificar si el usuario tiene preguntas de seguridad configuradas
            $sql_preguntas = "SELECT * FROM preguntas_seguridad WHERE usuario_id = :usuario_id";
            $query_preguntas = $pdo->prepare($sql_preguntas);
            $query_preguntas->bindParam(':usuario_id', $usuario['id_usuario']);
            $query_preguntas->execute();
            $preguntas = $query_preguntas->fetch(PDO::FETCH_ASSOC);
            
            if ($preguntas) {
                // Guardar en sesión para verificar después
                $_SESSION['recovery_user_id'] = $usuario['id_usuario'];
                $_SESSION['recovery_method'] = 'security';
                $_SESSION['pregunta1'] = $preguntas['pregunta1'];
                $_SESSION['pregunta2'] = $preguntas['pregunta2'];
                
                // Redirigir a verificación de preguntas
                header('Location: verificar_preguntas.php');
                exit;
            } else {
                $_SESSION['mensaje'] = "No tienes preguntas de seguridad configuradas. Usa el método de email.";
                $_SESSION['icono'] = "warning";
            }
        }
    } else {
        $_SESSION['mensaje'] = "No se encontró un usuario activo con ese email.";
        $_SESSION['icono'] = "error";
    }
    
    // Si la petición es AJAX (fetch), devolver solo mensaje para JS
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // Respuesta JSON con estado y mensaje
        header('Content-Type: application/json');
        echo json_encode([
            'icon' => $_SESSION['icono'] ?? 'info',
            'title' => $_SESSION['titulo'] ?? '',
            'message' => $_SESSION['mensaje'] ?? '',
            'email' => $_SESSION['email_enviado'] ?? ''
        ]);
        // Limpiar mensajes para evitar mostrar en recarga
        unset($_SESSION['mensaje'], $_SESSION['icono'], $_SESSION['titulo'], $_SESSION['email_enviado']);
        exit;
    }

    header('Location: login.php');
    exit;
}
?>  
<!DOCTYPE html>  
<html lang="es">  
<head>  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - <?=htmlspecialchars(APP_NAME, ENT_QUOTES)?></title>
    
    <!-- Google Font: Source Sans Pro -->  
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">  
    <!-- Font Awesome -->  
    <link rel="stylesheet" href="<?=htmlspecialchars(APP_URL, ENT_QUOTES)?>/public/plugins/fontawesome-free/css/all.min.css">  
    <!-- icheck bootstrap -->  
    <link rel="stylesheet" href="<?=htmlspecialchars(APP_URL, ENT_QUOTES)?>/public/plugins/icheck-bootstrap/icheck-bootstrap.min.css">  
    <!-- Theme style -->  
    <link rel="stylesheet" href="<?=htmlspecialchars(APP_URL, ENT_QUOTES)?>/public/dist/css/adminlte.min.css">  
    <!-- Sweetalert2 -->  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  
    
    <style>
        .login-page {
            <?= $estilo_fondo ?>
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .login-header {
            background: linear-gradient(135deg, #3c8dbc 0%, #2d5f7e 100%);
            color: white;
            padding: 25px 20px 20px 20px;
            text-align: center;
            margin: -1px;
        }
        .logo-container {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            background: white;
            border-radius: 50%;
            width: 160px;
            height: 160px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .login-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }
        .login-logo img {
            transition: transform 0.3s ease;
            border: none;
            background: transparent;
            padding: 0;
            border-radius: 0;
            max-width: 120%;
            max-height: 120%;
            height: auto;
            width: auto;
            object-fit: contain;
        }
        .login-logo img:hover {
            transform: scale(1.08);
        }
        .system-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 20px 0 8px;
            line-height: 1.3;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            font-family: 'Source Sans Pro', sans-serif;
        }
        .system-subtitle {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 8px 0 5px;
            line-height: 1.3;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            font-family: 'Source Sans Pro', sans-serif;
            opacity: 0.9;
        }
        .card-body {
            padding: 25px;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        .form-control {
            border-left: none;
            padding-left: 5px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
        .form-control:focus + .input-group-append .input-group-text {
            border-color: #80bdff;
        }
        .btn-login {
            background: linear-gradient(135deg, #3c8dbc 0%, #2d5f7e 100%);
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, #2d5f7e 0%, #3c8dbc 100%);
        }
        .btn-forgot {
            color: #3c8dbc;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        .btn-forgot:hover {
            color: #2d5f7e;
            text-decoration: underline;
        }
        .icheck-primary input:checked+label::before {
            background-color: #3c8dbc;
            border-color: #3c8dbc;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            border-top: 1px solid #eee;
            font-size: 0.85rem;
            background: linear-gradient(135deg, #3c8dbc 0%, #2d5f7e 100%);
            color: white;
            font-weight: bold;
            border-radius: 0 0 15px 15px;
            margin: -1px;
        }
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }
        .modal-header {
            background: linear-gradient(135deg, #3c8dbc 0%, #2d5f7e 100%);
            color: white;
        }
        .recovery-option {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .recovery-option:hover {
            border-color: #3c8dbc;
            box-shadow: 0 0 10px rgba(60, 141, 188, 0.2);
            transform: translateY(-2px);
        }
        .recovery-option.active {
            border-color: #3c8dbc;
            background-color: rgba(60, 141, 188, 0.1);
        }
        .recovery-form {
            transition: all 0.3s ease;
        }
        .custom-swal-popup {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .swal2-popup .swal2-title {
            color: #3c8dbc;
            font-weight: bold;
        }
        .swal2-popup .swal2-html-container strong {
            color: #2d5f7e;
        }
        @media (max-width: 576px) {
            .login-container {
                max-width: 100%;
            }
            .login-card {
                border-radius: 10px;
            }
            .system-name {
                font-size: 1.3rem;
            }
            .system-subtitle {
                font-size: 1.1rem;
            }
            .logo-container {
                width: 140px;
                height: 140px;
                padding: 18px;
            }
            .login-logo img {
                max-width: 110%;
                max-height: 110%;
            }
        }
    </style>
</head>  
<body class="hold-transition login-page">  
<div class="login-container">
    <div class="login-card card">  
        <div class="login-header">
            <div class="logo-container">
                <figure class="login-logo">  
                    <img src="<?=htmlspecialchars(APP_URL, ENT_QUOTES)?>/img/logo.jpg" alt="<?=htmlspecialchars(APP_NAME, ENT_QUOTES)?>" class="img-fluid">  
                </figure>  
            </div>
            <h2 class="system-name">Sistema Integral de Gestión</h2>
            <h2 class='system-name'>de Inscripciones (SIGI)</h2>
        </div>
        
        <div class="card-body login-card-body">  
            <form action="controller_login.php" method="post" id="loginForm" autocomplete="on">  
                <div class="input-group mb-4">  
                    <div class="input-group-prepend">  
                        <div class="input-group-text">  
                            <span class="fas fa-envelope"></span>    
                        </div>  
                    </div>  
                    <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required autocomplete="" autocapitalize="off" inputmode="email">
                </div>  
                
                <div class="input-group mb-4">  
                    <div class="input-group-prepend">  
                        <div class="input-group-text">  
                            <span class="fas fa-lock"></span>  
                        </div>  
                    </div>  
                    <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" required minlength="6" autocomplete="current-password">
                    <div class="input-group-append">  
                        <div class="input-group-text" id="togglePassword" title="Mostrar/Ocultar contraseña">  
                            <span class="fas fa-eye" id="eyeIcon"></span>  
                        </div>  
                    </div>  
                </div>  

               <div class="row mb-3">
    <div class="col-7 d-flex align-items-center">
        <div class="icheck-primary">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" title="Mantenerme autenticado">
                Recordarme
            </label>
        </div>
    </div>
    <div class="col-5">
        <button type="submit" class="btn btn-primary btn-block btn-login">
            <span class="fas fa-sign-in-alt"></span>
            Acceder
        </button>
    </div>
</div>

<div class="text-center mt-3">
    <a href="#" class="btn-forgot" data-toggle="modal" data-target="#recoveryModal">
        <i class="fas fa-key mr-1"></i> ¿Olvidaste tu contraseña?
    </a>
</div>
</form>  
</div>  

<div class="login-footer">
    <?= htmlspecialchars(APP_NAME, ENT_QUOTES) ?> &copy; <?= date('Y') ?>
</div>
</div>  
</div>  

<!-- Modal para recuperación de contraseña -->
<div class="modal fade" id="recoveryModal" tabindex="-1" role="dialog" aria-labelledby="recoveryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recoveryModalLabel">Recuperar Contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <form action="login.php" method="post" id="recoveryForm">
                <div class="modal-body">
                    <p class="text-center">Selecciona un método para recuperar tu contraseña:</p>
                    
                    <div class="recovery-option" data-method="email">
                        <div class="text-center">
                            <i class="fas fa-envelope fa-2x mb-2 text-primary"></i>
                            <h5>Recuperar por correo electrónico</h5>
                            <p class="small">Te enviaremos un enlace para restablecer tu contraseña</p>
                        </div>
                    </div>
                    
                    <div class="recovery-option" data-method="security">
                        <div class="text-center">
                            <i class="fas fa-shield-alt fa-2x mb-2 text-primary"></i>
                            <h5>Recuperar por preguntas de seguridad</h5>
                            <p class="small">Responde tus preguntas de seguridad para restablecer tu contraseña</p>
                        </div>
                    </div>
                    
                    <div id="emailRecoveryForm" class="recovery-form d-none mt-3">
                        <hr>
                        <div class="form-group">
                            <label for="recoveryEmail">Correo electrónico registrado</label>
                            <input type="email" class="form-control" id="recoveryEmail" name="email" required>
                        </div>
                    </div>
                    
                    <div id="securityRecoveryForm" class="recovery-form d-none mt-3">
                        <hr>
                        <div class="form-group">
                            <label for="securityEmail">Correo electrónico</label>
                            <input type="email" class="form-control" id="securityEmail" name="email" required>
                        </div>
                    </div>
                    
                    <input type="hidden" name="recovery_method" id="recoveryMethod" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="recoverySubmitBtn" disabled>Continuar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="<?=htmlspecialchars(APP_URL, ENT_QUOTES)?>/public/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=htmlspecialchars(APP_URL, ENT_QUOTES)?>/public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=htmlspecialchars(APP_URL, ENT_QUOTES)?>/public/dist/js/adminlte.min.js"></script>

<script>  
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');  
    const passwordInput = document.getElementById('password');  
    const eyeIcon = document.getElementById('eyeIcon');  

    if (togglePassword && passwordInput && eyeIcon) {
        togglePassword.addEventListener('click', () => {  
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';  
            passwordInput.setAttribute('type', type);  
            eyeIcon.classList.toggle('fa-eye');  
            eyeIcon.classList.toggle('fa-eye-slash');  
        });
    }
    
    // Form submission handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
        });
    }

    // Recuperación de contraseña
    const recoveryOptions = document.querySelectorAll('.recovery-option');
    const emailRecoveryForm = document.getElementById('emailRecoveryForm');
    const securityRecoveryForm = document.getElementById('securityRecoveryForm');
    const recoveryMethodInput = document.getElementById('recoveryMethod');
    const recoverySubmitBtn = document.getElementById('recoverySubmitBtn');
    const recoveryForm = document.getElementById('recoveryForm');
    let selectedMethod = '';

    // Función para validar email
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Seleccionar método de recuperación
    recoveryOptions.forEach(option => {
        option.addEventListener('click', function() {
            recoveryOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            selectedMethod = this.getAttribute('data-method');
            recoveryMethodInput.value = selectedMethod;
            
            emailRecoveryForm.classList.add('d-none');
            securityRecoveryForm.classList.add('d-none');
            
            if (selectedMethod === 'email') {
                emailRecoveryForm.classList.remove('d-none');
                setTimeout(() => {
                    document.getElementById('recoveryEmail').focus();
                }, 100);
            } else if (selectedMethod === 'security') {
                securityRecoveryForm.classList.remove('d-none');
                setTimeout(() => {
                    document.getElementById('securityEmail').focus();
                }, 100);
            }
            
            recoverySubmitBtn.disabled = false;
        });
    });

    // Enviar formulario recuperación
    recoverySubmitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (!selectedMethod) {
            Swal.fire('Error', 'Por favor selecciona un método de recuperación', 'error');
            return;
        }
        
        let email, isValid = false;
        let emailField;
        
        if (selectedMethod === 'email') {
            emailField = document.getElementById('recoveryEmail');
            email = emailField.value.trim();
            isValid = email && isValidEmail(email);
        } else if (selectedMethod === 'security') {
            emailField = document.getElementById('securityEmail');
            email = emailField.value.trim();
            isValid = email && isValidEmail(email);
        }
        
        if (!isValid) {
            Swal.fire('Error', 'Por favor ingresa un correo electrónico válido', 'error');
            if(emailField) emailField.focus();
            return;
        }
        
        recoverySubmitBtn.disabled = true;
        recoverySubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
        
        const formData = new FormData();
        formData.append('email', email);
        formData.append('recovery_method', selectedMethod);
        
        fetch('login.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.icon === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: data.title || 'Correo enviado',
                    html: `Se ha enviado un enlace de recuperación a:<br><strong style="color:#2d5f7e;">${email}</strong><br><br>
                           - Revisa tu bandeja de entrada<br>
                           - Si no lo encuentras, revisa la carpeta de spam<br>
                           - El enlace expira en 1 hora`,
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                }).then(() => {
                    $('#recoveryModal').modal('hide');
                    recoveryForm.reset();
                    recoverySubmitBtn.disabled = true;
                    recoverySubmitBtn.innerHTML = 'Continuar';
                    recoveryOptions.forEach(opt => opt.classList.remove('active'));
                    emailRecoveryForm.classList.add('d-none');
                    securityRecoveryForm.classList.add('d-none');
                    recoveryMethodInput.value = '';
                    selectedMethod = '';
                });
            } else {
                Swal.fire({
                    icon: data.icon || 'error',
                    title: data.title || 'Error',
                    html: data.message || 'Ocurrió un error al procesar la solicitud',
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                });
                recoverySubmitBtn.disabled = false;
                recoverySubmitBtn.innerHTML = 'Continuar';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error');
            recoverySubmitBtn.disabled = false;
            recoverySubmitBtn.innerHTML = 'Continuar';
        });
    });

    // Resetear formulario al cerrar modal
    $('#recoveryModal').on('hidden.bs.modal', function () {
        recoveryOptions.forEach(opt => opt.classList.remove('active'));
        emailRecoveryForm.classList.add('d-none');
        securityRecoveryForm.classList.add('d-none');
        recoverySubmitBtn.disabled = true;
        recoverySubmitBtn.innerHTML = 'Continuar';
        recoveryMethodInput.value = '';
        recoveryForm.reset();
        selectedMethod = '';
    });

    // Mostrar mensajes de sesión (no AJAX)
    <?php if (isset($_SESSION['mensaje'])): ?>  
        Swal.fire({  
            icon: '<?= isset($_SESSION['icono']) ? addslashes($_SESSION['icono']) : 'info' ?>',  
            title: '<?= isset($_SESSION['titulo']) ? addslashes($_SESSION['titulo']) : (isset($_SESSION['icono']) && $_SESSION['icono'] === 'success' ? 'Éxito' : 'Error') ?>',  
            html: '<?= addslashes($_SESSION['mensaje']) ?>',
            confirmButtonText: 'Aceptar',
            allowOutsideClick: false,
            customClass: {
                popup: 'custom-swal-popup'
            }
        });  
        <?php   
            unset($_SESSION['mensaje']);
            unset($_SESSION['icono']);
            unset($_SESSION['titulo']);
        ?>  
    <?php endif; ?>  
});
</script>  

</body>  
</html>