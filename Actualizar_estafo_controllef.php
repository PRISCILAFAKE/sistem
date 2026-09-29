<?php
// Asegúrate de incluir la conexión a tu base de datos y la configuración de sesión si usas autenticación
require_once("../Config/conexion.php"); // Reemplaza con tu ruta de conexión real

// Validar que la solicitud sea por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recoger y sanitizar los datos enviados desde el formulario/modal
    $id_requerimiento = isset($_POST['id_requerimiento']) ? intval($_POST['id_requerimiento']) : 0;
    $nuevo_estado     = isset($_POST['estado']) ? trim($_POST['estado']) : '';
    $encargado        = isset($_POST['encargado']) ? trim($_POST['encargado']) : null;
    $fecha_finalizacion = isset($_POST['fecha_finalizacion']) ? trim($_POST['fecha_finalizacion']) : null;

    if ($id_requerimiento <= 0 || empty($nuevo_estado)) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos o inválidos.']);
        exit;
    }

    try {
        // 2. Actualizar el estado (y los campos opcionales de programación) en la base de datos
        // Nota: Asegúrate de que tu tabla tenga las columnas 'encargado' y 'fecha_finalizacion' (o ajusta los nombres)
        $sql = "UPDATE tck_tickets SET estado = ?, encargado = ?, fecha_finalizacion = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        
        // Si no es el estado programando, puedes mandar NULL en encargado y fecha si lo prefieres, 
        // o guardarlos tal cual llegan.
        $stmt->bind_param("sssi", $nuevo_estado, $encargado, $fecha_finalizacion, $id_requerimiento);
        
        if ($stmt->execute()) {
            
            // 3. (Opcional pero recomendado) Si el estado es "programando", enviar el correo al solicitante
            if ($nuevo_estado === 'programando' && !empty($encargado)) {
                
                // Consultar el correo del solicitante y detalles del ticket
                $sql_correo = "SELECT correo_solicitante, titulo FROM tck_tickets WHERE id = ?";
                $stmt_correo = $conexion->prepare($sql_correo);
                $stmt_correo->bind_param("i", $id_requerimiento);
                $stmt_correo->execute();
                $resultado = $stmt_correo->get_result();
                
                if ($row = $resultado->fetch_assoc()) {
                    $destinatario = $row['correo_solicitante'];
                    $asunto = "Requerimiento Programado: " . $row['titulo'];
                    
                    // Cuerpo del mensaje (texto plano)
                    $cuerpo = "Hola,\n\n" .
                              "Tu requerimiento ha sido programado.\n" .
                              "Encargado asignado: " . $encargado . "\n" .
                              "Fecha estimada de finalización: " . $fecha_finalizacion . "\n\n" .
                              "Saludos cordiales,\nSistema de Tickets";
                    
                    $cabeceras = "From: noreply@tudominio.com\r\n" .
                                 "Reply-To: soporte@tudominio.com\r\n" .
                                 "X-Mailer: PHP/" . phpversion();
                    
                    // Intentar enviar el correo (asegúrate de que tu servidor local/hosting soporte mail())
                    @mail($destinatario, $asunto, $cuerpo, $cabeceras);
                }
                $stmt_correo->close();
            }

            $stmt->close();
            echo json_encode(['status' => 'success', 'message' => 'Requerimiento actualizado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar en la base de datos.']);
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Excepción: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
}
?>
