<?php
// app/controllers/chat.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

// Verificar autenticación
if (!isset($_SESSION['sesion_email'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No autenticado']);
        exit();
    } else {
        header('Location: ' . APP_URL . '/login');
        exit();
    }
}

// CONEXIÓN
try {
    $dsn = "mysql:dbname=" . BD . ";host=" . SERVIDOR . ";charset=utf8mb4";
    $pdo = new PDO($dsn, USUARIO, PASSWORD, array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
} catch (PDOException $e) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
        exit();
    } else {
        die('Error de conexión a la base de datos: ' . $e->getMessage());
    }
}

// Obtener el ID del usuario actual
$email_sesion = $_SESSION['sesion_email'];
$user_query = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email AND estado = '1'");
$user_query->bindParam(':email', $email_sesion, PDO::PARAM_STR);
$user_query->execute();
$user_data = $user_query->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Usuario no válido']);
        exit();
    } else {
        header('Location: ' . APP_URL . '/login');
        exit();
    }
}

$current_user_id = $user_data['id_usuario'];

// Determinar la acción solicitada
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_unread_count':
        getUnreadCount($pdo, $current_user_id);
        break;
    case 'get_contacts':
        getContacts($pdo, $current_user_id);
        break;
    case 'get_messages':
        header('Content-Type: application/json');

        if (!isset($_GET['contact_id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de contacto no proporcionado']);
            exit();
        }
        
        $contact_id = (int)$_GET['contact_id'];
        
        try {
            // PASO 1: MARCAR MENSAJES COMO LEÍDOS
            $update_query = $pdo->prepare("
                UPDATE chat_mensajes 
                SET leido = '1' 
                WHERE id_destinatario = :current_user_id 
                  AND id_remitente = :contact_id 
                  AND leido = '0'
                  AND estado = '1'
            ");
            $update_query->bindParam(':current_user_id', $current_user_id, PDO::PARAM_INT);
            $update_query->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
            $update_query->execute();

            // PASO 2: OBTENER TODOS LOS MENSAJES DE LA CONVERSACIÓN
            $select_query = $pdo->prepare("
                SELECT 
                    m.id_mensaje,
                    m.id_remitente,
                    m.id_destinatario,
                    m.mensaje,
                    m.archivo,
                    m.fecha_envio,
                    m.leido,
                    ur.email as remitente_email,
                    pr.nombres as remitente_nombres,
                    pr.apellidos as remitente_apellidos,
                    ud.email as destinatario_email,
                    pd.nombres as destinatario_nombres,
                    pd.apellidos as destinatario_apellidos
                FROM chat_mensajes m
                LEFT JOIN usuarios ur ON ur.id_usuario = m.id_remitente
                LEFT JOIN personas pr ON pr.usuario_id = ur.id_usuario
                LEFT JOIN usuarios ud ON ud.id_usuario = m.id_destinatario
                LEFT JOIN personas pd ON pd.usuario_id = ud.id_usuario
                WHERE (m.id_remitente = :current_user_id AND m.id_destinatario = :contact_id)
                    OR (m.id_remitente = :contact_id AND m.id_destinatario = :current_user_id)
                    AND m.estado = '1'
                ORDER BY m.fecha_envio ASC
            ");
            
            $select_query->bindParam(':current_user_id', $current_user_id, PDO::PARAM_INT);
            $select_query->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
            $select_query->execute();
            $messages = $select_query->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'messages' => $messages
            ]);
            
        } catch (PDOException $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener mensajes: ' . $e->getMessage()
            ]);
        }
        exit();
        break;
    case 'send_message':
        sendMessage($pdo, $current_user_id);
        break;
    case 'mark_as_read':
        markAsRead($pdo, $current_user_id);
        break;
    default:
        showChatInterface($pdo, $current_user_id);
        break;
}

// ----- FUNCIONES DE LA API -----

function getUnreadCount($pdo, $current_user_id) {
    header('Content-Type: application/json');
    try {
        $query = $pdo->prepare("
            SELECT
                id_remitente,
                COUNT(*) as unread_count
            FROM chat_mensajes
            WHERE id_destinatario = :current_user_id
              AND leido = '0'
              AND estado = '1'
            GROUP BY id_remitente
        ");
        
        $query->bindParam(':current_user_id', $current_user_id, PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        // Calcular el total de mensajes no leídos
        $total_unread = array_sum(array_column($results, 'unread_count'));
        
        // Reorganizar los datos para facilitar su uso en JavaScript
        $unread_by_contact = [];
        foreach ($results as $row) {
            $unread_by_contact[$row['id_remitente']] = (int)$row['unread_count'];
        }
        
        echo json_encode([
            'success' => true,
            'total_unread' => $total_unread,
            'unread_by_contact' => $unread_by_contact
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'total_unread' => 0, 'unread_by_contact' => [], 'message' => $e->getMessage()]);
    }
    exit();
}

function getContacts($pdo, $current_user_id) {
    header('Content-Type: application/json');
    try {
        $query = $pdo->prepare("
            SELECT 
                u.id_usuario, 
                u.email, 
                p.nombres, 
                p.apellidos 
            FROM usuarios u 
            LEFT JOIN personas p ON p.usuario_id = u.id_usuario 
            WHERE u.id_usuario != :current_user_id 
              AND u.estado = '1'
            ORDER BY p.nombres, p.apellidos, u.email
        ");
        
        $query->bindParam(':current_user_id', $current_user_id, PDO::PARAM_INT);
        $query->execute();
        $contacts = $query->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'contacts' => $contacts
        ]);
        
    } catch (PDOException $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener contactos: ' . $e->getMessage()
        ]);
    }
    exit();
}

function sendMessage($pdo, $current_user_id) {
    header('Content-Type: application/json');

    if (!isset($_POST['destinatario_id'])) {
        echo json_encode(['success' => false, 'message' => 'Destinatario no proporcionado']);
        exit();
    }

    $destinatario_id = (int)$_POST['destinatario_id'];
    $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
    $tieneArchivo = (isset($_FILES['file']) && isset($_FILES['file']['tmp_name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK);

    if ($mensaje === '' && !$tieneArchivo) {
        echo json_encode(['success' => false, 'message' => 'Debes enviar un texto o adjuntar un archivo.']);
        exit();
    }

    try {
        $check_user = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = :destinatario_id AND estado = '1'");
        $check_user->bindParam(':destinatario_id', $destinatario_id, PDO::PARAM_INT);
        $check_user->execute();
        if ($check_user->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'El usuario destinatario no existe o está inactivo']);
            exit();
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error validando destinatario: ' . $e->getMessage()]);
        exit();
    }

    $fileUrl = null;
    if ($tieneArchivo) {
        $uploadBase = realpath(__DIR__ . '/../') ?: (__DIR__ . '/../');
        $uploadDir = $uploadBase . '/uploads/';

        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0775, true);
        }
        
        $maxBytes = 16 * 1024 * 1024; 
        
        $allowedExt = ['jpg', 'jpeg', 'jfif', 'png', 'gif', 'webp', 'tiff', 'svg', 'ai', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'mp4', 'mov', 'webm', 'ogg', 'avi', 'mkv', 'txt'];
        
        $originalName = $_FILES['file']['name'];
        $tmpPath = $_FILES['file']['tmp_name'];
        $size = (int)$_FILES['file']['size'];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido.']);
            exit();
        }

        if ($size <= 0 || $size > $maxBytes) {
            echo json_encode(['success' => false, 'message' => 'El archivo excede el tamaño máximo permitido (16MB).']);
            exit();
        }

        $safeBase = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $uniqueName = $safeBase . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destPath = $uploadDir . $uniqueName;

        if (!move_uploaded_file($tmpPath, $destPath)) {
            echo json_encode(['success' => false, 'message' => 'Error al mover el archivo al directorio de destino.']);
            exit();
        }

        $fileUrl = 'app/uploads/' . $uniqueName;
    }

    try {
        $query = $pdo->prepare("
            INSERT INTO chat_mensajes (id_remitente, id_destinatario, mensaje, archivo, fecha_envio, leido, estado)
            VALUES (:remitente_id, :destinatario_id, :mensaje, :archivo, NOW(), '0', '1')
        ");
        
        $query->bindParam(':remitente_id', $current_user_id, PDO::PARAM_INT);
        $query->bindParam(':destinatario_id', $destinatario_id, PDO::PARAM_INT);
        $query->bindParam(':mensaje', $mensaje, PDO::PARAM_STR);
        $query->bindValue(':archivo', $fileUrl, PDO::PARAM_STR);

        if ($query->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Mensaje enviado correctamente',
                'message_id' => $pdo->lastInsertId(),
                'archivo' => $fileUrl
            ]);
        } else {
            $errorInfo = $query->errorInfo();
            echo json_encode([
                'success' => false,
                'message' => 'Error al ejecutar la inserción',
                'error_info' => $errorInfo
            ]);
        }
    } catch (PDOException $e) {
        error_log("Error en chat.php (sendMessage): " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al enviar mensaje: ' . $e->getMessage()]);
    }
    exit();
}

function markAsRead($pdo, $current_user_id) {
    header('Content-Type: application/json');
    if (!isset($_POST['contact_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de contacto no proporcionado']);
        exit();
    }
    
    $contact_id = (int)$_POST['contact_id'];
    
    try {
        $query = $pdo->prepare("
            UPDATE chat_mensajes 
            SET leido = '1' 
            WHERE id_destinatario = :current_user_id 
              AND id_remitente = :contact_id 
              AND leido = '0'
              AND estado = '1'
        ");
        
        $query->bindParam(':current_user_id', $current_user_id, PDO::PARAM_INT);
        $query->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
        $query->execute();
        
        echo json_encode(['success' => true, 'message' => 'Mensajes marcados como leídos']);
    } catch (PDOException $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'message' => 'Error al marcar mensajes como leídos: ' . $e->getMessage()]);
    }
    exit();
}

function showChatInterface($pdo, $current_user_id) {
    $chat_view_path = __DIR__ . '/chat_interface.php';
    
    if (file_exists($chat_view_path)) {
        $email_sesion = $_SESSION['sesion_email'];
        $user_query = $pdo->prepare("SELECT u.id_usuario, u.email, p.nombres, p.apellidos 
                                     FROM usuarios u 
                                     LEFT JOIN personas p ON p.usuario_id = u.id_usuario 
                                     WHERE u.email = :email AND u.estado = '1'");
        $user_query->bindParam(':email', $email_sesion, PDO::PARAM_STR);
        $user_query->execute();
        $user_data = $user_query->fetch(PDO::FETCH_ASSOC);
        
        $nombres_sesion_usuario = $user_data['nombres'] ?? 'Usuario';
        $apellidos_sesion_usuario = $user_data['apellidos'] ?? 'Sistema';
        
        include_once $chat_view_path;
    } else {
        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Chat - ' . APP_NAME . '</title>
            <link rel="stylesheet" href="' . APP_URL . '/public/dist/css/adminlte.min.css">
        </head>
        <body>
            <div class="container mt-5">
                <div class="alert alert-warning">
                    <h4><i class="fas fa-exclamation-triangle"></i> Vista de chat no encontrada</h4>
                    <p>El archivo chat_interface.php no existe en la ruta: ' . $chat_view_path . '</p>
                    <p>Contacta al administrador del sistema.</p>
                    <a href="' . APP_URL . '/admin" class="btn btn-primary">Volver al inicio</a>
                </div>
            </div>
        </body>
        </html>';
    }
}