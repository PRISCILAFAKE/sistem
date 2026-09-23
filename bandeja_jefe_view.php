<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/conexion.php';

if (!isset($_SESSION['id_usuario']) || empty($_SESSION['es_jefe']) || $_SESSION['es_jefe'] != 1) {
    header("Location: ../index.php");
    exit();
}

$id_area_jefe = $_SESSION['id_area'] ?? null;
$nombre_jefe = $_SESSION['user'] ?? 'Jefe de Área';

try {
    $sql = "SELECT r.*, t.nombre AS nombre_empleado 
            FROM requerimientos r 
            INNER JOIN usuario u ON r.id_usuario_f = u.id_usuario 
            INNER JOIN trabajador t ON u.id_trabajador_fk = t.id_trabajador 
            INNER JOIN areas a ON t.id_area_fk = a.id_area 
            WHERE a.id_area = :id_area AND (r.estado = 'pendiente_jefe' OR r.estado = 'registrado')";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id_area' => $id_area_jefe]);
    $requerimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $requerimientos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandeja del Jefe de Área</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f6f9; 
            margin: 0; 
            padding: 0; 
        }
        .header-bar { 
            background-color: #000000; 
            color: white; 
            padding: 15px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .header-bar span {
            font-size: 16px;
            font-weight: bold;
        }
        .logout { 
            background-color: #e74c3c;
            color: white; 
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none; 
            font-size: 14px;
        }
        .logout:hover {
            background-color: #c0392b;
        }
        .container { 
            max-width: 1200px; 
            margin: 40px auto; 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        }
        h2 { 
            color: #2c3e50; 
            margin-top: 0; 
            margin-bottom: 30px;
            font-size: 24px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            background: white;
            border: 1px solid #e1e8ed;
        }
        th, td { 
            padding: 14px 16px; 
            text-align: left; 
            border-bottom: 1px solid #e1e8ed; 
        }
        th { 
            background-color: #000000; 
            color: white; 
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tr:hover { 
            background-color: #f8fafc; 
        }
        .badge-estado {
            background-color: #00bcd4;
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            text-transform: lowercase;
            display: inline-block;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header-bar">
        <span>Jefe de Área: <?= htmlspecialchars($nombre_jefe) ?></span>
        <a href="../Controller/logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
    </div>

    <div class="container">
        <h2>Requerimientos Pendientes de Aprobación</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Solicitante</th>
                    <th>Nombre Solicitud</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($requerimientos)): ?>
                    <?php foreach ($requerimientos as $req): ?>
                        <?php 
                            $id_req = $req['id_requerimientos'] ?? $req['id_requerimiento'] ?? ''; 
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($id_req) ?></td>
                            <td><?= htmlspecialchars($req['fecha']) ?></td>
                            <td><?= htmlspecialchars($req['nombre_empleado']) ?></td>
                            <td><strong><?= htmlspecialchars($req['nombre_solicitud']) ?></strong></td>
                            <td>
                                <span class="badge-estado"><?= htmlspecialchars($req['estado']) ?></span>
                            </td>
                            <td>

                                <form action="../Controller/aprobar_jefe_controller.php" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de aceptar este requerimiento y enviarlo a gerencia?');">
                                    <input type="hidden" name="id_requerimiento" value="<?= $id_req ?>">
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Aceptar y pasar a Gerencia">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>

                                <form action="../Controller/rechazar_jefe_controller.php" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de rechazar y ELIMINAR permanentemente este requerimiento?');">
                                    <input type="hidden" name="id_requerimiento" value="<?= $id_req ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Rechazar y eliminar">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                           
                                <a href="../View/detalles_requerimiento_view.php?id=<?= $id_req ?>" class="btn btn-outline-primary btn-sm" title="Ver formulario">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 30px;">No hay requerimientos pendientes en tu área.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>