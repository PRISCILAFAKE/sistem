// Configuración
const API_BASE = './api';

// Elementos del DOM - General
const alertDiv = document.getElementById('alert');
const publicView = document.getElementById('publicView');
const adminPanel = document.getElementById('adminPanel');

// Elementos del DOM - Formulario Ticket
const formCrearTicket = document.getElementById('formCrearTicket');
const btnEnviar = document.getElementById('btnEnviar');

// Elementos del DOM - Admin/Login
const adminTrigger = document.getElementById('adminTrigger');
const loginModal = document.getElementById('loginModal');
const closeLogin = document.getElementById('closeLogin');
const formLogin = document.getElementById('formLogin');
const loginAlert = document.getElementById('loginAlert');
const btnLogout = document.getElementById('btnLogout');

// Elementos del DOM - Tabla Admin
const ticketsTbody = document.getElementById('ticketsTbody');
const filterEstado = document.getElementById('filterEstado');
const btnRecargar = document.getElementById('btnRecargar');

// Elementos del DOM - Gestión Admin
const manageModal = document.getElementById('manageModal');
const closeManage = document.getElementById('closeManage');
const formManage = document.getElementById('formManage');
const manageTicketIdLabel = document.getElementById('manageTicketId');
const manageDetailsDiv = document.getElementById('manageDetails');
const manageIdInput = document.getElementById('manageId');
const manageEstadoSelect = document.getElementById('manageEstado');

// Elementos del DOM - File Preview
const imagenInput = document.getElementById('imagen');
const fileLabel = document.getElementById('fileLabel');
const fileWrapper = document.getElementById('fileWrapper');
const hoverPreview = document.getElementById('hoverPreview');

// Event Listeners para Imagen
imagenInput.addEventListener('change', updateFileLabel);
fileWrapper.addEventListener('mousemove', showHoverPreview);
fileWrapper.addEventListener('mouseleave', () => hoverPreview.style.display = 'none');

/**
 * Actualizar texto y preparar previews múltiples
 */
function updateFileLabel() {
    const files = imagenInput.files;
    if (files.length > 0) {
        fileLabel.querySelector('span').textContent = files.length === 1 
            ? files[0].name 
            : `${files.length} imágenes seleccionadas`;
        
        hoverPreview.innerHTML = '';
        
        // Cargar miniaturas para el preview
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.marginBottom = '5px';
                hoverPreview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    } else {
        fileLabel.querySelector('span').textContent = 'Seleccionar Imagen';
        hoverPreview.innerHTML = '';
    }
}

/**
 * Mostrar preview siguiendo el mouse (Ajustado para que aparezca "encima")
 */
function showHoverPreview(e) {
    if (hoverPreview.innerHTML !== '') {
        hoverPreview.style.display = 'block';
        // Calcular posición para que aparezca arriba del puntero
        const rect = hoverPreview.getBoundingClientRect();
        hoverPreview.style.left = (e.pageX - (rect.width / 2)) + 'px';
        hoverPreview.style.top = (e.pageY - rect.height - 20) + 'px';
    }
}

// Event Listeners (Resto)
formCrearTicket.addEventListener('submit', crearTicket);
adminTrigger.addEventListener('click', () => loginModal.style.display = 'flex');
closeLogin.addEventListener('click', () => loginModal.style.display = 'none');
closeManage.addEventListener('click', () => manageModal.style.display = 'none');
formLogin.addEventListener('submit', loginAdmin);
formManage.addEventListener('submit', actualizarEstadoTicket);
btnLogout.addEventListener('click', logout);
btnRecargar.addEventListener('click', cargarTickets);
filterEstado.addEventListener('change', cargarTickets);

window.addEventListener('click', (e) => {
    if (e.target === loginModal) loginModal.style.display = 'none';
    if (e.target === manageModal) manageModal.style.display = 'none';
});

function crearTicket(e) {
    e.preventDefault();
    const formData = new FormData(formCrearTicket);
    btnEnviar.disabled = true;
    btnEnviar.textContent = 'Enviando...';

    fetch(`${API_BASE}/create_ticket.php`, { method: 'POST', body: formData })
    .then(response => response.json())
    .then(data => {
        btnEnviar.disabled = false;
        btnEnviar.textContent = 'Enviar Solicitud';
        if (data.success) {
            mostrarAlert(`✓ ¡Ticket #${String(data.ticket_id).padStart(4, '0')} creado con éxito!`, 'success');
            formCrearTicket.reset();
            fileLabel.querySelector('span').textContent = 'Seleccionar Imagen';
            hoverPreview.innerHTML = '';
        } else {
            mostrarAlert(`Error: ${data.message}`, 'error');
        }
    })
    .catch(error => {
        btnEnviar.disabled = false;
        btnEnviar.textContent = 'Enviar Solicitud';
        mostrarAlert(`Error de conexión: ${error.message}`, 'error');
    });
}

function loginAdmin(e) {
    e.preventDefault();
    const formData = new FormData();
    formData.append('usuario', document.getElementById('adminUser').value);
    formData.append('password', document.getElementById('adminPass').value);

    fetch(`${API_BASE}/login.php`, { method: 'POST', body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loginModal.style.display = 'none';
            formLogin.reset();
            mostrarVistaAdmin();
        } else {
            loginAlert.textContent = data.message;
            loginAlert.style.display = 'block';
            setTimeout(() => loginAlert.style.display = 'none', 3000);
        }
    });
}

function mostrarVistaAdmin() {
    publicView.classList.add('hidden');
    adminPanel.style.display = 'block';
    cargarTickets();
}

function logout() {
    adminPanel.style.display = 'none';
    publicView.classList.remove('hidden');
}

function cargarTickets() {
    const estado = filterEstado.value;
    let url = `${API_BASE}/get_tickets.php`;
    if (estado) url += `?estado=${encodeURIComponent(estado)}`;

    ticketsTbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Cargando solicitudes...</td></tr>';

    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            ticketsTbody.innerHTML = '';
            if (data.tickets.length === 0) {
                ticketsTbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No hay solicitudes registradas</td></tr>';
                return;
            }
            data.tickets.forEach(ticket => {
                const tr = document.createElement('tr');
                const rowData = JSON.stringify(ticket).replace(/'/g, "&apos;");
                tr.innerHTML = `
                    <td><strong>#${ticket.numero}</strong></td>
                    <td>${ticket.nombre_solicitante}</td>
                    <td>${ticket.celular}</td>
                    <td>${ticket.creado_en}</td>
                    <td><span class="badge badge-${ticket.prioridad}">${ticket.prioridad}</span></td>
                    <td>${ticket.estado}</td>
                    <td>
                        <button style="padding: 5px 10px; font-size: 0.8em; width: auto;" onclick='abrirGestion(${rowData})'>Gestionar</button>
                    </td>
                `;
                ticketsTbody.appendChild(tr);
            });
        }
    });
}

window.abrirGestion = function(ticket) {
    manageTicketIdLabel.textContent = `#${ticket.numero}`;
    manageIdInput.value = ticket.id;
    manageEstadoSelect.value = ticket.estado;
    
    let htmlContent = `
        <strong>Solicitante:</strong> ${ticket.nombre_solicitante}<br>
        <strong>Problema:</strong> ${ticket.descripcion}<br>
    `;

    if (ticket.imagenes && ticket.imagenes.length > 0) {
        htmlContent += `<div style="margin-top: 15px;">
            <strong>Evidencias adjuntas (${ticket.imagenes.length}):</strong><br>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                ${ticket.imagenes.map(img => `
                    <img src="${img.url}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 1px solid #ddd;" onclick="window.open(this.src)">
                `).join('')}
            </div>
        </div>`;
    }

    manageDetailsDiv.innerHTML = htmlContent;
    manageModal.style.display = 'flex';
};

function actualizarEstadoTicket(e) {
    e.preventDefault();
    const formData = new FormData();
    formData.append('ticket_id', manageIdInput.value);
    formData.append('estado', manageEstadoSelect.value);

    fetch(`${API_BASE}/update_status.php`, { method: 'POST', body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            manageModal.style.display = 'none';
            cargarTickets();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function mostrarAlert(mensaje, tipo) {
    alertDiv.className = `alert ${tipo}`;
    alertDiv.textContent = mensaje;
    alertDiv.style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    setTimeout(() => alertDiv.style.display = 'none', 5000);
}
