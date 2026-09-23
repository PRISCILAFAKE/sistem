<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($usuario)) {
    header("Location: ../Controller/perfil_controller.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil y Firma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

<div class="container pb-5 pt-5" style="max-width: 600px;">
    
    <div class="mb-3">
        <a href="../View/solicitud_view.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>

    <div class="card shadow border-0">
        <div class="card-header bg-info text-dark text-center">
            <h4 class="mb-0 fw-bold">Perfil de Usuario</h4>
        </div>
        <div class="card-body p-4">

            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?php echo $_SESSION['tipo_alerta']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['mensaje']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['mensaje'], $_SESSION['tipo_alerta']); ?>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Nombre:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Correo Electrónico:</label>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($usuario['correo']); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Área:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre_area'] ?? 'No asignada'); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Rol:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['rol']); ?>" readonly>
            </div>

            <hr class="my-4">

            <h5 class="fw-bold text-secondary mb-3">Gestión de Firma Digital</h5>

            <div class="text-center mb-4">
                <?php if (!empty($usuario['firma']) && file_exists(__DIR__ . '/../' . str_replace('../', '', $usuario['firma']))): ?>
                    <p class="text-muted mb-1">Firma actual registrada:</p>
                    <div class="p-2 border bg-white d-inline-block rounded shadow-sm">
                        <img src="<?php echo htmlspecialchars('../' . str_replace('../', '', $usuario['firma'])); ?>" alt="Firma del usuario" style="max-height: 100px; max-width: 100%;">
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning py-2 mb-0" role="alert">
                        Aún no has subido una firma digital.
                    </div>
                <?php endif; ?>
            </div>

            <form action="../Controller/perfil_controller.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="firma" class="form-label fw-bold text-secondary">Subir o actualizar firma (Imagen JPG o PNG):</label>
                    <input class="form-control" type="file" name="firma" id="firma" accept="image/png, image/jpeg" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-info fw-bold text-dark">Guardar Firma</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>