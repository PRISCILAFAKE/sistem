<?php
// Configurar la zona horaria en PHP para Perú
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../Config/conexion.php';

// Forzar a PDO a usar la hora de Perú (-05:00) para las consultas y funciones de fecha de MySQL
if (isset($conexion) && $conexion instanceof PDO) {
    $conexion->exec("SET time_zone = '-05:00'");
}

$termino = isset($_GET['termino']) ? trim($_GET['termino']) : '';
$empresa = isset($_GET['empresa']) ? trim($_GET['empresa']) : '';

try {
    $sql = "SELECT id, nombres, correoCorporativo, contrasenia, empresa, estado, fechaRegistro 
            FROM gc_drive_cuentas 
            WHERE 1=1";
    
    $params = [];

    if (!empty($empresa)) {
        $sql .= " AND empresa = :empresa";
        $params[':empresa'] = $empresa;
    }

    if (!empty($termino)) {
        $sql .= " AND (nombres LIKE :ter OR correoCorporativo LIKE :ter OR empresa LIKE :ter)";
        $params[':ter'] = "%$termino%";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($cuentas) > 0) {
        foreach ($cuentas as $row) {
            $badgeColor = ($row['estado'] === 'activo') ? 'bg-success' : 'bg-secondary';
            $btnEstadoColor = ($row['estado'] === 'activo') ? 'btn-outline-secondary' : 'btn-outline-success';
            $btnEstadoIcon = ($row['estado'] === 'activo') ? 'fa-toggle-on' : 'fa-toggle-off';
            $btnEstadoTitle = ($row['estado'] === 'activo') ? 'Desactivar cuenta' : 'Activar cuenta';

            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nombres']) . "</td>";
            echo "<td>" . htmlspecialchars($row['correoCorporativo']) . "</td>";
            echo "<td>" . htmlspecialchars($row['contrasenia']) . "</td>";
            echo "<td>" . htmlspecialchars($row['empresa']) . "</td>";
            echo "<td><span class='badge {$badgeColor}'>" . htmlspecialchars($row['estado']) . "</span></td>";
            echo "<td>" . htmlspecialchars($row['fechaRegistro']) . "</td>";
            echo "<td class='text-center'>
                    <a href='editar_cuenta_drive.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning mb-1' title='Editar'><i class='fas fa-edit'></i></a>
                    <a href='../Controllers/cambiar_estado_drive.php?id=" . $row['id'] . "' class='btn btn-sm {$btnEstadoColor} mb-1' title='{$btnEstadoTitle}' onclick='return confirm(\"¿Deseas cambiar el estado de esta cuenta?\")'><i class='fas {$btnEstadoIcon}'></i></a>
                    <a href='../Controllers/eliminar_cuenta_drive.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger mb-1' title='Eliminar cuenta' onclick='return confirm(\"¿Estás seguro de eliminar permanentemente esta cuenta?\")'><i class='fas fa-trash-alt'></i></a>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8' class='text-center text-muted py-4'>No se encontraron registros coincidentes.</td></tr>";
    }

} catch (PDOException $e) {
    echo "<tr><td colspan='8' class='text-center text-danger py-4'>Error en la consulta: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
