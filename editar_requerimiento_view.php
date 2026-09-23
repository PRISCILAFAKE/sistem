<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/conexion.php';

$id_requerimiento = $_GET['id'] ?? null;
if (!$id_requerimiento) {
    header("Location: mis_requerimientos_view.php");
    exit();
}

$sql = "SELECT r.*, p.estado 
        FROM requerimientos r 
        LEFT JOIN procesos p ON r.id_requerimiento = p.id_requerimiento_fk 
        WHERE r.id_requerimiento = :id_req";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id_req' => $id_requerimiento]);
$req = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$req) {
    die("Requerimiento no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Requerimiento Rechazado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">

<h2>Editar y Reenviar Requerimiento - <?php echo htmlspecialchars($req['nombre_solicitud']); ?></h2>
    
    <?php if (!empty($req['motivo_rechazo'])): ?>
        <div class="alert alert-danger mt-3" role="alert">
            <h4 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Requerimiento Rechazado por Gerencia!</h4>
            <p><strong>Motivo:</strong> <?php echo htmlspecialchars($req['motivo_rechazo']); ?></p>
            <hr>
            <p class="mb-0">Por favor, corrija los datos indicados y haga clic en "Guardar" para enviarlo nuevamente.</p>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm p-4 mt-3">
        <form action="../Controller/editar_requerimiento_controller.php" method="POST">
            <input type="hidden" name="id_requerimiento" value="<?php echo $req['id_requerimiento']; ?>">

            <div class="mb-3">
                <label class="form-label fw-bold">Nombre de la Solicitud:</label>
                <input type="text" class="form-control" name="nombre_solicitud" value="<?php echo htmlspecialchars($req['nombre_solicitud']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">¿Qué necesita que haga el sistema?</label>
                <textarea class="form-control" name="funcionalidad" rows="3" required><?php echo htmlspecialchars($req['funcionalidad']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">¿Por qué es necesario este cambio?</label>
                <textarea class="form-control" name="necesidad" rows="3" required><?php echo htmlspecialchars($req['necesidad']); ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Usuarios impactados:</label>
                    <input type="number" class="form-control" name="impacto_user" value="<?php echo htmlspecialchars($req['impacto_user']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Áreas afectadas:</label>  <p></p>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="areas_afectadas[]" value="Contabilidad">
                        <label class="form-check-label">Contabilidad</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="areas_afectadas[]" value="Logistica">
                        <label class="form-check-label">Logistica</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="areas_afectadas[]" value="Recursos Humanos">
                        <label class="form-check-label">Recursos Humanos</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="solicitud_view.php" class="btn btn-sm btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>