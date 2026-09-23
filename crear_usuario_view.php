<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/conexion.php';

try {
    $stmtAreas = $conexion->query("SELECT * FROM areas");
    $areas = $stmtAreas->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $areas = [];
}

try {
    $sqlUsuarios = "SELECT u.id_usuario, t.nombre, u.correo, u.contraseña, u.rol, u.estado, a.nombre_area 
                    FROM usuario u 
                    INNER JOIN trabajador t ON u.id_trabajador_fk = t.id_trabajador 
                    LEFT JOIN areas a ON t.id_area_fk = a.id_area 
                    ORDER BY u.id_usuario DESC";
    $stmtUsuarios = $conexion->query($sqlUsuarios);
    $listaUsuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $listaUsuarios = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container my-5">

        <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Usuario creado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white" style="text-align: center;">
                <h4 class="mb-0">Registrar Nuevo Usuario</h4>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                <form class="row g-3" action="../Controller/crear_usuario_controller.php" method="POST">
                    <div class="col-12">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="col-md-6">
                        <label for="correo" class="form-label">Correo electrónico:</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>

                    <div class="col-md-6">
                        <label for="contrasena" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>

                    <div class="col-md-6">
                        <label for="rol" class="form-label">Rol:</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="">Seleccione un rol</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Gerente">Gerente</option>
                            <option value="Solicitante">Solicitante</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="id_area" class="form-label">Área:</label>
                        <select class="form-select" id="id_area" name="id_area">
                            <option value="">Seleccione un área</option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?php echo $area['id_area']; ?>">
                                    <?php echo htmlspecialchars($area['nombre_area']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white text-center">
                <h4 class="mb-0">Usuarios Registrados</h4>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                <div class="table-responsive" style="max-height: 600px;">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Contraseña</th>
                                <th>Rol</th>
                                <th>Área</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($listaUsuarios)): ?>
                                <?php foreach ($listaUsuarios as $usr): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($usr['id_usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($usr['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($usr['correo']); ?></td>
                                        <td><code><?php echo htmlspecialchars($usr['contraseña']); ?></code></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($usr['rol']); ?></span></td>
                                        <td><?php echo htmlspecialchars($usr['nombre_area'] ?? 'Sin área'); ?></td>
                                        <td>
                                            <?php if (($usr['estado'] ?? 'activo') === 'activo'): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">

                                        <a href="editar_usuario_view.php?id=<?php echo $usr['id_usuario']; ?>" class="btn btn-sm btn-warning text-white mb-1">Editar</a>

                                            <?php if (($usr['estado'] ?? 'activo') === 'activo'): ?>
                                                <a href="../Controller/cambiar_estado_usuario_controller.php?id=<?php echo $usr['id_usuario']; ?>&estado=inactivo" class="btn btn-sm btn-danger mb-1" onclick="return confirm('¿Estás seguro de inhabilitar a este usuario?');">Inhabilitar</a>
                                            <?php else: ?>
                                                <a href="../Controller/cambiar_estado_usuario_controller.php?id=<?php echo $usr['id_usuario']; ?>&estado=activo" class="btn btn-sm btn-success mb-1" onclick="return confirm('¿Estás seguro de habilitar a este usuario?');">Habilitar</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No hay usuarios registrados actualmente.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="../View/solicitud_crud_view.php" class="btn btn-secondary btn-sm">Volver</a>
                </div>  
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>