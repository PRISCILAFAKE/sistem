<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validación de seguridad: Si no hay sesión activa, redirigir al login
if (empty($_SESSION['id_usuario'])) {
    header("Location: ../index.php"); // Cambia la ruta según tu estructura de login
    exit();
}

require_once __DIR__ . '/../Model/conexion.php';

$id_usuario_actual = $_SESSION['id_usuario'];$requerimientos = [];
// Cambiamos el nombre de la variable para que sea más descriptivo (búsqueda por texto)
$busqueda_texto = trim($_GET['id_buscado'] ?? '');

try {
    $sql = "SELECT r.*, 
                   t.nombre_s AS nombre_tipo_solicitud, 
                   u.nombre_ur AS nombre_urgencia, 
                   COALESCE(p.estado, 'registrado') AS estado,
                   p.motivo_rechazo 
            FROM requerimientos r 
            LEFT JOIN tipo_solicitud t ON r.id_tipo_solicitud_fk = t.id_tipos 
            LEFT JOIN urgencia u ON r.id_urgencia_fk = u.id_urgencia 
            LEFT JOIN procesos p ON r.id_requerimiento = p.id_requerimiento_fk 
            WHERE r.id_usuario_f = :id_usuario";

    if (!empty($busqueda_texto)) {$sql .= " AND r.nombre_solicitud LIKE :busqueda_texto";
    }

    $sql .= " ORDER BY r.fecha DESC";

    $stmt = $conexion->prepare($sql);
    
    $params = [':id_usuario' =>$id_usuario_actual];
    if (!empty($busqueda_texto)) {
        $params[':busqueda_texto'] = '\%' .$busqueda_texto . '%';
    }

    $stmt->execute($params);
    $requerimientos =$stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {$error_mensaje = "Error al obtener el historial: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Requerimientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        #sugerencias-lista {
            position: absolute;
            z-index: 1000;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            border-radius: 0 0 5px 5px;
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
    </style>
</head>
<body class="bg-light">

<div style="background: #333; color: #fff; padding: 12px 20px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <a href="../View/perfil_view.php" style="color: #9de3ff; text-decoration: none; font-weight: 500;">
            <i class="bi bi-person-plus-fill"></i> Mis Datos
        </a>
    </div>

    <div class="d-flex align-items-center gap-3">
        <span style="font-size: 14px;">
            <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['user'] ?? 'Invitado'); ?> | 
            <strong>Área:</strong> <?php echo htmlspecialchars($_SESSION['nombre_area'] ?? 'Sin área'); ?>
        </span>
        <a href="../Controller/logout.php" class="btn btn-sm btn-danger">Cerrar Sesión</a>
    </div>
</div>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Mis Requerimientos</h2>
        <a href="formularioS_view.php" class="btn btn-primary">Nuevo Requerimiento</a>
    </div>

    <?php if (isset($_GET['reenviado'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            El requerimiento fue corregido y reenviado a Gerencia con éxito.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 p-3 bg-white">
        <form method="GET" action="" class="row g-3 align-items-center">
            <div class="col-md-4 position-relative">
                <input type="text" id="input-busqueda" name="id_buscado" class="form-control" placeholder="Ingrese Nombre de Solicitud" value="<?php echo htmlspecialchars($busqueda_texto); ?>" autocomplete="off">
                <ul id="sugerencias-lista" class="list-group shadow-sm"></ul>
            </div>

            <div class="col-md-4 text-md-end">
                <button type="submit" class="btn btn-success"><i class="bi bi-search"></i> Buscar</button>
                <a href="solicitud_view.php" class="btn btn-secondary ms-2"><i class="bi bi-arrow-counterclockwise"></i> Mostrar Todos</a>
            </div>
        </form>
    </div>

    <?php if (isset($error_mensaje)): ?>
        <div class="alert alert-danger"><?php echo $error_mensaje; ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive table-responsive-sticky">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Tipo de Solicitud</th>
                            <th>Nombre Solicitud</th>
                            <th>Urgencia</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requerimientos)): ?>
                            <?php foreach ($requerimientos as$req): ?>
                                <tr>
                                    <td><?php echo $req['id_requerimiento']; ?></td>
                                    <td><?php echo htmlspecialchars($req['fecha']); ?></td>
                                    <td><?php echo htmlspecialchars($req['nombre_tipo_solicitud'] ?? 'N/A'); ?></td>
                                    <td><strong><?php echo htmlspecialchars($req['nombre_solicitud']); ?></strong></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo htmlspecialchars($req['nombre_urgencia'] ?? 'Normal'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($req['estado'] == 'rechazado'): ?>
                                            <span class="badge bg-danger">rechazado</span>
                                            <?php if (!empty($req['motivo_rechazo'])): ?>
                                                <div class="text-danger small mt-1">
                                                    <strong>Motivo:</strong> <?php echo htmlspecialchars($req['motivo_rechazo']); ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif ($req['estado'] == 'registrado'): ?>
                                            <span class="badge bg-warning text-dark">registrado</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark"><?php echo htmlspecialchars($req['estado']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                         <a href="../View/formvis_us_view.php?id=<?php echo $req['id_requerimiento']; ?>" 
                                             class="btn btn-outline-primary btn-sm" title="Ver detalle">
                                             <i class="bi bi-eye-fill"></i>
                                         </a>

                                        <a href="../View/formvis_us_view.php?id=<?php echo $req['id_requerimiento']; ?>&imprimir=true" 
                                           target="_blank" class="btn btn-outline-secondary btn-sm" title="Imprimir requerimiento">
                                            <i class="bi bi-printer-fill"></i> 
                                        </a>

                                         <?php if ($req['estado'] == 'rechazado'): ?>
                                             <a href="../View/editar_requerimiento_view.php?id=<?php echo $req['id_requerimiento']; ?>" 
                                                 class="btn btn-outline-warning btn-sm" title="Editar y corregir requerimiento">
                                                 <i class="bi bi-pencil-square"></i> 
                                             </a>
                                         <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No se encontraron requerimientos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
const inputBusqueda = document.getElementById('input-busqueda');
const listaSugerencias = document.getElementById('sugerencias-lista');

inputBusqueda.addEventListener('input', function() {
    const query = this.value.trim();
    if (query.length === 0) {
        listaSugerencias.innerHTML = '';
        return;
    }

    fetch(`../Controller/sugerencias_controller.php?term=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            listaSugerencias.innerHTML = '';
            if (data.length > 0) {
                data.forEach(item => {
                    const li = document.createElement('li');
                    li.classList.add('list-group-item', 'list-group-item-action');
                    li.style.cursor = 'pointer';
                    li.textContent = item;
                    
                    li.addEventListener('click', function() {
                        inputBusqueda.value = item;
                        listaSugerencias.innerHTML = '';
                        inputBusqueda.form.submit();
                    });
                    listaSugerencias.appendChild(li);
                });
            }
        })
        .catch(error => console.error('Error:', error));
});

document.addEventListener('click', function(e) {
    if (!inputBusqueda.contains(e.target) && !listaSugerencias.contains(e.target)) {
        listaSugerencias.innerHTML = '';
    }
});
</script>
</body>
</html>