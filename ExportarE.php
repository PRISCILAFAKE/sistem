antes de cerrar body 2 mod
<!-- Librería para generar archivos Excel desde JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>


cerca de ls barra de búsqueda 
<button type="button" id="btnExportarExcelInventario" class="btn btn-outline-success rounded-3 shadow-sm px-3 fw-semibold">
    <i class="bi bi-file-earmark-excel me-2"></i>Exportar a Excel
</button>




dentro del script 
document.addEventListener('DOMContentLoaded', function () {
    const btnExportarInv = document.getElementById('btnExportarExcelInventario');
    
    if (btnExportarInv) {
        btnExportarInv.addEventListener('click', function () {
            const tabla = document.getElementById('tablaInventario');
            if (!tabla) return;

            const filas = tabla.querySelectorAll('tbody tr');
            let datosExportar = [];

            // Encabezados del archivo Excel
            datosExportar.push(["ID", "SKU / Código", "Nombre del Producto", "Categoría / Tipo", "Marca", "Estado"]);

            // Recorrer solo las filas que están visibles en pantalla (filtradas)
            filas.forEach(function (fila) {
                if (fila.style.display !== 'none') {
                    const celdas = fila.querySelectorAll('td');
                    if (celdas.length >= 6) {
                        datosExportar.push([
                            celdas[0].innerText.trim(),
                            celdas[1].innerText.trim(),
                            celdas[2].innerText.trim(),
                            celdas[3].innerText.trim(),
                            celdas[4].innerText.trim(),
                            celdas[5].innerText.trim()
                        ]);
                    }
                }
            });

            // Generar libro y hoja de cálculo en Excel
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(datosExportar);

            // Definir ancho automático para las columnas
            ws['!cols'] = [{wch: 8}, {wch: 18}, {wch: 30}, {wch: 20}, {wch: 18}, {wch: 15}];

            XLSX.utils.book_append_sheet(wb, ws, "Inventario");

            // Descargar el archivo con la fecha de hoy
            const fecha = new Date().toISOString().split('T')[0];
            XLSX.writeFile(wb, `Inventario_Almacen_${fecha}.xlsx`);
        });
    }
});








al final de movimientoz almacén 
document.addEventListener('DOMContentLoaded', function () {
    const btnExportarInv = document.getElementById('btnExportarExcelInventario');
    
    if (btnExportarInv) {
        btnExportarInv.addEventListener('click', function () {
            const tabla = document.getElementById('tablaInventario');
            if (!tabla) return;

            const filas = tabla.querySelectorAll('tbody tr');
            let datosExportar = [];

            // Encabezados del archivo Excel
            datosExportar.push(["ID", "SKU / Código", "Nombre del Producto", "Categoría / Tipo", "Marca", "Estado"]);

            // Recorrer solo las filas que están visibles en pantalla (filtradas)
            filas.forEach(function (fila) {
                if (fila.style.display !== 'none') {
                    const celdas = fila.querySelectorAll('td');
                    if (celdas.length >= 6) {
                        datosExportar.push([
                            celdas[0].innerText.trim(),
                            celdas[1].innerText.trim(),
                            celdas[2].innerText.trim(),
                            celdas[3].innerText.trim(),
                            celdas[4].innerText.trim(),
                            celdas[5].innerText.trim()
                        ]);
                    }
                }
            });

            // Generar libro y hoja de cálculo en Excel
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(datosExportar);

            // Definir ancho automático para las columnas
            ws['!cols'] = [{wch: 8}, {wch: 18}, {wch: 30}, {wch: 20}, {wch: 18}, {wch: 15}];

            XLSX.utils.book_append_sheet(wb, ws, "Inventario");

            // Descargar el archivo con la fecha de hoy
            const fecha = new Date().toISOString().split('T')[0];
            XLSX.writeFile(wb, `Inventario_Almacen_${fecha}.xlsx`);
        });
    }
});





sl lado del btn retorno 
<!-- BLOQUE DE FILTROS Y BOTÓN DE RETORNO/EXCEL -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white border border-light-subtle">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small text-muted fw-semibold">Filtrar por Estado de Préstamo</label>
            <select id="selectTipoMovimiento" class="form-select rounded-3">
                <option value="TODOS" selected>Todos los movimientos</option>
                <option value="Pendiente">En Préstamo (Sin Retorno)</option>
                <option value="Devuelto">Devueltos</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label small text-muted fw-semibold">Búsqueda Rápida</label>
            <input type="text" id="inputBusquedaMovimiento" class="form-control rounded-3" placeholder="Buscar por producto, SKU, DNI o trabajador..." autocomplete="off">
        </div>

        <div class="col-md-4 text-end d-flex gap-2">
            <button type="button" id="btnExportarExcelMovimientos" class="btn btn-outline-success w-50 py-2 rounded-3 shadow-sm fw-semibold">
                <i class="bi bi-file-earmark-excel me-1"></i>Excel
            </button>
            <button type="button" class="btn btn-info w-50 py-2 rounded-3 shadow-sm text-white fw-semibold" data-bs-toggle="modal" data-bs-target="#modalRetorno">
                <i class="bi bi-arrow-down-left-square me-1"></i>Retorno
            </button>
        </div>
    </div>
</div>














findl movimiento almacén 
// Exportación a Excel en tiempo real para Movimientos
const btnExportarMov = document.getElementById('btnExportarExcelMovimientos');

if (btnExportarMov) {
    btnExportarMov.addEventListener('click', function () {
        const tabla = document.getElementById('tablaMovimientos');
        if (!tabla) return;

        const filas = tabla.querySelectorAll('tbody tr');
        let datosExportar = [];

        // Encabezados
        datosExportar.push(["ID", "Estado de Préstamo", "Producto", "SKU", "Trabajador Responsable", "DNI", "Fechas (Salida / Retorno)"]);

        filas.forEach(function (fila) {
            // Exportar únicamente las filas que están visibles según los filtros aplicados
            if (fila.style.display !== 'none') {
                const celdas = fila.querySelectorAll('td');
                if (celdas.length >= 5) {
                    // Limpieza de saltos de línea y espacios innecesarios
                    const id = celdas[0].innerText.trim();
                    const estado = celdas[1].innerText.trim();
                    
                    // Extraer Producto y SKU de la celda 2
                    const lineasProducto = celdas[2].innerText.split('\n');
                    const producto = lineasProducto[0] ? lineasProducto[0].trim() : '';
                    const sku = lineasProducto[1] ? lineasProducto[1].replace('SKU:', '').trim() : '';

                    // Extraer Trabajador y DNI de la celda 3
                    const lineasTrabajador = celdas[3].innerText.split('\n');
                    const trabajador = lineasTrabajador[0] ? lineasTrabajador[0].trim() : '';
                    const dni = lineasTrabajador[1] ? lineasTrabajador[1].replace('DNI:', '').trim() : '';

                    // Extraer Fechas
                    const fechas = celdas[4].innerText.replace(/\s+/g, ' ').trim();

                    datosExportar.push([id, estado, producto, sku, trabajador, dni, fechas]);
                }
            }
        });

        // Crear libro y descargar
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(datosExportar);

        // Ancho de columnas optimizado
        ws['!cols'] = [{wch: 8}, {wch: 18}, {wch: 28}, {wch: 18}, {wch: 28}, {wch: 15}, {wch: 32}];

        XLSX.utils.book_append_sheet(wb, ws, "Movimientos");

        const fecha = new Date().toISOString().split('T')[0];
        XLSX.writeFile(wb, `Movimientos_Almacen_${fecha}.xlsx`);
    });
}








movimientos almacén 
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Config/conexion.php';

try {
    $stmtTrabajador =$conexion->query("SELECT * FROM trabajador ORDER BY id ASC");
    $trabajador =$stmtTrabajador->fetchAll();

    // Productos disponibles para nueva salida
    $stmtProducto =$conexion->query("SELECT * FROM al_producto ORDER BY id ASC");
    $producto =$stmtProducto->fetchAll();

    $stmtEstadoP =$conexion->query("SELECT * FROM al_estado_producto ORDER BY id ASC");
    $estado_producto =$stmtEstadoP->fetchAll();

    // Productos que están actualmente en préstamo (sin fecha de retorno) para el modal de retorno
    $stmtProdPrestados =$conexion->query("
        SELECT p.*, o.id_trabajador_fk, t.nombres AS trabajador_nombre, t.DNI AS trabajador_dni 
        FROM al_producto p
        INNER JOIN al_orden_salida o ON p.id = o.producto_fk
        LEFT JOIN trabajador t ON o.id_trabajador_fk = t.id
        WHERE o.fecha_retorno IS NULL
        GROUP BY p.id
        ORDER BY p.id ASC
    ");
    $productos_prestados =$stmtProdPrestados->fetchAll();

    // Bitácora de movimientos
    $sql = "SELECT o.*, p.nombre AS producto_nombre, p.codigo, t.nombres AS trabajador_nombre, t.DNI,
                   e.nombre AS estado_retorno_nombre
            FROM al_orden_salida o 
            LEFT JOIN al_producto p ON o.producto_fk = p.id 
            LEFT JOIN trabajador t ON o.id_trabajador_fk = t.id 
            LEFT JOIN al_estado_producto e ON o.estado_retorno_fk = e.id
            ORDER BY o.id DESC";
    $stmtMov = $conexion->query($sql);
    $movimientos =$stmtMov->fetchAll();

} catch (PDOException $e) {$trabajador = [];
    $producto = [];$productos_prestados = [];
    $estado_producto = [];$movimientos = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos - Grupo T-Soluciona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Librería SheetJS para Exportar a Excel -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
</head>
<body class="bg-light">

    <div class="container-fluid">
        <div class="row min-vh-100">
            <!-- Menú Lateral -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-white border-end p-4 sidebar">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success me-2">
                        <i class="bi bi-box-seam fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Grupo T-Soluciona</h6>
                        <small class="text-muted">Almacén de Sistemas</small>
                    </div>
                </div>

                <ul class="nav flex-column gap-1">
                    <li class="nav-item"><a href="centroAlmacen.php" class="nav-link text-secondary px-3 py-2 rounded-3"><i class="bi bi-grid me-2"></i> Centro</a></li>
                    <li class="nav-item"><a href="inventarioAlmacen.php" class="nav-link text-secondary px-3 py-2 rounded-3"><i class="bi bi-boxes me-2"></i> Inventario</a></li>
                    <li class="nav-item"><a href="movimientosAlmacen.php" class="nav-link text-success fw-semibold bg-success bg-opacity-10 px-3 py-2 rounded-3"><i class="bi bi-arrow-left-right me-2"></i> Movimientos</a></li>
                    <li class="nav-item"><a href="personalAlmacen.php" class="nav-link text-secondary px-3 py-2 rounded-3"><i class="bi bi-buildings me-2"></i> Empresas y Personal</a></li> <br>
                    <li class="nav-item"><a href="../../index.php" class="btn btn-success px-5 rounded-pill shadow-sm btn-sm"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>

            <!-- Contenido Principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4">
                <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm mb-4 border border-light-subtle">
                    <div>
                        <h1 class="h4 fw-bold text-dark mb-1">Movimientos y Registro de Salidas</h1>
                        <p class="text-muted small mb-0">Control de entregas de equipos e historial de operaciones de almacén.</p>
                    </div>
                    <button type="button" class="btn btn-success px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="bi bi-plus-lg me-1"></i>Registrar Nueva Salida</button>
                </div>

                <!-- BLOQUE DE FILTROS, EXCEL Y RETORNO -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white border border-light-subtle">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-semibold">Filtrar por Estado</label>
                            <select id="selectTipoMovimiento" class="form-select rounded-3">
                                <option value="TODOS" selected>Todos los movimientos</option>
                                <option value="Pendiente">En Préstamo (Sin Retorno)</option>
                                <option value="Devuelto">Devueltos</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-semibold">Búsqueda Rápida</label>
                            <input type="text" id="inputBusquedaMovimiento" class="form-control rounded-3" placeholder="Buscar por producto, SKU, DNI o trabajador..." autocomplete="off">
                        </div>

                        <div class="col-md-5 text-end d-flex gap-2">
                            <button type="button" id="btnExportarExcelMovimientos" class="btn btn-outline-success w-50 py-2 rounded-3 shadow-sm fw-semibold">
                                <i class="bi bi-file-earmark-excel me-1"></i>Excel
                            </button>
                            <button type="button" class="btn btn-info w-50 py-2 rounded-3 shadow-sm text-white fw-semibold" data-bs-toggle="modal" data-bs-target="#modalRetorno">
                                <i class="bi bi-arrow-down-left-square me-1"></i>Retorno
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Nueva Salida -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true"> 
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title"><i class="bi bi-arrow-up-right-square me-2"></i> Nueva Salida</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="../Controllers/guardarSalidaController.php" method="POST" id="formNuevaSalida">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="col-form-label fw-bold">Producto:</label>
                                        <input type="text" class="form-control" list="datalistProductos" id="inputBusquedaProducto" placeholder="Escriba para buscar por nombre, marca o SKU..." autocomplete="off" required>
                                        <datalist id="datalistProductos">
                                            <?php foreach ($producto as$prod): ?>
                                                <option data-id="<?php echo $prod['id']; ?>" value="<?php echo htmlspecialchars($prod['nombre'] . ' - ' .$prod['marca'] . ' (' . ($prod['codigo'] ?? 'SKU-' .$prod['id']) . ')'); ?>">
                                            <?php endforeach; ?>
                                        </datalist>
                                        <input type="hidden" name="producto_fk" id="producto_fk_hidden" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="col-form-label fw-bold">Fecha de Salida:</label>
                                        <input type="date" class="form-control" name="fecha_salida" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="col-form-label fw-bold">Trabajador Asignado:</label>
                                        <input type="text" class="form-control" list="datalistTrabajadores" id="inputBusquedaTrabajador" placeholder="Escriba para buscar por DNI o nombres..." autocomplete="off" required>
                                        <datalist id="datalistTrabajadores">
                                            <?php foreach ($trabajador as$trab): ?>
                                                <option data-id="<?php echo $trab['id']; ?>" value="<?php echo htmlspecialchars($trab['nombres'] . ' - DNI: ' .$trab['DNI']); ?>">
                                            <?php endforeach; ?>
                                        </datalist>
                                        <input type="hidden" name="id_trabajador_fk" id="id_trabajador_fk_hidden" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning text-white">Continuar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Registrar Retorno -->
                <div class="modal fade" id="modalRetorno" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title"><i class="bi bi-arrow-down-left-square me-2"></i> Registrar Retorno de Equipo</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="../Controllers/guardarRetornoController.php" method="POST" id="formRetorno">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="col-form-label fw-bold">Producto en Préstamo que Retorna:</label>
                                        <input type="text" class="form-control" list="datalistProductosRetorno" id="inputBusquedaProductoRetorno" placeholder="Seleccione equipo actualmente fuera..." autocomplete="off" required>
                                        <datalist id="datalistProductosRetorno">
                                            <?php foreach ($productos_prestados as$prod): ?>
                                                <option data-id="<?php echo $prod['id']; ?>" 
                                                        data-worker-name="<?php echo htmlspecialchars(($prod['trabajador_nombre'] ?? 'Sin asignar') . (!empty($prod['trabajador_dni']) ? ' - DNI: ' .$prod['trabajador_dni'] : '')); ?>"
                                                        value="<?php echo htmlspecialchars($prod['nombre'] . ' - ' .$prod['marca'] . ' (' . ($prod['codigo'] ?? 'SKU-' .$prod['id']) . ')'); ?>">
                                            <?php endforeach; ?>
                                        </datalist>
                                        <input type="hidden" name="producto_fk" id="producto_fk_retorno_hidden" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="col-form-label fw-bold">Fecha de Retorno:</label>
                                        <input type="date" class="form-control" name="fecha_retorno" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="col-form-label fw-bold">Nuevo Estado del Producto:</label>
                                        <select class="form-select" name="nuevo_estado_fk" required>
                                            <option value="">Seleccione estado al retornar...</option>
                                            <?php foreach ($estado_producto as$est): ?>
                                                <option value="<?php echo $est['id']; ?>"><?php echo htmlspecialchars($est['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="col-form-label fw-bold">Trabajador Responsable:</label>
                                        <input type="text" class="form-control bg-light" id="inputBusquedaTrabajadorRetorno" placeholder="Se autocompletará..." autocomplete="off" readonly>
                                        <small class="text-muted">Detectado automáticamente de la salida activa.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-info text-white">Guardar Retorno</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Movimientos -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-dark mb-3">Bitácora de Movimientos</h5>

                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="tablaMovimientos">
                            <thead class="text-muted small text-uppercase bg-light sticky-top">
                                <tr>
                                    <th class="py-3 rounded-start">ID</th>
                                    <th class="py-3">Estado</th>
                                    <th class="py-3">Producto</th>
                                    <th class="py-3">Trabajador Responsable</th>
                                    <th class="py-3">Fechas (Salida / Retorno)</th>
                                    <th class="py-3 text-center rounded-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($movimientos)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-clock-history fs-2 d-block mb-2 opacity-50"></i>
                                            No hay movimientos registrados en el sistema.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($movimientos as$mov): ?>
                                        <?php 
                                            $esDevuelto = !empty($mov['fecha_retorno']); 
                                            $tipoFiltro =$esDevuelto ? 'Devuelto' : 'Pendiente';
                                        ?>
                                        <tr class="fila-movimiento" data-tipo="<?php echo $tipoFiltro; ?>">
                                            <td><strong><?php echo $mov['id']; ?></strong></td>
                                            <td>
                                                <?php if ($esDevuelto): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Devuelto</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">En Préstamo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($mov['producto_nombre'] ?? 'Desconocido'); ?></strong><br>
                                                <small class="text-muted">SKU: <?php echo htmlspecialchars($mov['codigo'] ?? 'N/A'); ?></small>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($mov['trabajador_nombre'] ?? 'Sin asignar'); ?></strong><br>
                                                <small class="text-muted"><code>DNI: <?php echo htmlspecialchars($mov['DNI'] ?? 'N/A'); ?></code></small>
                                            </td>
                                            <td>
                                                <span class="text-dark small">Salida: <strong><?php echo htmlspecialchars($mov['fecha_salida'] ?? 'N/A'); ?></strong></span><br>
                                                <?php if ($esDevuelto): ?>
                                                    <span class="text-success small">Retorno: <strong><?php echo htmlspecialchars($mov['fecha_retorno']); ?></strong></span>
                                                <?php else: ?>
                                                    <span class="text-muted small italic">Sin retorno aún</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark px-2 py-1 border">ID: #<?php echo $mov['id']; ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script de Filtros, Autocompletado y Exportación Excel -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Filtros de tabla
            const inputBusqueda = document.getElementById('inputBusquedaMovimiento');
            const selectTipo = document.getElementById('selectTipoMovimiento');
            const filas = document.querySelectorAll('.fila-movimiento');

            function filtrarMovimientos() {
                const textoBusqueda = inputBusqueda.value.toLowerCase().trim();
                const tipoSeleccionado = selectTipo.value;

                filas.forEach(function (fila) {
                    const textoFila = fila.textContent.toLowerCase();
                    const tipoFila = fila.getAttribute('data-tipo');

                    const coincideTexto = textoBusqueda === '' || textoFila.includes(textoBusqueda);
                    const coincideTipo = tipoSeleccionado === 'TODOS' || tipoFila === tipoSeleccionado;

                    if (coincideTexto && coincideTipo) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            }

            if (inputBusqueda) inputBusqueda.addEventListener('keyup', filtrarMovimientos);
            if (selectTipo) selectTipo.addEventListener('change', filtrarMovimientos);

            // Sincronizar Salida (Producto)
            const inputProd = document.getElementById('inputBusquedaProducto');
            const hiddenProd = document.getElementById('producto_fk_hidden');
            const listProd = document.getElementById('datalistProductos');

            if (inputProd) {
                inputProd.addEventListener('input', function () {
                    let val = this.value;
                    hiddenProd.value = ''; 
                    for (let option of listProd.options) {
                        if (option.value === val) {
                            hiddenProd.value = option.getAttribute('data-id');
                            break;
                        }
                    }
                });
            }

            // Sincronizar Salida (Trabajador)
            const inputTrab = document.getElementById('inputBusquedaTrabajador');
            const hiddenTrab = document.getElementById('id_trabajador_fk_hidden');
            const listTrab = document.getElementById('datalistTrabajadores');

            if (inputTrab) {
                inputTrab.addEventListener('input', function () {
                    let val = this.value;
                    hiddenTrab.value = ''; 
                    for (let option of listTrab.options) {
                        if (option.value === val) {
                            hiddenTrab.value = option.getAttribute('data-id');
                            break;
                        }
                    }
                });
            }

            // Sincronizar Retorno y Autocompletar Trabajador
            const inputProdRet = document.getElementById('inputBusquedaProductoRetorno');
            const hiddenProdRet = document.getElementById('producto_fk_retorno_hidden');
            const listProdRet = document.getElementById('datalistProductosRetorno');
            const inputTrabRet = document.getElementById('inputBusquedaTrabajadorRetorno');

            if (inputProdRet) {
                inputProdRet.addEventListener('input', function () {
                    let val = this.value;
                    hiddenProdRet.value = '';
                    inputTrabRet.value = '';

                    for (let option of listProdRet.options) {
                        if (option.value === val) {
                            hiddenProdRet.value = option.getAttribute('data-id');
                            inputTrabRet.value = option.getAttribute('data-worker-name');
                            break;
                        }
                    }
                });
            }

            // Validaciones de formularios
            const formSalida = document.getElementById('formNuevaSalida');
            if (formSalida) {
                formSalida.addEventListener('submit', function(e) {
                    if (!hiddenProd.value || !hiddenTrab.value) {
                        alert('Por favor, seleccione un producto y un trabajador válidos de la lista.');
                        e.preventDefault();
                    }
                });
            }

            const formRetorno = document.getElementById('formRetorno');
            if (formRetorno) {
                formRetorno.addEventListener('submit', function(e) {
                    if (!hiddenProdRet.value) {
                        alert('Por favor, seleccione un producto válido para el retorno.');
                        e.preventDefault();
                    }
                });
            }

            // Exportación a Excel en tiempo real respetando filtros
            const btnExportarMov = document.getElementById('btnExportarExcelMovimientos');
            if (btnExportarMov) {
                btnExportarMov.addEventListener('click', function () {
                    const tabla = document.getElementById('tablaMovimientos');
                    if (!tabla) return;

                    const filasTabla = tabla.querySelectorAll('tbody tr');
                    let datosExportar = [];

                    // Encabezados
                    datosExportar.push(["ID", "Estado de Préstamo", "Producto", "SKU", "Trabajador Responsable", "DNI", "Fechas (Salida / Retorno)"]);

                    filasTabla.forEach(function (fila) {
                        if (fila.style.display !== 'none') {
                            const celdas = fila.querySelectorAll('td');
                            if (celdas.length >= 5) {
                                const id = celdas[0].innerText.trim();
                                const estado = celdas[1].innerText.trim();
                                
                                const lineasProducto = celdas[2].innerText.split('\n');
                                const producto = lineasProducto[0] ? lineasProducto[0].trim() : '';
                                const sku = lineasProducto[1] ? lineasProducto[1].replace('SKU:', '').trim() : '';

                                const lineasTrabajador = celdas[3].innerText.split('\n');
                                const trabajador = lineasTrabajador[0] ? lineasTrabajador[0].trim() : '';
                                const dni = lineasTrabajador[1] ? lineasTrabajador[1].replace('DNI:', '').trim() : '';

                                const fechas = celdas[4].innerText.replace(/\s+/g, ' ').trim();

                                datosExportar.push([id, estado, producto, sku, trabajador, dni, fechas]);
                            }
                        }
                    });

                    const wb = XLSX.utils.book_new();
                    const ws = XLSX.utils.aoa_to_sheet(datosExportar);

                    ws['!cols'] = [{wch: 8}, {wch: 18}, {wch: 28}, {wch: 18}, {wch: 28}, {wch: 15}, {wch: 32}];

                    XLSX.utils.book_append_sheet(wb, ws, "Movimientos");

                    const fecha = new Date().toISOString().split('T')[0];
                    XLSX.writeFile(wb, `Movimientos_Almacen_${fecha}.xlsx`);
                });
            }
        });
    </script>
</body>
</html>












