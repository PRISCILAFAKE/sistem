<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Controller/solicitud_crud_controller.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Requerimientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        .modal.fade:not(.show) {
            display: none !important;
        }

        .timeline-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            min-width: 320px;
            padding: 5px 0;
        }
        .timeline-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 15px;
            right: 15px;
            height: 3px;
            background: #e9ecef;
            transform: translateY(-50%);
            z-index: 1;
        }
        .timeline-step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: transparent;
            border: none;
            padding: 0;
            cursor: pointer;
        }
        .timeline-step .circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .timeline-step.completed .circle {
            background-color: #198754; 
        }
        .timeline-step.active .circle {
            background-color: #0d6efd; 
            transform: scale(1.1);
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.25);
        }
        .timeline-step .label {
            font-size: 10px;
            margin-top: 4px;
            color: #6c757d;
            font-weight: 600;
            white-space: nowrap;
        }
        .timeline-step.active .label {
            color: #0d6efd;
        }
        .timeline-step.completed .label {
            color: #198754;
        }

        .table-responsive-sticky {
            max-height: 600px;
            overflow-y: auto;
            position: relative;
        }
        
        .table-responsive-sticky thead th {
            position: sticky;
            top: 0;
            background-color: #212529 !important; 
            color: white;
            z-index: 10;
            box-shadow: inset 0 -1px 0 #dee2e6;
        }

        .table-responsive-modal-sticky {
            max-height: 400px;
            overflow-y: auto;
            position: relative;
        }

        .table-responsive-modal-sticky thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa !important; 
            color: #212529;
            z-index: 10;
            box-shadow: inset 0 -1px 0 #dee2e6;
        }
    </style>
</head>
<body class="bg-light">

<div style="background: #333; color: #fff; padding: 12px 20px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <a href="../View/crear_usuario_view.php" style="color: #9de3ff; text-decoration: none; font-weight: 500;">
            <i class="bi bi-person-plus-fill"></i> Crear Nuevo Usuario
        </a>
    </div>

    <div class="d-flex align-items-center gap-3">
        <span style="font-size: 14px;">
            <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['user'] ?? 'Invitado'); ?> | 
            <strong>Área:</strong> <?php echo htmlspecialchars($_SESSION['nombre_area'] ?? 'Sin área'); ?>
        </span>
        <a href="../Controller/logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
    </div>
</div>

<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0">Panel de Control de Requerimientos</h2>
            <p class="text-muted small m-0">Gestión y seguimiento de requerimientos aprobados</p>
        </div>
    </div>

    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 p-3 bg-white">
        <form method="GET" action="" class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="id_buscado" class="form-control" placeholder="Buscar por Nombre de Solicitud..." value="<?php echo htmlspecialchars($_GET['id_buscado'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <select class="form-select" name="id_usuario_f">
                    <option value="">Buscar por Solicitante</option>
                    <?php foreach ($usuarios as$usr): ?>
                        <option value="<?php echo $usr['id_usuario']; ?>" <?php echo (isset($_GET['id_usuario_f']) && $_GET['id_usuario_f'] ==$usr['id_usuario']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($usr['nombre_usuario']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 text-md-end">
                <button type="submit" class="btn btn-success"><i class="bi bi-search"></i> Buscar</button>
                <a href="solicitud_crud_view.php" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise"></i> Mostrar Todos</a>
            </div>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive table-responsive-sticky">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Nombre Solicitud</th>
                            <th>Solicitante</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requerimientos)): ?>
                            <?php foreach ($requerimientos as$req): ?>
                                <?php 
                                    $estadoActual = strtolower($req['estado'] ?? 'registrado'); 
                                    $pasos = ['registrado', 'aceptado', 'en proceso', 'finalizado'];$indiceActual = array_search($estadoActual,$pasos);
                                    if ($indiceActual === false)$indiceActual = 0; 
                                ?>
                                <tr>
                                    <td><?php echo $req['id_requerimiento']; ?></td>
                                    <td><?php echo htmlspecialchars($req['fecha']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($req['nombre_solicitud']); ?></strong></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($req['nombre_usuario'] ?? 'Desconocido'); ?></span></td>
                                    
                                    <td>
                                        <div class="timeline-steps" data-id="<?php echo $req['id_requerimiento']; ?>">
                                            <?php foreach ($pasos as $index =>$paso): ?>
                                                <?php 
                                                    $claseEstado = "";
                                                    if ($index < $indiceActual) {$claseEstado = "completed";
                                                    } elseif ($index === $indiceActual) {$claseEstado = "active";
                                                    }
                                                    $etiquetas = [
                                                        'registrado' => 'Registrado',
                                                        'aceptado' => 'Aceptado',
                                                        'en proceso' => 'En Proceso',
                                                        'finalizado' => 'Finalizado'
                                                    ];
                                                ?>
                                                <button type="button" 
                                                        class="timeline-step <?php echo $claseEstado; ?>" 
                                                        data-paso="<?php echo $paso; ?>"
                                                        data-index="<?php echo $index; ?>"
                                                        data-actual="<?php echo $indiceActual; ?>"
                                                        title="Avanzar a: <?php echo $etiquetas[$paso]; ?>">
                                                    <div class="circle">
                                                        <?php if ($index <$indiceActual): ?>
                                                            <i class="bi bi-check-lg"></i>
                                                        <?php elseif ($index ===$indiceActual): ?>
                                                            <i class="bi bi-dot"></i>
                                                        <?php else: ?>
                                                            <?php echo $index + 1; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="label"><?php echo $etiquetas[$paso]; ?></span>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($estadoActual === 'finalizado'): ?>
                                            <span class="badge bg-secondary mb-1">Finalizado</span>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal-<?php echo $req['id_requerimiento']; ?>">
                                                <i class="bi bi-calendar-event"></i> Reunión
                                            </button>
                                            
                                            <div class="modal fade" id="exampleModal-<?php echo $req['id_requerimiento']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-warning text-dark">
                                                            <h5 class="modal-title" id="exampleModalLabel">Nueva Reunión - Solicitud: <?php echo $req['nombre_solicitud']; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <form action="../Controller/reunion_controller.php" method="POST" enctype="multipart/form-data">
                                                                <input type="hidden" name="id_requerimiento" value="<?php echo $req['id_requerimiento']; ?>">

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold text-secondary">Asunto:</label>
                                                                    <input type="text" name="asunto" class="form-control" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold text-secondary">Para:</label>
                                                                    <input type="text" name="para" class="form-control" placeholder="ej. correo@dominio.com, nose@dominio.com" required>
                                                                    <div class="form-text" style="font-size: 11px;">Puedes ingresar uno o varios correos separados por comas (,).</div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold text-secondary">Fecha:</label>
                                                                    <input type="date" name="fecha" class="form-control" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold text-secondary">Hora:</label>
                                                                    <input type="time" name="hora" class="form-control" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold text-secondary">Breve descripción:</label>
                                                                    <textarea class="form-control" name="descripcion" rows="3" required></textarea>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold text-secondary">Archivo adjunto:</label>
                                                                    <input class="form-control" type="file" name="archivo" required>
                                                                </div> 

                                                                <div class="modal-footer px-0 pb-0">
                                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" name="actualizar" class="btn btn-warning btn-sm">Solicitar Reunión</button>
                                                                </div>
                                                            </form>    
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalHistorialReuniones" data-id-requerimiento="<?php echo $req['id_requerimiento']; ?>" title="Ver historial de reuniones">
                                            <i class="bi bi-clock-history"></i>
                                        </button>

                                        <a href="../View/formvis_ad_view.php?id=<?php echo $req['id_requerimiento']; ?>" 
                                           class="btn btn-outline-primary btn-sm" title="Ver detalle">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <a href="../View/formvis_ad_view.php?id=<?php echo $req['id_requerimiento']; ?>&imprimir=true" 
                                           target="_blank" class="btn btn-outline-secondary btn-sm" title="Imprimir requerimiento">
                                            <i class="bi bi-printer-fill"></i> 
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No se encontraron requerimientos.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistorialReuniones" tabindex="-1" aria-labelledby="modalHistorialReunionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalHistorialReunionesLabel">Historial de Reuniones del Requerimiento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body table-responsive-modal-sticky">
                <div id="contenidoReuniones" class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando reuniones...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.timeline-step').forEach(button => {
        button.addEventListener('click', function() {
            const pasoDestino = this.getAttribute('data-paso');
            const indexDestino = parseInt(this.getAttribute('data-index'));
            const indexActual = parseInt(this.getAttribute('data-actual'));
            const container = this.closest('.timeline-steps');
            const idRequerimiento = container.getAttribute('data-id');

            if (indexDestino <= indexActual) {
                if (indexDestino === indexActual) {
                    alert('Este requerimiento ya se encuentra en este estado.');
                } else {
                    alert('No está permitido retroceder de estado por motivos de control.');
                }
                return;
            }

            if (indexDestino !== indexActual + 1) {
                alert('Debe completar el paso anterior antes de avanzar a este estado.');
                return;
            }

            if (!confirm(`¿Desea aprobar y avanzar el proceso al estado "${pasoDestino.toUpperCase()}"?`)) {
                return;
            }

            fetch('../Controller/actualizar_estado_controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_requerimiento=${idRequerimiento}&nuevo_estado=${encodeURIComponent(pasoDestino)}`
            })
            .then(async response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    const errorTexto = await response.text();
                    console.error("Detalle del error del servidor:", errorTexto);
                    alert('Error al procesar la actualización del estado: ' + errorTexto);
                }
            })
            .catch(error => {
                console.error('Error de red:', error);
                alert('Hubo un error de conexión.');
            });
        });
    });

    const modalHistorialReuniones = document.getElementById('modalHistorialReuniones');
    if (modalHistorialReuniones) {
        modalHistorialReuniones.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const idRequerimiento = button.getAttribute('data-id-requerimiento');
            const contenidoDiv = document.getElementById('contenidoReuniones');
            
            contenidoDiv.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando reuniones...</p>
            `;

            fetch(`../Controller/obtener_reuniones_controller.php?id_requerimiento=${idRequerimiento}`)
                .then(response => response.text())
                .then(html => {
                    contenidoDiv.innerHTML = html;
                })
                .catch(error => {
                    contenidoDiv.innerHTML = `<div class="alert alert-danger">Error al cargar las reuniones.</div>`;
                });
        });
    }
});
</script>

</body>
</html>