<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['sesion_email'])) {
    header('Location: ' . APP_URL . '/login');
    exit();
}

require_once __DIR__ . '/../config.php';

if (!isset($current_user_id)) {
    try {
        $email_sesion = $_SESSION['sesion_email'];
        if (!isset($pdo)) {
            $dsn = "mysql:dbname=" . BD . ";host=" . SERVIDOR . ";charset=utf8mb4";
            $pdo = new PDO($dsn, USUARIO, PASSWORD, [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
        $user_query = $pdo->prepare("
            SELECT u.id_usuario, u.email, p.nombres, p.apellidos 
            FROM usuarios u 
            LEFT JOIN personas p ON p.usuario_id = u.id_usuario 
            WHERE u.email = :email AND u.estado = '1'
        ");
        $user_query->bindParam(':email', $email_sesion, PDO::PARAM_STR);
        $user_query->execute();
        $user_data = $user_query->fetch(PDO::FETCH_ASSOC);
        $current_user_id = $user_data['id_usuario'] ?? 0;
    } catch (Throwable $e) {
        $current_user_id = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Interno - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }
        .chat-container {
            height: calc(100vh - 150px);
            display: flex;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .contacts-list {
            width: 300px;
            background-color: #f8f9fa;
            border-right: 2px solid #dee2e6;
            overflow-y: auto;
        }
        .contact-item {
            position: relative;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }
        .contact-item:hover {
            background-color: #e9ecef;
        }
        .contact-item.active {
            background-color: #daeafc;
        }
        .contact-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #00aaff, #007bff);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 10px;
            font-size: 18px;
            font-weight: bold;
        }
        .chat-messages {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background-color: #e6ddd4;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .chat-header {
            padding: 12px 15px;
            background-color: #f0f0f0;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
        }
        .chat-body {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }
        .message {
            margin-bottom: 12px;
            display: flex;
            max-width: 80%;
            animation: fadeIn 0.3s ease-in-out;
            position: relative;
        }
        .message.sent {
            align-self: flex-end;
        }
        .message.received {
            align-self: flex-start;
        }
        .message-content {
            padding: 10px 14px;
            border-radius: 8px;
            position: relative;
            word-wrap: break-word;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }
        .message.sent .message-content {
            background-color: #DCF8C6;
            border-top-right-radius: 0;
        }
        .message.received .message-content {
            background-color: #FFFFFF;
            border-top-left-radius: 0;
        }
        .message-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 5px;
        }
        .message-time {
            font-size: 0.7rem;
            opacity: 0.7;
        }
        .message-status {
            font-size: 0.7rem;
            margin-left: 5px;
        }
        .message-status.sent-single {
            color: #888;
        }
        .message-status.read-double {
            color: #4CAF50;
        }
        .message-input-container {
            padding: 12px;
            background-color: #f0f0f0;
            border-top: 1px solid #ddd;
            display: flex;
            align-items: center;
        }
        .message-input {
            flex-grow: 1;
            padding: 10px 15px;
            border-radius: 20px;
            border: 1px solid #ddd;
            font-size: 1rem;
            margin: 0 5px;
            outline: none;
        }
        .send-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .attach-button {
            background-color: transparent;
            color: #6c757d;
            width: auto;
            height: auto;
            margin: 0 5px;
            padding: 0;
            border: none;
            font-size: 1.2rem;
        }
        .send-button:hover {
            background-color: #0056b3;
        }
        .attach-button:hover {
            color: #007bff;
        }
        .chat-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #999;
        }
        .chat-empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        .contact-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .contact-email {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .unread-badge {
            background-color: #dc3545;
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            border-radius: 50%;
            padding: 3px 6px;
            min-width: 20px;
            text-align: center;
            margin-left: auto;
        }
        .file-link {
            display: block;
            margin-top: 8px;
            padding: 6px 10px;
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 5px;
            text-decoration: none;
            color: #007bff;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
        }
        .file-link i {
            margin-right: 5px;
        }
        .message-image, .message-video {
            margin-bottom: 10px;
            cursor: pointer;
        }
        .message-image img, .message-video video {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .lightbox-modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
            align-items: center;
            justify-content: center;
        }
        .lightbox-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
        }
        .lightbox-content.video {
            width: auto;
            height: auto;
        }
        .lightbox-caption {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            text-align: center;
            color: #ccc;
            padding: 10px 0;
            height: 150px;
        }
        .lightbox-close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }
        .lightbox-close:hover,
        .lightbox-close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .chat-body::-webkit-scrollbar { width: 6px; }
        .chat-body::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.05); border-radius: 10px; }
        .chat-body::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.2); border-radius: 10px; }
        .chat-body::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.3); }
        .context-menu {
            position: absolute;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            list-style: none;
            padding: 8px 0;
        }
        .context-menu .list-group-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 14px;
        }
        .context-menu .list-group-item:hover {
            background-color: #f0f0f0;
        }
        .edited-tag {
            font-size: 0.6rem;
            margin-left: 5px;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php
        $navbar_path = __DIR__ . '/../layout/admin/parte1.php';
        if (file_exists($navbar_path)) {
            include_once $navbar_path;
        } else {
            echo '
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <div class="container">
                    <a href="' . APP_URL . '" class="navbar-brand">
                        <span class="brand-text font-weight-light">' . APP_NAME . '</span>
                    </a>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="' . APP_URL . '/admin">
                                <i class="fas fa-home"></i> Volver al inicio
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>';
        }
        ?>

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Chat Interno</h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="chat-container">
                                        <div class="contacts-list">
                                            <div class="p-3 border-bottom">
                                                <input type="text" class="form-control" placeholder="Buscar contacto..." id="searchContact">
                                            </div>
                                            <div id="contactsContainer">
                                                <div class="p-3 text-center text-muted">
                                                    <i class="fas fa-spinner fa-spin"></i> Cargando contactos...
                                                </div>
                                            </div>
                                        </div>

                                        <div class="chat-messages">
                                            <div id="chatHeader" class="chat-header">
                                                <div class="d-flex align-items-center">
                                                    <div class="contact-avatar">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div class="ml-2">
                                                        <div class="contact-name">Selecciona un contacto</div>
                                                        <div class="contact-email">Para comenzar a chatear</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="chatBody" class="chat-body">
                                                <div class="chat-empty-state">
                                                    <i class="fas fa-comments"></i>
                                                    <p>Selecciona un contacto para comenzar a chatear</p>
                                                </div>
                                            </div>

                                            <div class="message-input-container d-none" id="messageInputContainer">
                                                <input type="text" id="messageText" class="message-input" placeholder="Escribe un mensaje..." autocomplete="off">
                                                <input type="file" id="fileInput" style="display: none;" />
                                                <button id="attachFileBtn" class="attach-button" title="Adjuntar archivo">
                                                    <i class="fas fa-paperclip"></i>
                                                </button>
                                                <button id="sendMessageBtn" class="send-button">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div id="lightboxModal" class="lightbox-modal">
        <span class="lightbox-close">&times;</span>
        <div style="display: flex; align-items: center; justify-content: center; height: 100%; position: relative;">
            <img class="lightbox-content" id="lightboxImage" src="" alt="">
            <video class="lightbox-content video" id="lightboxVideo" src="" controls style="display: none;"></video>
            <a id="downloadBtn" href="#" download class="btn btn-primary" style="position: absolute; bottom: 20px; right: 20px; display: none;">
                <i class="fas fa-download"></i> Descargar
            </a>
        </div>
    </div>

    <script src="<?= APP_URL ?>/public/plugins/jquery/jquery.min.js"></script>
    <script src="<?= APP_URL ?>/public/dist/js/adminlte.min.js"></script>
    <script src="<?= APP_URL ?>/public/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        let currentContactId = null;
        let refreshInterval;
        const CURRENT_USER_ID = '<?= (int)$current_user_id ?>';
        const MAX_FILE_SIZE_MB = 16;
        const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;
        const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'jfif', 'png', 'gif', 'webp', 'tiff', 'svg'];
        const VIDEO_EXTENSIONS = ['mp4', 'webm', 'ogg']; 
        
        // Función para escapar HTML y prevenir XSS
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        $(document).ready(function () {
            loadContacts();

            function loadContacts() {
                $.ajax({
                    url: '<?= APP_URL ?>/app/controllers/chat.php?action=get_contacts',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            renderContacts(response.contacts);
                            fetchUnreadCounts();
                        } else {
                            $('#contactsContainer').html('<div class="p-3 text-center text-danger">Error al cargar contactos</div>');
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#contactsContainer').html('<div class="p-3 text-center text-danger">Error de conexión</div>');
                    }
                });
            }

            function fetchUnreadCounts() {
                $.ajax({
                    url: '<?= APP_URL ?>/app/controllers/chat.php?action=get_unread_count',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Actualizar el conteo total en alguna parte de la interfaz si es necesario
                            // Por ejemplo, en un badge junto al icono del chat principal
                            // console.log('Total unread messages:', response.total_unread);

                            // Actualizar badges de no leídos por contacto
                            $('.contact-item').each(function() {
                                const contactId = $(this).data('contact-id');
                                const unreadCount = response.unread_by_contact[contactId] || 0;
                                let badge = $(this).find('.unread-badge');
                                if (unreadCount > 0) {
                                    if (badge.length === 0) {
                                        badge = $('<span class="unread-badge"></span>');
                                        $(this).append(badge);
                                    }
                                    badge.text(unreadCount);
                                } else {
                                    if (badge.length > 0) {
                                        badge.remove();
                                    }
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al obtener conteos de no leídos:', error);
                    }
                });
            }

            function renderContacts(contacts) {
                const contactsContainer = $('#contactsContainer');
                contactsContainer.empty();
                if (contacts.length === 0) {
                    contactsContainer.html('<div class="p-3 text-center text-muted">No hay contactos disponibles</div>');
                    return;
                }
                contacts.forEach(contact => {
                    const displayName = contact.nombres && contact.apellidos ? `${contact.nombres} ${contact.apellidos}` : contact.email;
                    const contactHtml = `<div class="contact-item" data-contact-id="${contact.id_usuario}">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    <div class="contact-avatar">${displayName.charAt(0).toUpperCase()}</div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="contact-name">${escapeHtml(displayName)}</div>
                                                    <div class="contact-email">${escapeHtml(contact.email)}</div>
                                                </div>
                                            </div>
                                        </div>`;
                    contactsContainer.append(contactHtml);
                });
                $('.contact-item').off('click').on('click', function() {
                    $('.contact-item').removeClass('active');
                    $(this).addClass('active');
                    const contactId = $(this).data('contact-id');
                    selectContact(contactId);
                });
                
                // Configurar la búsqueda de contactos
                $('#searchContact').off('input').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    $('.contact-item').each(function() {
                        const contactName = $(this).find('.contact-name').text().toLowerCase();
                        const contactEmail = $(this).find('.contact-email').text().toLowerCase();
                        if (contactName.includes(searchTerm) || contactEmail.includes(searchTerm)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });
            }

            function selectContact(contactId) {
                currentContactId = contactId;
                $('#messageInputContainer').removeClass('d-none');
                
                const contactElement = $(`.contact-item[data-contact-id="${contactId}"]`);
                const contactName = contactElement.find('.contact-name').text();
                const contactEmail = contactElement.find('.contact-email').text();
                
                // Actualizar el encabezado del chat
                $('#chatHeader').html(`
                    <div class="d-flex align-items-center">
                        <div class="contact-avatar">${contactName.charAt(0).toUpperCase()}</div>
                        <div class="ml-2">
                            <div class="contact-name">${escapeHtml(contactName)}</div>
                            <div class="contact-email">${escapeHtml(contactEmail)}</div>
                        </div>
                    </div>
                `);

                // Limpiar el intervalo de refresco anterior y crear uno nuevo
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
                loadMessages(contactId);
                refreshInterval = setInterval(() => {
                    loadMessages(contactId);
                }, 3000);

                // Quitar el badge de no leídos de inmediato al seleccionar el contacto
                const unreadBadge = contactElement.find('.unread-badge');
                if (unreadBadge.length > 0) {
                    unreadBadge.remove();
                }
            }

            function loadMessages(contactId) {
                const messagesContainer = $('#chatBody');
                const scrollPosition = messagesContainer[0].scrollHeight - messagesContainer.scrollTop();
                const shouldScrollToBottom = scrollPosition <= messagesContainer.height() + 20;

                $.ajax({
                    url: '<?= APP_URL ?>/app/controllers/chat.php?action=get_messages&contact_id=' + contactId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            renderMessages(response.messages);
                            if (shouldScrollToBottom) {
                                messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
                            }
                        } else {
                            console.error('Error al cargar mensajes:', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la solicitud:', error);
                    }
                });
            }

            function renderMessages(messages) {
                const messagesContainer = $('#chatBody');
                messagesContainer.empty();
                
                if (messages.length === 0) {
                    messagesContainer.html('<div class="chat-empty-state"><i class="fas fa-comment-slash"></i><p>No hay mensajes aún</p></div>');
                    return;
                }

                messages.forEach(message => {
                    const isSent = message.id_remitente == CURRENT_USER_ID;
                    const messageTime = new Date(message.fecha_envio).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    
                    let statusIcon = '';
                    if (isSent) {
                        if (message.leido && parseInt(message.leido) === 1) { 
                            statusIcon = '<i class="fas fa-check-double message-status read-double"></i>';
                        } else {
                            statusIcon = '<i class="fas fa-check message-status sent-single"></i>';
                        }
                    }
                    
                    let editedTag = (message.editado && message.editado === '1') ? '<span class="edited-tag">(editado)</span>' : '';

                    let messageContent = '';
                    if (message.archivo && message.archivo.trim() !== '') {
                        const fileExtension = message.archivo.split('.').pop().toLowerCase();
                        const filePath = `<?= APP_URL ?>/${message.archivo}`;
                        const fileName = message.archivo.substring(message.archivo.lastIndexOf('/') + 1);

                        if (IMAGE_EXTENSIONS.includes(fileExtension)) {
                            messageContent += `<div class="message-image" data-file-url="${filePath}" data-file-type="image">
                                                    <img src="${filePath}" alt="Imagen enviada" style="max-width: 250px;">
                                                </div>`;
                        } else if (VIDEO_EXTENSIONS.includes(fileExtension)) {
                            messageContent += `<div class="message-video" data-file-url="${filePath}" data-file-type="video">
                                                    <video src="${filePath}" controls preload="metadata" style="max-width: 250px; border-radius: 8px;"></video>
                                                </div>`;
                        } else {
                            messageContent += `<a href="${filePath}" target="_blank" class="file-link">
                                                    <i class="fas fa-file-download"></i> ${escapeHtml(fileName)}
                                                </a>`;
                        }
                    }
                    
                    if (message.mensaje && message.mensaje.trim() !== '') {
                         if (messageContent !== '') {
                                messageContent += `<div class="message-text" style="margin-top: 10px;">${escapeHtml(message.mensaje)}</div>`;
                         } else {
                             messageContent += `<div class="message-text">${escapeHtml(message.mensaje)}</div>`;
                         }
                    }
                    
                    const messageHtml = `
                        <div class="message ${isSent ? 'sent' : 'received'}" data-message-id="${message.id_mensaje}">
                            <div class="message-content">
                                ${messageContent}
                                <div class="message-info">
                                    <span class="message-time">${messageTime}</span>
                                    ${editedTag}
                                    ${statusIcon}
                                </div>
                            </div>
                        </div>
                    `;
                    messagesContainer.append(messageHtml);
                });
                
                // Eventos de click para el lightbox
                $('.message-image, .message-video').off('click').on('click', function(e) {
                    e.preventDefault();
                    if ($(e.target).is('video')) {
                        return;
                    }
                    const fileUrl = $(this).data('file-url');
                    const fileType = $(this).data('file-type');
                    
                    $('#lightboxImage').hide();
                    $('#lightboxVideo').hide();
                    $('#downloadBtn').hide();

                    if (fileType === 'image') {
                        $('#lightboxImage').attr('src', fileUrl).show();
                        $('#downloadBtn').attr('href', fileUrl).show();
                    } else if (fileType === 'video') {
                        $('#lightboxVideo').attr('src', fileUrl).show();
                        $('#lightboxVideo')[0].load(); 
                        $('#downloadBtn').attr('href', fileUrl).show();
                    }
                    $('#lightboxModal').css('display', 'flex'); 
                });

                // Context menu para mensajes propios
                $('.message.sent').off('contextmenu').on('contextmenu', function(e) {
                    e.preventDefault();
                    const messageId = $(this).data('message-id');
                    showContextMenu(e.pageX, e.pageY, messageId);
                });
            }

            function showContextMenu(x, y, messageId) {
                $('.context-menu').remove();
                
                const contextMenu = $(`
                    <ul class="context-menu list-group" data-message-id="${messageId}" style="position: absolute; top: ${y}px; left: ${x}px; z-index: 1000; min-width: 150px;">
                        <li class="list-group-item list-group-item-action edit-message"><i class="fas fa-edit"></i> Editar</li>
                        <li class="list-group-item list-group-item-action delete-for-me"><i class="fas fa-trash-alt"></i> Eliminar para mí</li>
                        <li class="list-group-item list-group-item-action delete-for-all"><i class="fas fa-trash"></i> Eliminar para todos</li>
                    </ul>
                `);
                $('body').append(contextMenu);

                $(document).off('click').on('click', function(e) {
                    if (!$(e.target).closest('.context-menu').length) {
                        $('.context-menu').remove();
                        $(document).off('click');
                    }
                });

                $('.edit-message').off('click').on('click', function() {
                    $('.context-menu').remove();
                    const messageElement = $(`.message[data-message-id="${messageId}"]`);
                    const currentMessageText = messageElement.find('.message-text').text();

                    Swal.fire({
                        title: 'Editar mensaje',
                        input: 'textarea',
                        inputValue: currentMessageText,
                        inputAttributes: {
                            'aria-label': 'Escribe tu nuevo mensaje'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            editMessage(messageId, result.value);
                        }
                    });
                });

                $('.delete-for-me').off('click').on('click', function() {
                    $('.context-menu').remove();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Este mensaje se eliminará solo para ti.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteMessage(messageId, false);
                        }
                    });
                });

                $('.delete-for-all').off('click').on('click', function() {
                    $('.context-menu').remove();
                    Swal.fire({
                        title: '¿Eliminar para todos?',
                        text: "Esta acción no se puede revertir. El mensaje se eliminará para ti y para el destinatario.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteMessage(messageId, true);
                        }
                    });
                });
            }

            function deleteMessage(messageId, deleteForAll) {
                $.ajax({
                    url: '<?= APP_URL ?>/app/controllers/chat.php?action=delete_message',
                    type: 'POST',
                    data: {
                        message_id: messageId,
                        delete_for_all: deleteForAll ? 'true' : 'false'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Éxito', response.message, 'success');
                            loadMessages(currentContactId);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo eliminar el mensaje.', 'error');
                    }
                });
            }

            function editMessage(messageId, newMessage) {
                $.ajax({
                    url: '<?= APP_URL ?>/app/controllers/chat.php?action=edit_message',
                    type: 'POST',
                    data: {
                        message_id: messageId,
                        new_message: newMessage
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Éxito', response.message, 'success');
                            loadMessages(currentContactId);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo editar el mensaje.', 'error');
                    }
                });
            }
            
            // Evento de click para adjuntar archivo
            $('#attachFileBtn').click(function() {
                $('#fileInput').click();
            });

            $('#sendMessageBtn').click(sendMessage);
            $('#messageText').keypress(function(e) {
                if (e.which === 13 && ($('#messageText').val().trim() !== '' || $('#fileInput')[0].files.length > 0)) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            function sendMessage() {
                const messageText = $('#messageText').val().trim();
                const fileInput = $('#fileInput')[0];
                const file = fileInput.files[0];
                
                if (!messageText && !file || !currentContactId) {
                    return;
                }

                if (file && file.size > MAX_FILE_SIZE_BYTES) {
                    Swal.fire('Error', `El archivo es demasiado grande. El tamaño máximo es de ${MAX_FILE_SIZE_MB}MB.`, 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('destinatario_id', currentContactId);
                formData.append('mensaje', messageText);
                if (file) {
                    formData.append('file', file);
                }

                $.ajax({
                    url: '<?= APP_URL ?>/app/controllers/chat.php?action=send_message',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#messageText').val('');
                            $('#fileInput').val('');
                            loadMessages(currentContactId);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Error al enviar el mensaje.', 'error');
                        console.error('Error al enviar mensaje:', error);
                    }
                });
            }
        });
    </script>
</body>
</html>