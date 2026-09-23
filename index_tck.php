<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Tickets - Soporte TI</title>
    
    <style>
        :root {
            --primary-color: #8C1F1F;
            --black: #000000;
            --white: #ffffff;
            --gray-light: #f4f4f4;
            --gray: #dddddd;
            --success: #28a745;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(-45deg, #000000, #2b0a0a, #8C1F1F, #2b0a0a, #000000);
            background-size: 400% 400%;
            animation: glowWave 15s ease infinite;
            color: var(--white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        @keyframes glowWave {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: auto;
            position: relative;
            z-index: 1;
        }

        header {
            background: var(--primary-color);
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            border-bottom: 4px solid var(--white);
        }

        header h1 {
            color: var(--white);
            font-size: 2em;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-section {
            background: var(--white);
            color: var(--black);
            padding: 40px;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .form-section h2 {
            color: var(--primary-color);
            margin-bottom: 25px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            display: inline-block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--black);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--gray);
            border-radius: 4px;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        button {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 14px 20px;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            width: 100%;
            text-transform: uppercase;
        }

        button:hover {
            background-color: #6d1717;
            transform: translateY(-1px);
        }

        .admin-login-trigger {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary-color);
            color: var(--white);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            transition: all 0.3s;
            z-index: 100;
        }

        .admin-login-trigger:hover {
            transform: scale(1.1) rotate(15deg);
            background: var(--white);
            color: var(--primary-color);
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: none;
            font-weight: 500;
        }

        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; display: block; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; display: block; }

        #adminPanel {
            display: none;
            margin-top: 30px;
            background: var(--white);
            color: var(--black);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
        }

        .logout-btn {
            width: auto;
            padding: 8px 15px;
            font-size: 0.9em;
            background: var(--black);
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: var(--primary-color);
            color: var(--white);
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid var(--gray);
            vertical-align: middle;
        }

        tr:hover {
            background-color: var(--gray-light);
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
            text-transform: capitalize;
        }

        .badge-software { background: #e2e3e5; color: #383d41; }
        .badge-hardware { background: #fff3cd; color: #856404; }

        /* BARRA INTERACTIVA */
        .timeline-steps {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            min-width: 260px;
            padding: 5px 0;
        }

        .timeline-steps::before {
            content: '';
            position: absolute;
            top: 16px;
            left: 15px;
            right: 15px;
            height: 3px;
            background-color: var(--gray);
            z-index: 1;
        }

        .timeline-step {
            background: transparent !important;
            border: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            cursor: pointer;
            padding: 0;
            outline: none;
            width: auto !important;
            text-transform: none;
        }

        .timeline-step .circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
            font-weight: bold;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .timeline-step .label {
            margin-top: 4px;
            font-size: 0.7em;
            color: #6c757d;
            font-weight: bold;
            white-space: nowrap;
        }

        .timeline-step.completed .circle { background-color: var(--success); }
        .timeline-step.completed .label { color: var(--success); }

        .timeline-step.active .circle {
            background-color: #007bff;
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25);
            font-size: 1em;
        }
        .timeline-step.active .label { color: #007bff; }

        .timeline-step:not(.disabled-step):hover .circle { transform: scale(1.1); }

        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-action {
            background: #8f2d2d;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: auto !important;
        }
        .btn-action:hover { background: #af6262; }

        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--white);
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 480px;
            color: var(--black);
            position: relative;
        }

        .modal-content h3 {
            margin-bottom: 20px;
            color: var(--primary-color);
            text-align: center;
        }

        .close-modal {
            position: absolute;
            top: 15px; right: 20px;
            cursor: pointer;
            font-size: 1.5em;
            font-weight: bold;
            color: var(--black);
        }

        .file-upload-wrapper {
            position: relative;
            width: 100%;
            height: 50px;
            border: 2px dashed var(--gray);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gray-light);
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-upload-wrapper input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload-label {
            font-weight: 600;
            color: #666;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hidden { display: none !important; }
        .form-hint { font-size: 0.8em; color: #666; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- VISTA PÚBLICA -->
        <div id="publicView">
            <header>
                <h1>Soporte TI</h1>
            </header>

            <div class="form-section">
                <h2>Enviar una Solicitud</h2>

                <div id="alert" class="alert" role="alert"></div>

                <!-- El campo de Nombre Completo y Correo Electronico debe completarse automaticamente con los datos de la cuenta del inicio de sesion en el login general que utilizan los 3 sistemas. rq/Controller/login_controller.php ahi es donde se maneja el login general de los tres sistemas-->
                <form id="formCrearTicket">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
                    </div>

                    <div class="form-group">
                        <label for="celular">Celular</label>
                        <input type="text" id="celular" name="celular" placeholder="Ej: 1234567890" required>
                    </div>

                    <!-- Conectado a la tabla tipo_reporte -->
                    <div class="form-group">
                        <label for="id_tipo">Tipo de Reporte</label>
                        <select id="id_tipo" name="id_tipo" required>
                            <option value="">Cargando tipos de reporte...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción del Problema</label>
                        <textarea id="descripcion" name="descripcion" placeholder="Describe tu problema aquí..." required rows="4"></textarea>
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label for="imagen">Adjuntar Imagen (Opcional)</label>
                        <div class="file-upload-wrapper" id="fileWrapper">
                            <div class="file-upload-label" id="fileLabel">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                <span>Seleccionar Imagen</span>
                            </div>
                            <input type="file" id="imagen" name="imagen[]" accept="image/*" multiple>
                        </div>
                        <div class="form-hint">Puedes seleccionar varias imágenes (JPG, PNG, GIF)</div>
                    </div>

                    <button type="submit" id="btnEnviar">Enviar Solicitud</button>
                </form>

                <div class="admin-login-trigger" id="adminTrigger" title="Acceso Admin">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
            </div>
        </div>

        <!-- VISTA ADMINISTRADOR -->
        <div id="adminPanel">
            <div class="admin-header">
                <h2>Panel de Administración</h2>
                <button class="logout-btn" id="btnLogout">Cerrar Sesión</button>
            </div>

            <div class="filters" style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
                <select id="filterEstado" style="width: auto; padding: 8px;" aria-label="Filtrar por estado">
                    <option value="">Todos los estados</option>
                    <option value="Nuevo">Nuevo</option>
                    <option value="En Progreso">En Progreso</option>
                    <option value="Resuelto">Resuelto</option>
                    <option value="Cerrado">Cerrado</option>
                </select>
            </div>

            <!-- Agregar una columna de urgencia pero que solo el administrador pueda decidir que tan urgente es cuando haya un nuevo tickets, los tipos de urgencia serian; baja, media y alta. la columna que se utilizara para almacenar la urgencia sera la columna prioridad de la tabla tck_tickets.-->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Solicitante</th>
                            <th>Celular</th>
                            <th>Fecha</th>
                            <th>Tipo de Reporte</th>
                            <th>Estado (Haz clic para avanzar)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsTbody">
                        <!-- Creado dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DE LOGIN -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="closeLogin">&times;</span>
            <h3>Acceso Administrativo</h3>
            <form id="formLogin">
                <div class="form-group">
                    <label for="adminUser">Usuario</label>
                    <input type="text" id="adminUser" required>
                </div>
                <div class="form-group">
                    <label for="adminPass">Contraseña</label>
                    <input type="password" id="adminPass" required>
                </div>
                <button type="submit">Entrar</button>
            </form>
            <div id="loginAlert" class="alert error" style="margin-top: 15px;"></div>
        </div>
    </div>

    <!-- MODAL DE DETALLES Y VISUALIZADOR DE ARCHIVOS -->
    <div id="manageModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="closeManage">&times;</span>
            <h3>Detalles del Ticket <span id="manageTicketId"></span></h3>
            <div id="manageDetails" style="font-size: 0.9em; line-height: 1.6;"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            cargarTiposReporte();

            const publicView = document.getElementById('publicView');
            const adminPanel = document.getElementById('adminPanel');
            const formCrearTicket = document.getElementById('formCrearTicket');
            const alertBox = document.getElementById('alert');
            
            const loginModal = document.getElementById('loginModal');
            const adminTrigger = document.getElementById('adminTrigger');
            const closeLogin = document.getElementById('closeLogin');
            const formLogin = document.getElementById('formLogin');
            const loginAlert = document.getElementById('loginAlert');
            const btnLogout = document.getElementById('btnLogout');

            const manageModal = document.getElementById('manageModal');
            const closeManage = document.getElementById('closeManage');
            const manageDetails = document.getElementById('manageDetails');
            const manageTicketId = document.getElementById('manageTicketId');

            const ticketsTbody = document.getElementById('ticketsTbody');
            const filterEstado = document.getElementById('filterEstado');
            const btnRecargar = document.getElementById('btnRecargar');

            const fileInput = document.getElementById('imagen');
            const fileLabel = document.getElementById('fileLabel');
            
            if (fileInput) {
                fileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        fileLabel.querySelector('span').textContent = `${e.target.files.length} archivo(s) seleccionado(s)`;
                    } else {
                        fileLabel.querySelector('span').textContent = 'Seleccionar Imagen';
                    }
                });
            }

            async function cargarTiposReporte() {
                const selectTipo = document.getElementById('id_tipo');
                if (!selectTipo) return;

                try {
                    const response = await fetch('api/get_tipos.php');
                    const data = await response.json();

                    if (data.success && data.tipos.length > 0) {
                        selectTipo.innerHTML = '<option value="">Seleccione una opción</option>';
                        data.tipos.forEach(tipo => {
                            const option = document.createElement('option');
                            option.value = tipo.id_tipo;
                            option.textContent = tipo.nombre.charAt(0).toUpperCase() + tipo.nombre.slice(1);
                            selectTipo.appendChild(option);
                        });
                    } else {
                        selectTipo.innerHTML = '<option value="">No hay tipos disponibles</option>';
                    }
                } catch (error) {
                    console.error('Error al cargar tipos de reporte:', error);
                    selectTipo.innerHTML = '<option value="">Error al cargar opciones</option>';
                }
            }

            if (formCrearTicket) {
                formCrearTicket.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(formCrearTicket);
                    const btnEnviar = document.getElementById('btnEnviar');
                    btnEnviar.disabled = true;
                    btnEnviar.textContent = 'Enviando...';

                    try {
                        const response = await fetch('api/create_ticket.php', { method: 'POST', body: formData });
                        const data = await response.json();
                        alertBox.className = 'alert ' + (data.success ? 'success' : 'error');
                        alertBox.textContent = data.message;
                        alertBox.style.display = 'block';

                        if (data.success) {
                            formCrearTicket.reset();
                            if(fileLabel) fileLabel.querySelector('span').textContent = 'Seleccionar Imagen';
                            cargarTiposReporte();
                        }
                    } catch (error) {
                        alertBox.className = 'alert error';
                        alertBox.textContent = 'Error de conexión con el servidor.';
                        alertBox.style.display = 'block';
                    } finally {
                        btnEnviar.disabled = false;
                        btnEnviar.textContent = 'Enviar Solicitud';
                    }
                });
            }

            if (adminTrigger) adminTrigger.addEventListener('click', () => { loginModal.style.display = 'flex'; });
            if (closeLogin) closeLogin.addEventListener('click', () => { loginModal.style.display = 'none'; });

            window.addEventListener('click', (e) => {
                if (e.target === loginModal) loginModal.style.display = 'none';
                if (e.target === manageModal) manageModal.style.display = 'none';
            });

            if (formLogin) {
                formLogin.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const usuario = document.getElementById('adminUser').value;
                    const password = document.getElementById('adminPass').value;

                    try {
                        const response = await fetch('api/login.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `usuario=${encodeURIComponent(usuario)}&password=${encodeURIComponent(password)}`
                        });
                        const data = await response.json();

                        if (data.success) {
                            loginModal.style.display = 'none';
                            formLogin.reset();
                            loginAlert.style.display = 'none';
                            publicView.classList.add('hidden');
                            adminPanel.style.display = 'block';
                            cargarTickets();
                        } else {
                            loginAlert.textContent = data.message;
                            loginAlert.style.display = 'block';
                        }
                    } catch (err) {
                        loginAlert.textContent = 'Error al intentar iniciar sesión.';
                        loginAlert.style.display = 'block';
                    }
                });
            }

            if (btnLogout) {
                btnLogout.addEventListener('click', () => {
                    adminPanel.style.display = 'none';
                    publicView.classList.remove('hidden');
                });
            }

            async function cargarTickets() {
                const estadoFiltro = filterEstado ? filterEstado.value : '';
                try {
                    const response = await fetch(`api/get_tickets.php?estado=${encodeURIComponent(estadoFiltro)}`);
                    const data = await response.json();

                    if (data.success) {
                        ticketsTbody.innerHTML = '';
                        if (data.tickets.length === 0) {
                            ticketsTbody.innerHTML = `<tr><td colspan="7" style="text-align:center;">No hay tickets registrados.</td></tr>`;
                            return;
                        }

                        data.tickets.forEach(ticket => {
                            const tipoReporte = ticket.tipo_reporte || 'general';
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>#${ticket.numero}</strong></td>
                                <td>${escapeHTML(ticket.nombre_solicitante)}</td>
                                <td>${escapeHTML(ticket.celular)}</td>
                                <td>${ticket.creado_en}</td>
                                <td><span class="badge badge-${tipoReporte.toLowerCase()}">${escapeHTML(tipoReporte)}</span></td>
                                <td>${ticket.estado_html}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-detalles" data-id="${ticket.id}" title="Ver detalles y archivos">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </button>
                                    </div>
                                </td>
                            `;
                            ticketsTbody.appendChild(tr);
                        });

                        document.querySelectorAll('.timeline-step:not(.disabled-step)').forEach(btn => {
                            btn.addEventListener('click', async (e) => {
                                const stepTarget = e.currentTarget.closest('.timeline-step');
                                if (!stepTarget) return;

                                const ticketId = stepTarget.getAttribute('data-id');
                                const nuevoEstado = stepTarget.getAttribute('data-estado');

                                const seguro = confirm(`¿Estás seguro de avanzar el estado del ticket a "${nuevoEstado}"? Una vez cambiado, no podrás retroceder.`);
                                if (!seguro) return;

                                await actualizarEstadoTicket(ticketId, nuevoEstado);
                            });
                        });

                        document.querySelectorAll('.btn-detalles').forEach(btn => {
                            btn.addEventListener('click', () => {
                                const ticketId = btn.getAttribute('data-id');
                                const ticket = data.tickets.find(t => t.id == ticketId);
                                if (ticket) mostrarDetalleTicket(ticket);
                            });
                        });
                    }
                } catch (error) {
                    console.error('Error al cargar tickets:', error);
                }
            }

            async function actualizarEstadoTicket(ticketId, nuevoEstado) {
                try {
                    const response = await fetch('api/update_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `ticket_id=${ticketId}&estado=${encodeURIComponent(nuevoEstado)}`
                    });
                    const data = await response.json();
                    if (data.success) {
                        cargarTickets();
                    } else {
                        alert(data.message);
                    }
                } catch (err) {
                    console.error('Error de red:', err);
                }
            }

            function mostrarDetalleTicket(ticket) {
                manageTicketId.textContent = '#' + ticket.numero;
                let imagenesHtml = '<p style="margin-top:15px; color:#666;">No hay archivos adjuntos en este ticket.</p>';
                
                if (ticket.imagenes && ticket.imagenes.length > 0) {
                    imagenesHtml = '<div style="margin-top:15px;"><p><strong>Archivo(s) Adjunto(s):</strong></p><div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:8px;">';
                    ticket.imagenes.forEach(img => {
                        imagenesHtml += `<a href="${img.url}" target="_blank" rel="noopener noreferrer"><img src="${img.url}" style="width:100px; height:100px; object-fit:cover; border-radius:4px; border:1px solid #ccc;" title="Abrir imagen completa"></a>`;
                    });
                    imagenesHtml += '</div></div>';
                }

                manageDetails.innerHTML = `
                    <p><strong>Solicitante:</strong> ${escapeHTML(ticket.nombre_solicitante)}</p>
                    <p><strong>Celular:</strong> ${escapeHTML(ticket.celular)}</p>
                    <p><strong>Email:</strong> ${escapeHTML(ticket.email || 'No proporcionado')}</p>
                    <p><strong>Tipo de Reporte:</strong> ${escapeHTML(ticket.tipo_reporte || 'No especificado')}</p>
                    <p><strong>Estado Actual:</strong> ${ticket.estado}</p>
                    <p><strong>Fecha:</strong> ${ticket.creado_en}</p>
                    <p style="margin-top:10px;"><strong>Descripción:</strong></p>
                    <p style="background:#f9f9f9; padding:10px; border-radius:4px; border:1px solid #ddd; margin-top:5px;">${escapeHTML(ticket.descripcion)}</p>
                    ${imagenesHtml}
                `;
                manageModal.style.display = 'flex';
            }

            function escapeHTML(str) {
                return str ? str.replace(/[&<>'"]/g, 
                    tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
                ) : '';
            }

            if (closeManage) closeManage.addEventListener('click', () => { manageModal.style.display = 'none'; });
            if (filterEstado) filterEstado.addEventListener('change', cargarTickets);
            if (btnRecargar) btnRecargar.addEventListener('click', cargarTickets);
        });
    </script>
</body>
</html>
