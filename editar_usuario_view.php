<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/conexion.php';

$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    header("Location: crear_usuario_view.php");
    exit();
}

try {
    $stmt = $conexion->prepare("SELECT u.*, t.nombre AS nombre_trabajador, t.id_area_fk FROM usuario u INNER JOIN trabajador t ON u.id_trabajador_fk = t.id_trabajador WHERE u.id_usuario = :id");
    $stmt->execute([':id' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header("Location: crear_usuario_view.php");
        exit();
    }
} catch (Exception $e) {
    header("Location: crear_usuario_view.php");
    exit();
}

try {
    $stmtAreas = $conexion->query("SELECT * FROM areas");
    $areas = $stmtAreas->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $areas = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container my-5">

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Editar Usuario</h4>
            </div>
            <div class="card-body">
                <form class="row g-3" action="../Controller/editar_usuario_controller.php" method="POST">
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($usuario['id_usuario']); ?>">

                    <div class="col-12">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre_trabajador']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="correo" class="form-label">Correo electrónico:</label>
                        <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="contrasena" class="form-label">Contraseña:</label>
                        <input type="text" class="form-control" id="contrasena" name="contrasena" value="<?php echo htmlspecialchars($usuario['contraseña']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="rol" class="form-label">Rol:</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="Administrador" <?php echo ($usuario['rol'] === 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                            <option value="Gerente" <?php echo ($usuario['rol'] === 'Gerente') ? 'selected' : ''; ?>>Gerente</option>
                            <option value="Solicitante" <?php echo ($usuario['rol'] === 'Solicitante') ? 'selected' : ''; ?>>Solicitante</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="id_area" class="form-label">Área:</label>
                        <select class="form-select" id="id_area" name="id_area">
                            <option value="">Seleccione un área</option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?php echo $area['id_area']; ?>" <?php echo ($usuario['id_area_fk'] == $area['id_area']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($area['nombre_area']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-between">
                        <a href="crear_usuario_view.php" class="btn btn-secondary btn-sm">Cancelar</a>
                        <button type="submit" class="btn btn-warning btn-sm text-dark">Actualizar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>