<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/conexion.php';

try {
    $stmtTipo = $conexion->query("SELECT * FROM tipo_solicitud");
    $tipos_solicitud = $stmtTipo->fetchAll(PDO::FETCH_ASSOC);

    $stmtUrgencia = $conexion->query("SELECT * FROM urgencia");
    $urgencias = $stmtUrgencia->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $tipos_solicitud = [];
    $urgencias = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Nuevo Requerimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3">
<div class="container" style="max-width: 600px;">
    <div class="card shadow border-0">
        <div class="card-header bg-info text-dark text-center">
            <h4 class="mb-0 fw-bold">Solicitud de Requerimiento</h4>
        </div>
        <div class="card-body p-4">

            <form action="../Controller/formularioS_controller.php" method="POST" enctype="multipart/form-data"> 

                <div class="mb-3">
                    <label for="usuario_solicitante" class="form-label fw-bold text-secondary">Usuario Solicitante:</label>
                    <input type="text" id="usuario_solicitante" class="form-control" 
                    value="<?php echo htmlspecialchars($_SESSION['user'] ?? ''); ?>"
                    readonly required>
                </div>

                <div class="mb-3">
                    <label for="area_solicitante" class="form-label fw-bold text-secondary">Área Solicitante:</label>
                    <input type="text" id="area_solicitante" name="area_solicitante" class="form-control" 
                    value="<?php echo htmlspecialchars($_SESSION['nombre_area'] ?? 'Área General'); ?>" 
                    readonly required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Nombre Solicitud:</label>
                    <input type="text" name="nombre_solicitud" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Tipo de Solicitud:</label>
                    <div class="col-sm-12">
                        <select class="form-select" name="id_tipo_solicitud_fk" required>
                            <option value="">Seleccione una opción...</option>
                            <?php foreach ($tipos_solicitud as $tipo): ?>
                                <option value="<?php echo $tipo['id_tipos']; ?>">
                                    <?php echo htmlspecialchars($tipo['nombre_s']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Urgencia:</label>
                    <div class="col-sm-12">
                        <select class="form-select" name="id_urgencia_fk" required>
                            <option value="">Seleccione una urgencia...</option>
                            <?php foreach ($urgencias as $urg): ?>
                                <option value="<?php echo $urg['id_urgencia']; ?>">
                                    <?php echo htmlspecialchars($urg['nombre_ur']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Fecha de Solicitud:</label>
                    <input type="date" name="fecha" class="form-control" required>
                </div>

                <h5 class="mb-3">Descripción detallada</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">¿Qué necesitas que haga el sistema?</label>
                    <textarea class="form-control" name="funcionalidad" rows="2" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">¿Por qué es necesario este cambio?</label>
                    <textarea class="form-control" name="necesidad" rows="2" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Usuarios Impactados:</label>
                    <div class="col-sm-10">
                        <input type="number" name="impacto_user" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Archivo adjunto</label>
                    <input class="form-control" type="file" name="archivo">
                </div> 

                <div class="mb-3">
                    <h5 class="mb-3">Áreas afectadas:</h5>
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

                <div class="d-flex justify-content-between border-top pt-3 mt-4">
                    <a href="../View/solicitud_view.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-info fw-bold">Solicitar Requerimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>