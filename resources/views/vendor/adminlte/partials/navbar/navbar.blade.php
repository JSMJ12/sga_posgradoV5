<nav
    class="main-header navbar {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }} {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">
    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        
        <label for="toggle-sound" style="cursor: pointer;">
            <i id="sound-icon" class="fas fa-volume-up" style="font-size: 10px; margin-left: 10px;" title="Apagar sonido notificacion"></i>
        </label>
        <input type="checkbox" id="toggle-sound" checked style="display: none;" />
        
        @php
            $notificaciones = auth()->user()->unreadNotifications;

            $mensajes = $notificaciones->filter(fn($n) => str_contains($n->type, 'NewMessageNotification'));
            $sistema = $notificaciones->reject(fn($n) => str_contains($n->type, 'NewMessageNotification'));

            $mensajesCount = $mensajes->count();
            $sistemaCount = $sistema->count();
        @endphp

        {{-- 🔔 Notificaciones del sistema --}}
        <li class="nav-item dropdown" id="sistemaDropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" id="sistemaToggle">
                <i class="far fa-bell" style="font-size: 25px;"></i>
                <span id="sistema-badge" class="badge badge-warning navbar-badge" style="font-size: 0.7rem;">
                    {{ $sistemaCount > 0 ? $sistemaCount : '0' }}
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right animate__animated animate__fadeIn">
                <span class="dropdown-header" id="sistema-header">
                    {{ $sistemaCount }} Notificaciones del sistema
                </span>
                <div class="dropdown-divider"></div>

                <div id="sistema-lista">
                    @foreach ($sistema->take(5) as $noti)
                        <a href="#" class="dropdown-item" onclick="marcarLeido('{{ $noti->id }}')">
                            <i class="fas fa-info-circle text-info mr-2"></i>
                            {{ Str::limit($noti->data['message'], 100) }}
                            <span class="float-right text-muted text-sm">{{ $noti->created_at->diffForHumans() }}</span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                </div>
            </div>
        </li>

        {{-- ✉️ Mensajes entre usuarios --}}
        <li class="nav-item dropdown" id="dropdown-mensajes">
            <a class="nav-link" data-toggle="dropdown" href="#" id="mensajeToggle">
                <i class="far fa-envelope" style="font-size: 25px;"></></i>
                <span class="badge badge-primary navbar-badge" id="mensajes-count" style="font-size: 0.7rem;">
                    {{ $mensajesCount > 0 ? $mensajesCount : '0' }}
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="mensajes-dropdown">
                <span class="dropdown-header" id="mensajes-header">{{ $mensajesCount }} Mensajes nuevos</span>
                <div class="dropdown-divider"></div>

                @foreach ($mensajes->take(5) as $noti)
                    <a href="{{ url('/mensajes/buzon') }}" class="dropdown-item"
                        onclick="marcarLeido('{{ $noti->id }}')">
                        <i class="fas fa-user text-primary mr-2"></i>
                        {{ $noti->data['sender']['name'] ?? 'Usuario' }}: {{ Str::limit($noti->data['message'], 30) }}
                        <span class="float-right text-muted text-sm">{{ $noti->created_at->diffForHumans() }}</span>
                    </a>
                    <div class="dropdown-divider"></div>
                @endforeach

                <a href="{{ route('messages.index') }}" class="dropdown-item dropdown-footer">Ir a la bandeja</a>
            </div>
        </li>

        {{-- Custom right links --}}
        @yield('content_top_nav_right')

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

        {{-- Right sidebar toggler --}}
        @if (config('adminlte.right_sidebar'))
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>
</nav>
@auth
    <style>
        /* Estilos personalizados para Toastr */
        .toast-message {
            display: flex;
            align-items: center;
        }

        .toast-message i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .toast-message .notification-content {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="{{ asset('js/notificaciones.js') }}"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sistemaToggle = document.getElementById('sistemaToggle');

            sistemaToggle.addEventListener('click', function() {
                // Llamada AJAX solo una vez
                if (!sistemaToggle.dataset.marked) {
                    fetch("{{ route('notificaciones.sistema.leidas') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log("✅ Notificaciones marcadas como leídas");
                                document.getElementById('sistema-badge').style.display = "none";
                                document.getElementById('sistema-header').textContent =
                                    "0 Notificaciones del sistema";
                            }
                        });
                    sistemaToggle.dataset.marked = true;
                }
            });

            const mensajeToggle = document.getElementById('mensajeToggle');
            const sistemaBadge = document.getElementById('mensajes-count');
            const sistemaHeader = document.getElementById('mensajes-header');

            // Evitar múltiples llamadas AJAX
            if (!mensajeToggle.dataset.marked) {
                mensajeToggle.addEventListener('click', function() {
                    // Llamada AJAX para marcar notificaciones como leídas
                    fetch("{{ route('notificaciones.mensajes.leidas') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Actualizar la visualización
                                console.log("✅ Notificaciones marcadas como leídas");

                                // Ocultar el badge de nuevos mensajes
                                sistemaBadge.style.display = "none";

                                // Actualizar el header de la lista
                                sistemaHeader.textContent = "0 Mensajes nuevos";

                                // Marcar que ya se ha realizado la acción
                                mensajeToggle.dataset.marked = true;
                            }
                        })
                        .catch(error => {
                            console.error("❌ Error al marcar las notificaciones como leídas:", error);
                        });
                });
            }
        });
    </script>

@endauth

<style>
    .dropdown-item {
        white-space: normal !important;
        word-wrap: break-word;
        max-width: 300px;
    }
</style>
