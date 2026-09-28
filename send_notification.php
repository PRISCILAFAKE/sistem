<?php
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

        // Destinatarios
        $mail->setFrom(SMTP_USER, 'Sistema de Tickets');
        $mail->addAddress(ADMIN_EMAIL, ADMIN_NAME);

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

// También crear tabla de historial si no existe
function createHistoryTable() {
    $conn = getConnection();
    $sql = "CREATE TABLE IF NOT EXISTS tck_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT NOT NULL,
        accion VARCHAR(255),
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
        INDEX idx_ticket_id (ticket_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $conn->query($sql);
}

// Ejecutar al incluir
createHistoryTable();
?>
