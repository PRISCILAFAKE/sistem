<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/conexion.php'; 

// Importar clases de PHPMailer
require_once __DIR__ . '/../include/PHPMailer/Exception.php';
require_once __DIR__ . '/../include/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../include/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_requerimiento   = $_POST['id_requerimiento'] ?? null;
    $nuevo_estado       = $_POST['nuevo_estado'] ?? 'programando';
    $fecha_finalizacion = $_POST['fecha_programacion'] ?? ''; 
    $encargado          = $_POST['encargado'] ?? ''; 
    $mensaje_correo     = $_POST['mensaje_correo'] ?? '';

    $nombre_admin       = $_SESSION['user'] ?? 'Administrador';
    $correo_admin       = $_SESSION['correo'] ?? 'agenda@pilsac.com.pe';

    if (!$id_requerimiento) {
        die("Error: No se especificó el ID del requerimiento.");
    }

    try {
        //  Obtener datos del requerimiento, el usuario y el nombre del trabajador asociado
        $sql = "SELECT r.*, u.*, t.nombres AS nombre_solicitante 
                FROM requerimientos r 
                JOIN usuario u ON r.id_usuario_f = u.id_usuario 
                LEFT JOIN trabajador t ON u.id_trabajador_fk = t.id 
                WHERE r.id_requerimiento = ?";
        
        $stmt = $conexion->prepare($sql); 
        $stmt->execute([$id_requerimiento]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$datos) {
            die("Error: No se encontró el requerimiento o el usuario asociado.");
        }

        $correoDestino     = $datos['correo']; 
        $nombreSolicitud   = $datos['nombre_solicitud'];
        $nombreSolicitante = !empty($datos['nombre_solicitante']) ? $datos['nombre_solicitante'] : 'Estimado(a)';

        // Validar que el correo destino sea válido
        if (empty($correoDestino) || !filter_var($correoDestino, FILTER_VALIDATE_EMAIL)) {
            die("Error: El usuario que creó el requerimiento no tiene un correo electrónico válido registrado.");
        }

        // Actualizar el estado, la fecha de finalización y el encargado 
        $updateSql = "UPDATE requerimientos SET estado = ?, fecha_finalizacion = ?, encargado = ? WHERE id_requerimiento = ?";
        $updateStmt = $conexion->prepare($updateSql);
        $updateStmt->execute([$nuevo_estado, $fecha_finalizacion, $encargado, $id_requerimiento]);

        //  Enviar correo usando PHPMailer
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = 'mail.pilsac.com.pe';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'agenda@pilsac.com.pe';        
        $mail->Password   = 'Gts10203040*';    
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // Remitente
        $mail->setFrom('agenda@pilsac.com.pe', 'Equipo de Sistemas');

        // Destinatario
        $mail->addAddress($correoDestino);

        if (!empty($correo_admin)) {
            $mail->addReplyTo($correo_admin, $nombre_admin);
        }

        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $mail->Subject = "Actualización de Requerimiento: #{$id_requerimiento} - Programado";
        
        $mail->Body    = "Hola, <b>{$nombreSolicitante}</b><br><br>"
            . "Su requerimiento '<b>{$nombreSolicitud}</b>' se encuentra En Desarrollo</b>.<br><br>"
            . "<b>Responsable asignado:</b> {$encargado}<br>"
            . "<b>La Fecha tentativa de término es el:</b> {$fecha_finalizacion}<br>"
            . "Atentamente,<br>Equipo de Sistemas";

        $mail->send();

        // Redirección exitosa
        header("Location: ../View/solicitud_crud_view.php?exito=programado");
        exit();

    } catch (PDOException $e) {
        die("Error en la Base de Datos: " . $e->getMessage());
    } catch (Exception $e) {
        die("El estado se actualizó, pero no se pudo enviar el correo: " . $mail->ErrorInfo);
    }
}
?>