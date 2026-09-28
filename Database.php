database.php:
<?php
// Configuración de Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'empresa');
define('DB_PORT', 3306); 

// Crear conexión pasando el puerto como cuarto parámetro
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Verificar conexión
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Crear base de datos si no existe (por si acaso)
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
$conn->query($sql);

// Seleccionar base de datos
$conn->select_db(DB_NAME);

// Crear tabla de tickets si no existe (con id_tipo integrado)
$table_sql = "CREATE TABLE IF NOT EXISTS tck_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_solicitante VARCHAR(100) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    descripcion TEXT NOT NULL,
    imagen LONGBLOB DEFAULT NULL,
    imagen_tipo VARCHAR(50) DEFAULT NULL,
    id_tipo INT DEFAULT 1,
    estado ENUM('Nuevo', 'En Progreso', 'Resuelto', 'Cerrado') DEFAULT 'Nuevo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$conn->query($table_sql);

// Crear tabla para múltiples imágenes por ticket
$imagenes_table_sql = "CREATE TABLE IF NOT EXISTS tck_imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    imagen LONGBLOB NOT NULL,
    imagen_tipo VARCHAR(50) NOT NULL,
    FOREIGN KEY (ticket_id) REFERENCES tck_tickets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$conn->query($imagenes_table_sql);

// Configuración de Email SMTP (PHPMailer)
define('SMTP_HOST', 'mail.grupot-soluciona.com.pe'); 
define('SMTP_PORT', 465); // 465 (SSL) o 587 (TLS)
define('SMTP_USER', 'ti@grupot-soluciona.com.pe');
define('SMTP_PASS', 'Gts@007*/');
define('ADMIN_EMAIL', 'cortezpompapriscila@gmail.com'); 
define('ADMIN_NAME', 'Admin Soporte TI');

// Función auxiliar: obtener conexión
function getConnection() {
    global $conn;
    return $conn;
}
?>


créate_tickets.php:
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Captura flexible para evitar que el nombre llegue vacío
$nombre = $_POST['nombre'] ?? $_POST['nombres'] ?? $_POST['solicitante'] ?? '';
$celular = $_POST['celular'] ?? '';
$email = $_POST['email'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$id_tipo = $_POST['id_tipo'] ?? ''; 

if (empty($nombre) || empty($celular) || empty($descripcion) || empty($id_tipo)) {
    echo json_encode(['success' => false, 'message' => 'Faltan campos requeridos']);
    exit;
}

$conn = getConnection();
$conn->begin_transaction();

try {
    // 1. Insertar el ticket con id_tipo
    $sql = "INSERT INTO tck_tickets (nombre_solicitante, celular, email, descripcion, id_tipo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $celular, $email, $descripcion, $id_tipo); 
    $stmt->execute();
    $ticket_id = $conn->insert_id;
    $stmt->close();

    // 2. Procesar Múltiples Imágenes (Lógica original con BLOB)
    if (isset($_FILES['imagen'])) {
        $files = $_FILES['imagen'];
        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $check = getimagesize($files['tmp_name'][$i]);
                if ($check !== false) {
                    $img_data = file_get_contents($files['tmp_name'][$i]);
                    $img_tipo = $files['type'][$i];

                    $sql_img = "INSERT INTO tck_imagenes (ticket_id, imagen, imagen_tipo) VALUES (?, ?, ?)";
                    $stmt_img = $conn->prepare($sql_img);
                    $null = NULL;
                    $stmt_img->bind_param("ibs", $ticket_id, $null, $img_tipo);
                    $stmt_img->execute();
                    $stmt_img->close();
                }
            }
        }
    }

    $conn->commit();

    // 3. Enviar Notificación por Email
    require_once __DIR__ . '/../mail/send_notification.php';
    $notificacion = sendNotification($ticket_id, $nombre, $celular, $email, $descripcion, $id_tipo);

    echo json_encode([
        'success' => true, 
        'message' => 'Ticket creado con éxito', 
        'ticket_id' => $ticket_id,
        'email_enviado' => $notificacion['success']
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al crear el ticket: ' . $e->getMessage()]);
}
?>



send_noti.php:    <?php
require_once __DIR__ . '/../config/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/PHPMailer/Exception.php';
require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';

function sendNotification($ticket_id, $nombre, $celular, $email, $descripcion, $id_tipo) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del Servidor
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL (puerto 465)
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Destinatario Principal (Administrador)
        $mail->setFrom(SMTP_USER, 'Sistema de Tickets');
        $mail->addAddress(ADMIN_EMAIL, ADMIN_NAME);

        // --- COPIAS (CC) PARA LOS 3 SUPERVISORES ---
        // (Reemplaza los correos y nombres por los reales de tus supervisores)
        $mail->addCC('supervisor1@grupot-soluciona.com.pe', 'Supervisor 1');
        $mail->addCC('supervisor2@grupot-soluciona.com.pe', 'Supervisor 2');
        $mail->addCC('supervisor3@grupot-soluciona.com.pe', 'Supervisor 3');


        // Contenido del Email
        $mail->isHTML(true);
        $mail->Subject = "Nuevo Ticket #$ticket_id - $descripcion";
        
        $cuerpo = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { background-color: #f9f9f9; padding: 20px; border-radius: 5px; }
                .ticket-box { background: white; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0; }
                .ticket-details { margin: 10px 0; }
                .label { font-weight: bold; color: #007bff; }
                .priority-Software { color: red; font-weight: bold; }
                .priority-Hardware { color: green; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>🎟️ Nuevo Ticket de Soporte</h2>
                <div class='ticket-box'>
                    <div class='ticket-details'>
                        <p><span class='label'>Número de Ticket:</span> #$ticket_id</p>
                        <p><span class='label'>Solicitante:</span> $nombre</p> 
                        <p><span class='label'>Celular:</span> $celular</p>
                        <p><span class='label'>Email:</span> " . ($email ? $email : 'No proporcionado') . "</p>
                        <p><span class='label'>Tipo de Reporte:</span> <span class='priority-$id_tipo'>$id_tipo</span></p>
                        <p><span class='label'>Problema:</span></p>
                        <p style='background: #f0f0f0; padding: 10px; border-radius: 3px;'>$descripcion</p>
                    </div>
                </div>
                <p><strong>Acción:</strong> Comunícate con el solicitante al celular <strong>$celular</strong> para asistencia.</p>
            </div>
        </body>
        </html>";

        $mail->Body = $cuerpo;
        $mail->send();

        // Registrar en BD que notificación fue enviada
        $conn = getConnection();
        $sql = "INSERT INTO tck_history (ticket_id, accion, timestamp) VALUES (?, 'Email enviado correctamente', NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $ticket_id);
            $stmt->execute();
            $stmt->close();
        }

        return array('success' => true, 'message' => 'Email enviado correctamente');

    } catch (Exception $e) {
        // Registrar error en el historial
        $conn = getConnection();
        $error_msg = "Error al enviar email: {$mail->ErrorInfo}";
        $sql = "INSERT INTO tck_history (ticket_id, accion, timestamp) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("is", $ticket_id, $error_msg);
            $stmt->execute();
            $stmt->close();
        }
        return array('success' => false, 'message' => $error_msg);
    }
}

// Crear tabla de historial si no existe
function createHistoryTable() {
    $conn = getConnection();
    $sql = "CREATE TABLE IF NOT EXISTS tck_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT NOT NULL,
        accion VARCHAR(255),
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ticket_id) REFERENCES tck_tickets(id) ON DELETE CASCADE,
        INDEX idx_ticket_id (ticket_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $conn->query($sql);
}

// Ejecutar al incluir
createHistoryTable();
?>

