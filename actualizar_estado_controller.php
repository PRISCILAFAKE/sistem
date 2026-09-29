<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_requerimiento = $_POST['id_requerimiento'] ?? null;
    $nuevo_estado = $_POST['nuevo_estado'] ?? null;

    if ($id_requerimiento && $nuevo_estado) {
        try {
            $sql = "UPDATE requerimientos SET estado = :estado WHERE id_requerimiento = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':estado' => $nuevo_estado,
                ':id' => $id_requerimiento
            ]);
            
            http_response_code(200);
            echo "Estado actualizado correctamente";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error en base de datos: " . $e->getMessage();
        }
    } else {
        http_response_code(400);
        echo "Faltan parámetros requeridos.";
    }
}
?>