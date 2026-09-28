<?php
// Configuración de Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'empresa');
define('DB_PORT', 3306); 

// Crear conexión pasando el puerto como cuarto parámetro (y la base de datos como quinto si deseas, o la seleccionamos abajo)
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

// Crear tabla de tickets si no existe
$table_sql = "CREATE TABLE IF NOT EXISTS tck_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_solicitante VARCHAR(100) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    descripcion TEXT NOT NULL,
    imagen LONGBLOB DEFAULT NULL,
    imagen_tipo VARCHAR(50) DEFAULT NULL,
    prioridad ENUM('Baja', 'Media', 'Alta') DEFAULT 'Baja',
    estado ENUM('Nuevo', 'En Progreso', 'Resuelto', 'Cerrado') DEFAULT 'Nuevo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado),
    INDEX idx_prioridad (prioridad)
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
define('SMTP_HOST', 'mail.grupot-soluciona.com.pe'); // Ajustar si es diferente
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

// Cerrar conexión (se ejecutará al final del script)
// mysqli_close($conn);
?>