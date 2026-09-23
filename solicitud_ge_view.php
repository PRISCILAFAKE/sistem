<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Controller/solicitud_ge_controller.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gerencia - Requerimientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
    </style>
</head>
<body class="bg-light">

<div style="background: #333; color: #fff; padding: 12px 20px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
    <div><strong>Gerencia:</strong> <?php echo htmlspecialchars($_SESSION['user'] ?? 'Gerente'); ?></div>
    <div><a href="../Controller/logout.php" class="btn btn-sm btn-danger">Cerrar Sesión</a></div>
</div>

<div class="container mt-4">
    <h2>Requerimientos Pendientes de Aprobación</h2>

    <?php if (isset($_GET['rechazado'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            El requerimiento fue rechazado correctamente y devuelto al Solicitante.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['aprobado'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            El requerimiento fue aceptado y enviado a Administración correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Ocurrió un error: <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <div class="overflow-y-auto" style="max-height: 600px;">            
                <div class="table-responsive">
                    <div class="overflow-y-auto" style="max-width: 1500px;">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Nombre Solicitud</th>
                                <th>Urgencia</th>
                                <th>Estado</th>
                                <th class="text-center">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($requerimientos)): ?>
                                <?php foreach ($requerimientos as $req): ?>
                                    <tr>
                                        <td><?php echo $req['id_requerimiento']; ?></td>
                                        <td><?php echo htmlspecialchars($req['fecha']); ?></td>
                                        <td><strong><?php echo htmlspecialchars($req['nombre_solicitud']); ?></strong></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($req['nombre_urgencia'] ?? 'Normal'); ?></span></td>
                                        <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($req['estado'] ?? 'registrado'); ?></span></td>
                                        
                                        <td class="text-center">

                                        <form action="../Controller/gerencia_acciones_controller.php" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea aceptar este requerimiento?');">
                                            <input type="hidden" name="id_requerimiento" value="<?php echo $req['id_requerimiento']; ?>">
                                            <input type="hidden" name="accion" value="aceptar">
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Aceptar y pasar al Administrador">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalRechazo-<?php echo $req['id_requerimiento']; ?>" title="Rechazar requerimiento">
                                            <i class="bi bi-x-lg"></i>
                                        </button>

                                        <a href="../View/formvis_ge_view.php?id=<?php echo $req['id_requerimiento']; ?>" class="btn btn-outline-primary btn-sm" title="Ver formulario">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <div class="modal fade" id="modalRechazo-<?php echo $req['id_requerimiento']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                     <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Rechazar Requerimiento - <?php echo htmlspecialchars($req['nombre_solicitud']); ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                     </div>
                                                     <div class="modal-body text-start">
                                                        <form action="../Controller/gerencia_acciones_controller.php" method="POST">
                                                            <input type="hidden" name="id_requerimiento" value="<?php echo $req['id_requerimiento']; ?>">
                                                            <input type="hidden" name="accion" value="rechazar">

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold text-secondary">Motivo del rechazo:</label>
                                                                <textarea class="form-control" name="motivo_rechazo" rows="4" placeholder="Escriba el motivo detallado..." required></textarea>
                                                            </div>

                                                            <div class="modal-footer px-0 pb-0">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
                                                            </div>
                                                        </form>    
                                                     </div>
                                                </div>
                                            </div>
                                        </div>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No se encontraron requerimientos registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>