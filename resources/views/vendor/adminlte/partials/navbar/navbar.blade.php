<nav
    class="main-header navbar {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }} {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">
    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
        {{-- Mostrar el icono de notificaciones con el número --}}

    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Custom right links --}}
        @yield('content_top_nav_right')
        <li class="nav-item">
            <a id="notificacionesModalLink" class="nav-link" href="#">
                <i class="fa fa-bell"></i> {{-- Icono de campana para notificaciones --}}
                <span class="badge badge-warning" id="cantidadDeNuevasNotificaciones">
                    {{ $cantidadDeNuevasNotificaciones ?? 0 }}
                </span> {{-- Número de nuevos mensajes --}}
            </a>
        </li>
        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- User menu link --}}
        @if (Auth::user())
            @if (config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if (config('adminlte.right_sidebar'))
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>
</nav>

<div class="modal fade" id="notificacionesModal" tabindex="-1" role="dialog"
    aria-labelledby="notificacionesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="notificacionesModalLabel">Notificaciones</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Aquí se llenarán dinámicamente las notificaciones mediante JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<script>
    $(document).ready(function() {
        const notificationModal = $('#notificacionesModal');
        const notificationBody = notificationModal.find('.modal-body');

        function renderNotification(type, id, senderName, message) {
            const senderInfo = senderName ? `De: ${senderName}<br>` : '';
            return `<li data-message-id="${id}" data-type="${type}">${senderInfo}Mensaje: ${message}</li>`;
        }

        function handleNotificationClick() {
            const messageId = $(this).data('message-id');
            const notificationType = $(this).data('type');

            const redirectMap = {
                'NewMessageNotification': '/mensajes/buzon/',
                'PostulanteAceptadoNotification': '/inicio',
                'SubirArchivoNotification': '/inicio',
                'MatriculaExito': '/'
            };

            if (redirectMap[notificationType]) {
                window.location.href = redirectMap[notificationType];
            }
        }

        function updateModalContent(data) {
            notificationBody.empty();

            if (data.notificaciones && data.notificaciones.length > 0) {
                const notifications = data.notificaciones.map(notificacion => {
                    const {
                        id,
                        data: {
                            type,
                            message,
                            sender
                        }
                    } = notificacion;
                    return renderNotification(type, id, sender?.name, message);
                }).join('');
                notificationBody.html(`<ul>${notifications}</ul>`);
                notificationBody.find('li').click(handleNotificationClick);
            } else {
                notificationBody.html('<p>No hay notificaciones.</p>');
            }

            $('#cantidadDeNuevasNotificaciones').text(data.cantidadNotificacionesNuevas);
        }

        function subscribeToPusher() {
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}'
            });

            const channel = pusher.subscribe('brief-valley-786');

            channel.bind('App\\Events\\NewMessageNotificationEvent', handlePusherEvent);
            channel.bind('App\\Events\\PostulanteAceptado', handlePusherEvent);
            channel.bind('App\\Events\\SubirArchivoEvent', handlePusherEvent);
        }

        function handlePusherEvent(data) {
            const {
                type,
                id,
                sender,
                message
            } = data;
            const newNotification = renderNotification(type, id, sender?.name, message);

            if (notificationBody.find('ul').length === 0) {
                notificationBody.html(`<ul>${newNotification}</ul>`);
            } else {
                notificationBody.find('ul').prepend(newNotification);
            }

            notificationBody.find('li').first().click(handleNotificationClick);
        }

        $('#notificacionesModalLink').click(function(event) {
            event.preventDefault();

            $.get('/notificaciones')
                .done(updateModalContent)
                .fail(() => {
                    notificationBody.html('<p>Error al obtener las notificaciones.</p>');
                    $('#cantidadDeNuevasNotificaciones').text('0');
                })
                .always(() => notificationModal.modal('show'));

            subscribeToPusher();
        });
    });
</script>

<script>
    $(document).ready(function() {
        function actualizarContador() {
            $.get('/cantidad-notificaciones', function(data) {
                $('#cantidadDeNuevasNotificaciones').text(data.cantidadNotificacionesNuevas);
            }).fail(function() {
                $('#cantidadDeNuevasNotificaciones').text('0');
            });
        }

        setInterval(actualizarContador, 1000);
    });
</script>
