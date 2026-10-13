al/Views/inventarioAlmacen.php

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Config/conexion.php';

try {
    // Obtener empresas para el filtro
    $stmtEmpresas = $conexion->query("SELECT id, razon_social_emp FROM gc_empresas ORDER BY razon_social_emp ASC");
    $empresas = $stmtEmpresas->fetchAll(PDO::FETCH_ASSOC);

    // Obtener estados para el filtro
    $stmtEstados = $conexion->query("SELECT id, nombre FROM estado_producto ORDER BY id ASC");
    $estados = $stmtEstados->fetchAll(PDO::FETCH_ASSOC);

    // Consulta de productos con los nombres reales de tus columnas
    $sql = "SELECT p.*, e.razon_social_emp, est.nombre AS estado_nombre, tp.nombre AS tipo_nombre 
            FROM producto p 
            LEFT JOIN gc_empresas e ON p.id_empresa_fk = e.id 
            LEFT JOIN estado_producto est ON p.id_estado_fk = est.id 
            LEFT JOIN tipo_producto tp ON p.tipo_producto_fk = tp.id 
            ORDER BY p.id DESC";
    $stmt = $conexion->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $empresas = [];
    $estados = [];
    $productos = [];
}
?>




remplazar la tabla estatica
<tbody id="tablaInventario">
    <?php if (empty($productos)): ?>
        <tr>
            <td colspan="7" class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                No hay productos registrados en el inventario.
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($productos as $prod): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($prod['codigo'] ?? '#' . $prod['id']); ?></strong></td>
                <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                <td><?php echo htmlspecialchars($prod['marca']); ?></td>
                <td><?php echo htmlspecialchars($prod['razon_social_emp'] ?? 'Sin empresa'); ?></td>
                <td>
                    <span class="badge bg-success-subtle text-success px-2 py-1">
                        <?php echo htmlspecialchars($prod['estado_nombre'] ?? 'Activo'); ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($prod['tipo_nombre'] ?? 'General'); ?></td>
                <td class="text-center">
                    <a href="editarProducto.php?id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>










al/Views/personalAlmacen.php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Config/conexion.php';

try {
    // Obtener empresas asociadas
    $stmtEmp = $conexion->query("SELECT id, ruc_emp, razon_social_emp FROM gc_empresas ORDER BY id ASC");
    $lista_empresas = $stmtEmp->fetchAll(PDO::FETCH_ASSOC);

    // Obtener trabajadores con su empresa y área correspondiente (remplazando sede por empresa)
    $stmtTrab = $conexion->query("SELECT t.*, e.razon_social_emp, a.nombre_area 
                                  FROM trabajador t 
                                  LEFT JOIN gc_empresas e ON t.id_empresa_fk = e.id 
                                  LEFT JOIN areas a ON t.id_area_fk = a.id_area 
                                  ORDER BY t.id DESC");
    $lista_trabajadores = $stmtTrab->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $lista_empresas = [];
    $lista_trabajadores = [];
}
?>


tabla empresas asociadas:
<tbody>
    <?php if (empty($lista_empresas)): ?>
        <tr>
            <td colspan="3" class="text-center text-muted py-5">
                <i class="bi bi-building fs-2 d-block mb-2 opacity-50"></i>
                No hay Empresas registradas.
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($lista_empresas as $emp): ?>
            <tr>
                <td><?php echo $emp['id']; ?></td>
                <td><?php echo htmlspecialchars($emp['ruc_emp']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($emp['razon_social_emp']); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>


tabla trbajadpres:
<tbody>
    <?php if (empty($lista_trabajadores)): ?>
        <tr>
            <td colspan="3" class="text-center text-muted py-5">
                <i class="bi bi-people fs-2 d-block mb-2 opacity-50"></i>
                No hay trabajadores registrados.
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($lista_trabajadores as $trab): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($trab['nombres'] ?? ''); ?></strong><br>
                    <small class="text-muted">DNI: <?php echo htmlspecialchars($trab['DNI'] ?? 'N/A'); ?></small>
                </td>
                <td><?php echo htmlspecialchars($trab['razon_social_emp'] ?? 'Sin asignar'); ?></td>
                <td class="text-center">
                    <span class="badge bg-light text-dark"><?php echo htmlspecialchars($trab['nombre_area'] ?? 'Sin área'); ?></span>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>









