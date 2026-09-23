<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/conexion.php';

$id_requerimiento = $_GET['id'] ?? null;

if (!$id_requerimiento) {
    die("No se ha especificado un requerimiento.");
}

try {
    $sql = "SELECT r.*, 
                   t.nombre_s AS nombre_tipo_solicitud, 
                   urg.nombre_ur AS nombre_urgencia, 
                   p.estado,
                   u.id_usuario,
                   u.nombre AS nombre_usuario,
                   ar.nombre_area AS nombre_area_usuario 
            FROM requerimientos r 
            LEFT JOIN tipo_solicitud t ON r.id_tipo_solicitud_fk = t.id_tipos 
            LEFT JOIN urgencia urg ON r.id_urgencia_fk = urg.id_urgencia 
            LEFT JOIN procesos p ON r.id_requerimiento = p.id_requerimiento_fk 
            LEFT JOIN usuario u ON r.id_usuario_f = u.id_usuario 
            LEFT JOIN areas ar ON u.id_area_fk = ar.id_area 
            WHERE r.id_requerimiento = :id_req";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id_req' => $id_requerimiento]);
    $requerimiento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$requerimiento) {
        die("El requerimiento no existe.");
    }

    $id_usuario = $requerimiento['id_usuario'];

    $stmt_orden = $conexion->prepare("SELECT COUNT(*) as orden FROM requerimientos WHERE id_usuario_f = :id_usr AND id_requerimiento <= :id_req");
    $stmt_orden->execute([
        ':id_usr' => $id_usuario,
        ':id_req' => $id_requerimiento
    ]);
    $resultado_orden = $stmt_orden->fetch(PDO::FETCH_ASSOC);
    $numeroOrdenInt = $resultado_orden['orden'] ?? 1;

    $areaTexto = $requerimiento['nombre_area_usuario'] ?? 'GEN';
    $codeArea = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $areaTexto), 0, 3));
    if (strlen($codeArea) < 3) { $codeArea = str_pad($codeArea, 3, 'X'); }

    $nombreUsrTexto = $requerimiento['nombre_usuario'] ?? 'USR';
    $codeUser = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nombreUsrTexto), 0, 3));
    if (strlen($codeUser) < 3) { $codeUser = str_pad($codeUser, 3, 'X'); }

    $numeroOrden = str_pad($numeroOrdenInt, 3, '0', STR_PAD_LEFT);
    $codigoRequerimiento = $codeArea . '_' . $codeUser . '_' . $numeroOrden;

    function formatearFechaEspanol($fechaStr) {
        if (empty($fechaStr)) return 'N/A';
        $timestamp = strtotime($fechaStr);
        if (!$timestamp) return htmlspecialchars($fechaStr);
        
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        
        $dia = date('j', $timestamp);
        $mes = $meses[(int)date('n', $timestamp)];
        $anio = date('Y', $timestamp);
        
        return "$dia de $mes de $anio";
    }

    $fechaFormateada = formatearFechaEspanol($requerimiento['fecha']);

} catch (PDOException $e) {
    die("Error al cargar el detalle: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Requerimiento - Gerencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        .campo-grupo { margin-bottom: 1.6rem; }
        .pregunta-titulo { font-size: 0.95rem; font-weight: 700; color: #212529; margin-bottom: 0.35rem; display: block; }
        .respuesta-texto { font-size: 0.9rem; color: #495057; background: #ffffff; padding: 0; }

        @media print {
            .btn, button, a.btn, .d-flex, .no-print, .ocultar-en-impresion { display: none !important; }
            body, html { height: 100% !important; overflow: hidden !important; background-color: #ffffff !important; color: #000 !important; font-size: 10px !important; }
            .container { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
            .card { border: 1px solid #000 !important; box-shadow: none !important; padding: 10px !important; margin: 0 !important; page-break-inside: avoid !important; break-inside: avoid !important; }
            .campo-grupo { margin-bottom: 0.9rem !important; }
            .pregunta-titulo { font-size: 10px !important; color: #000 !important; font-weight: bold !important; margin-bottom: 2px !important; }
            .respuesta-texto { font-size: 10px !important; color: #000 !important; }
            .solo-texto-impresion { display: block !important; font-size: 10px !important; color: #000 !important; }
        }
        .solo-texto-impresion { display: none; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4 mb-4" style="max-width: 800px;">
    <div class="card shadow-sm p-4 border-0">
        
        <div class="border-bottom pb-3 mb-4">
            <div class="row align-items-center">
                <div class="col-8">
                    <h3 class="fw-bold fs-5 text-dark m-0">Solicitud de Cambios / Nuevos Módulos</h3>
                    <p class="text-muted small m-0">Panel de Control Gerencial</p>
                </div>
                <div class="col-4 text-end">
                    <div class="border p-2 rounded bg-light">
                        <span class="d-block text-muted" style="font-size: 10px;">CÓDIGO DE REQUERIMIENTO</span>
                        <strong class="text-primary fs-6"><?php echo $codigoRequerimiento; ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">Usuario Solicitante:</span>
            <div class="respuesta-texto"><?php echo htmlspecialchars($requerimiento['nombre_usuario'] ?? 'No especificado'); ?></div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">Área Solicitante:</span>
            <div class="respuesta-texto"><?php echo htmlspecialchars($requerimiento['nombre_area_usuario'] ?? 'No especificado'); ?></div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">Nombre Solicitud:</span>
            <div class="respuesta-texto fw-bold text-dark"><?php echo htmlspecialchars($requerimiento['nombre_solicitud']); ?></div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">Tipo de Solicitud:</span>
            <div class="respuesta-texto"><?php echo htmlspecialchars($requerimiento['nombre_tipo_solicitud'] ?? 'N/A'); ?></div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">Urgencia:</span>
            <div class="respuesta-texto"><?php echo htmlspecialchars($requerimiento['nombre_urgencia'] ?? 'N/A'); ?></div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">Fecha de Solicitud:</span>
            <div class="respuesta-texto"><?php echo $fechaFormateada; ?></div>
        </div>

        <div class="campo-grupo mt-4 border-top pt-3">
            <h5 class="fw-bold text-dark fs-6">Descripción detallada</h5>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">¿Qué necesitas que haga el sistema?</span>
            <div class="respuesta-texto"><?php echo nl2br(htmlspecialchars($requerimiento['funcionalidad'])); ?></div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">¿Por qué es necesario este cambio?</span>
            <div class="respuesta-texto"><?php echo nl2br(htmlspecialchars($requerimiento['necesidad'])); ?></div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">Usuarios Impactados:</span>
            <div class="respuesta-texto"><?php echo htmlspecialchars($requerimiento['impacto_user']); ?></div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">Archivo adjunto</span>
            <div class="mt-1">
                <?php if (!empty($requerimiento['archivo_adjunto'])): ?>
                    <?php 
                        $rutaArchivo = '../' . htmlspecialchars($requerimiento['archivo_adjunto']);
                        $nombreArchivoPuro = basename($requerimiento['archivo_adjunto']);
                        $extension = strtolower(pathinfo($requerimiento['archivo_adjunto'], PATHINFO_EXTENSION));
                        $esImagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $esPdf = ($extension === 'pdf');
                    ?>

                    <div class="solo-texto-impresion">
                        <i class="bi bi-paperclip"></i> Archivo adjunto: <strong><?php echo htmlspecialchars($nombreArchivoPuro); ?></strong>
                    </div>

                    <div class="ocultar-en-impresion">
                        <div class="mb-2">
                            <a href="<?php echo $rutaArchivo; ?>" class="btn btn-success btn-sm" download target="_blank">
                                <i class="bi bi-download"></i> Descargar
                            </a>
                        </div>

                        <?php if ($esImagen): ?>
                            <div class="border rounded p-1 bg-white text-center" style="max-width: 220px;">
                                <img src="<?php echo $rutaArchivo; ?>" alt="Archivo adjunto" class="img-fluid rounded" style="max-height: 70px; object-fit: contain;">
                            </div>
                        <?php elseif ($esPdf): ?>
                            <div>
                                <iframe src="<?php echo $rutaArchivo; ?>" width="100%" height="160px" class="border rounded"></iframe>
                            </div>
                        <?php else: ?>
                            <div class="text-muted small"><i class="bi bi-file-earmark-text"></i> Archivo adjunto disponible (Excel, Word u otro formato).</div>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <span class="text-muted small">No se adjuntó ningún archivo.</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">Áreas afectadas:</span>
            <div class="respuesta-texto"><?php echo htmlspecialchars($requerimiento['areas_afectadas']); ?></div>
        </div>

        <div class="campo-grupo">
            <span class="pregunta-titulo">Estado Actual del Proceso:</span>
            <div class="respuesta-texto fw-bold text-uppercase text-primary"><?php echo htmlspecialchars($requerimiento['estado'] ?? 'registrado'); ?></div>
        </div>

        <hr class="my-3">
        <div class="d-flex justify-content-end gap-2 no-print">
            <a href="solicitud_ge_view.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver al Panel </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('imprimir') === 'true') {
            window.print();
            window.addEventListener('focus', function() {
                setTimeout(function() { window.location.href = 'solicitud_ge_view.php'; }, 300);
            }, { once: true });
        }
    });
</script>
</body>
</html>