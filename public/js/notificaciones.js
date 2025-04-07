// Verifica si hay preferencia guardada en localStorage
const sonidoActivado = localStorage.getItem('notificaciones_sonido') === 'true';

// Inicializa el checkbox y el ícono según la preferencia guardada
document.getElementById('toggle-sound').checked = sonidoActivado;
const soundIcon = document.getElementById('sound-icon');

// Cambiar el ícono basado en la preferencia
if (!sonidoActivado) {
    soundIcon.classList.remove('fa-volume-up');
    soundIcon.classList.add('fa-volume-mute');
}

// Evento para actualizar la preferencia del usuario y el ícono
document.getElementById('toggle-sound').addEventListener('change', (e) => {
    const isChecked = e.target.checked;
    localStorage.setItem('notificaciones_sonido', isChecked);

    // Cambiar el ícono según el estado
    if (isChecked) {
        soundIcon.classList.remove('fa-volume-mute');
        soundIcon.classList.add('fa-volume-up');
    } else {
        soundIcon.classList.remove('fa-volume-up');
        soundIcon.classList.add('fa-volume-mute');
    }
});


let userInteracted = false;

// Asegurándonos de que el sonido se define antes de cualquier uso
const notificationSound = new Audio('/sounds/light-hearted-message-tone.mp3');
document.addEventListener('click', () => {
    userInteracted = true;
});

function playNotificationSound() {
    const sonidoActivado = localStorage.getItem('notificaciones_sonido') === 'true';

    if (sonidoActivado && userInteracted) {
        notificationSound.play().catch(error => {
            console.log('🔇 Reproducción bloqueada:', error);
        });
    }
}

Pusher.logToConsole = false;

var pusher = new Pusher('b0d28aca280947c65ff5', {
    cluster: 'us2',
    authEndpoint: "/broadcasting/auth",
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
});

var userId = document.querySelector('meta[name="user-id"]').getAttribute('content');
let sistemaCount = parseInt(document.getElementById('sistema-badge').innerText) || 0;

const currentTime = new Date().toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
});


if (userId) {
    var mensajesChannel = pusher.subscribe('private-user.' + userId);
    var sistemaChannel = pusher.subscribe('private-user.' + userId);
    mensajesChannel.bind('new.message', function (data) {
        if (data.message) {
            // 🔔 Sonido
            playNotificationSound();

            // 📨 Toastr
            toastr.info(`
            <div class="notification-content" onclick="window.location.href='${window.location.origin}/mensajes/buzon'" style="cursor: pointer;">
                <i class="fas fa-envelope text-blue-500"></i>
                <strong>${data.sender}</strong> te envió un mensaje:<br>
                <span>"${data.message}"</span><br>
                <small><i class="fas fa-clock"></i> ${data.time}</small>
            </div>`,
                'Nuevo Mensaje', {
                closeButton: true,
                progressBar: true,
                timeOut: 10000,
                positionClass: 'toast-top-right',
                enableHtml: true
            }
            );

            // 🔢 Actualizar contador de mensajes
            let badge = document.getElementById('mensajes-count');
            let currentCount = parseInt(badge.innerText) || 0;
            badge.innerText = currentCount + 1;

            // 📥 Agregar mensaje al dropdown
            const nuevaNoti = `
            <a href="/mensajes/buzon" class="dropdown-item">
                <i class="fas fa-user text-primary mr-2"></i>
                ${data.sender}: ${data.message.substring(0, 30)}...
                <span class="float-right text-muted text-sm">Justo ahora</span>
            </a>
            <div class="dropdown-divider"></div>
        `;

            const dropdown = document.getElementById('mensajes-dropdown');
            dropdown.insertAdjacentHTML('afterbegin', nuevaNoti);

            // 📝 Actualizar header del dropdown
            const header = dropdown.querySelector('.dropdown-header');
            if (header) {
                header.innerText = (currentCount + 1) + ' Mensajes nuevos';
            }
        } else {
            console.log("❌ No hay mensaje en los datos:", data);
        }
    });

    sistemaChannel.bind('subir.archivo', function (data) {
        console.log('📢 Notificación de archivo subido recibida:', data);

        if (data.message) {
            // 🔔 Sonido
            playNotificationSound();

            // 📨 Toastr
            toastr.info(
                `<div class="notification-content">
                    <i class="fas fa-file-upload text-green-500"></i>
                    <span><strong>${data.message}</strong></span><br>
                    <small><i class="fas fa-clock"></i> ${currentTime}</small>
                </div>`,
                '📤 Recordatorio', {
                closeButton: true,
                progressBar: true,
                timeOut: 5000,
                positionClass: 'toast-top-right',
                enableHtml: true
            }
            );

            // Actualizar el contador de notificaciones
            sistemaCount++;
            document.getElementById('sistema-badge').innerText = sistemaCount;
            document.getElementById('sistema-header').innerText = `${sistemaCount} Notificaciones del sistema`;
            const nuevaNoti = `
                <a href="#" class="dropdown-item">
                    <i class="fas fa-info-circle text-info mr-2"></i>
                    ${data.message}
                    <span class="float-right text-muted text-sm">Justo ahora</span>
                </a>
                <div class="dropdown-divider"></div>
            `;
            document.getElementById('sistema-lista').insertAdjacentHTML('afterbegin', nuevaNoti);

        } else {
            console.log("❌ No hay mensaje de archivo en los datos:", data);
        }
    });

    sistemaChannel.bind('archivo.subido', function (data) {
        console.log('📢 Notificación de archivo subido recibida:', data);

        if (data.message) {
            // 🔔 Sonido
            playNotificationSound();

            // 📨 Toastr
            toastr.success(
                `<div class="notification-content">
                    <i class="fas fa-file-upload text-green-500"></i>
                    <span><strong>${data.message}</strong></span><br>
                    <small><i class="fas fa-clock"></i> ${currentTime}</small>
                </div>`,
                '😎 ¡Gracias por proporcionar tus datos!', {
                closeButton: true,
                progressBar: true,
                timeOut: 5000,
                positionClass: 'toast-top-right',
                enableHtml: true
            }
            );

            // Actualizar el contador de notificaciones
            sistemaCount++;
            document.getElementById('sistema-badge').innerText = sistemaCount;
            document.getElementById('sistema-header').innerText = `${sistemaCount} Notificaciones del sistema`;
            const nuevaNoti = `
                <a href="#" class="dropdown-item">
                    <i class="fas fa-info-circle text-info mr-2"></i>
                    ${data.message}
                    <span class="float-right text-muted text-sm">Justo ahora</span>
                </a>
                <div class="dropdown-divider"></div>
            `;
            document.getElementById('sistema-lista').insertAdjacentHTML('afterbegin', nuevaNoti);
        } else {
            console.log("❌ No hay mensaje de archivo en los datos:", data);
        }
    });

    // Evento: Postulante aceptado
    sistemaChannel.bind('postulante.aceptado', function (data) {
        if (data.message) {
            // 🔔 Sonido
            playNotificationSound();

            // 📨 Toastr
            toastr.success(
                `<div class="notification-content">
                    <span><strong>${data.message}</strong></span><br>
                    <small><i class="fas fa-clock"></i> ${currentTime}</small>
                </div>`,
                '🥳 ¡Felicidades! Has sido aceptado', {
                closeButton: true,
                progressBar: true,
                timeOut: 7000,
                positionClass: 'toast-top-right',
                enableHtml: true
            }
            );

            // Actualizar el contador de notificaciones
            sistemaCount++;
            document.getElementById('sistema-badge').innerText = sistemaCount;
            document.getElementById('sistema-header').innerText = `${sistemaCount} Notificaciones del sistema`;
            const nuevaNoti = `
                <a href="#" class="dropdown-item">
                    <i class="fas fa-info-circle text-info mr-2"></i>
                    ${data.message}
                    <span class="float-right text-muted text-sm">Justo ahora</span>
                </a>
                <div class="dropdown-divider"></div>
            `;
            document.getElementById('sistema-lista').insertAdjacentHTML('afterbegin', nuevaNoti);
        } else {
            console.log("❌ No hay mensaje en los datos:", data);
        }
    });

    // Evento: Pago matrícula
    sistemaChannel.bind('pago.matricula', function (data) {
        if (data.message) {
            // 🔔 Sonido
            playNotificationSound();

            // 📨 Toastr
            toastr.info(
                `<div class="notification-content" onclick="window.location.href='${window.location.origin}/pagos/matricula'" style="cursor: pointer;">
                    <span><strong>${data.message}</strong></span><br>
                    <small><i class="fas fa-clock"></i> ${currentTime}</small>
                </div>`,
                '👀 ¡Verificar Pago!', {
                closeButton: true,
                progressBar: true,
                timeOut: 8000,
                positionClass: 'toast-top-right',
                enableHtml: true
            }
            );

            // Actualizar el contador de notificaciones
            sistemaCount++;
            document.getElementById('sistema-badge').innerText = sistemaCount;
            document.getElementById('sistema-header').innerText = `${sistemaCount} Notificaciones del sistema`;
            const nuevaNoti = `
                <a href="#" class="dropdown-item">
                    <i class="fas fa-info-circle text-info mr-2"></i>
                    ${data.message}
                    <span class="float-right text-muted text-sm">Justo ahora</span>
                </a>
                <div class="dropdown-divider"></div>
            `;
            document.getElementById('sistema-lista').insertAdjacentHTML('afterbegin', nuevaNoti);
        } else {
            console.log("❌ No hay mensaje de archivo en los datos:", data);
        }
    });

    // Evento: Matrícula exitosa
    sistemaChannel.bind('matricula.exito', function (data) {
        if (data.message) {
            // 🔔 Sonido
            playNotificationSound();

            // 📨 Toastr
            toastr.info(
                `<div class="notification-content" style="cursor: pointer;">
                    <span><strong>${data.message}</strong></span><br>
                    <small><i class="fas fa-clock"></i> ${currentTime}</small>
                </div>`,
                '🥳 ¡Felicidades!', {
                closeButton: true,
                progressBar: true,
                timeOut: 8000,
                positionClass: 'toast-top-right',
                enableHtml: true
            }
            );

            // Actualizar el contador de notificaciones
            sistemaCount++;
            document.getElementById('sistema-badge').innerText = sistemaCount;
            document.getElementById('sistema-header').innerText = `${sistemaCount} Notificaciones del sistema`;
            const nuevaNoti = `
                <a href="#" class="dropdown-item">
                    <i class="fas fa-info-circle text-info mr-2"></i>
                    ${data.message}
                    <span class="float-right text-muted text-sm">Justo ahora</span>
                </a>
                <div class="dropdown-divider"></div>
            `;
            document.getElementById('sistema-lista').insertAdjacentHTML('afterbegin', nuevaNoti);
        } else {
            console.log("❌ No hay mensaje de archivo en los datos:", data);
        }
    });

}