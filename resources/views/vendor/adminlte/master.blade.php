<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')
    @auth
        <meta name="user-id" content="{{ auth()->user()->id }}">
    @endauth

    {{-- Page Title --}}
    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')

    {{-- Base Stylesheets --}}
    @if (!config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

        @if (config('adminlte.google_fonts.allowed', true))
            <link rel="stylesheet"
                href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        @endif
    @else
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @endif

    {{-- Extra Configured Plugins Stylesheets --}}
    @include('adminlte::plugins', ['type' => 'css'])

    {{-- Livewire Styles --}}
    @if (config('adminlte.livewire'))
        @if (intval(app()->version()) >= 7)
            @livewireStyles
        @else
            <livewire:styles />
        @endif
    @endif
    <style>
        .main-sidebar {
            overflow-y: auto !important;
            max-height: 100vh;
        }
        .bg-custom-green {
            background-color: #04840c !important;
            color: #fff !important;
        }
        .nav-sidebar .nav-item > .nav-link.active {
            background-color: #04840c !important;
            color: #fff !important;
        }

        .nav-sidebar .nav-item > .nav-link:hover {
            background-color: #04840c !important;
            color: #fff !important;
        }
        /* ✅ Cambiar color de header */
table.dataTable thead {
    background-color: #04840c;
    color: white;
}

/* ✅ Hover fila */
table.dataTable tbody tr:hover {
    background-color: #eaf7ec;
}

/* ✅ Paginación activa */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #04840c !important;
    color: white !important;
    border: 1px solid transparent;
    border-radius: 4px;
}

/* ✅ Paginación hover */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #04840c !important;
    color: white !important;
}

/* ✅ Búsqueda y select */
.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
    border-radius: 6px;
    border: 1px solid #ced4da;
    padding: 4px;
}
/* ✅ Encabezado de la tabla */
table.dataTable thead {
    background-color: #04840c;
    color: white;
}

/* ✅ Hover de fila */
table.dataTable tbody tr:hover {
    background-color: #eaf7ec;
}

/* ✅ Botón de paginación activo */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #77bf7e !important;
    color: white !important;
    border: 1px solid transparent;
    border-radius: 4px;
}

/* ✅ Botón de paginación hover */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #77bf7e !important;
    color: white !important;
    border: 1px solid transparent;
}

/* ✅ Input de búsqueda y select de registros */
.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
    border-radius: 6px;
    border: 1px solid #ced4da;
    padding: 4px;
}

/* ✅ Estilo para "Mostrar X registros" */
.dataTables_wrapper .dataTables_length label {
    font-weight: 500;
}

/* ✅ Estilo para el filtro */
.dataTables_wrapper .dataTables_filter label {
    font-weight: 500;
}

/* Estilo base para los botones de paginación */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    background: #f4f8f6;
    color: #04840c !important;
    border: 1px solid #b6e2c6;
    border-radius: 8px;
    margin: 0 2px;
    padding: 6px 14px;
    font-weight: 500;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    box-shadow: 0 1px 2px rgba(4,132,12,0.04);
}

/* Botón activo */
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #04840c 60%, #77bf7e 100%);
    color: #fff !important;
    border: 1.5px solid #04840c;
    box-shadow: 0 2px 8px rgba(4,132,12,0.10);
}

/* Hover */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
    background: #eaf7ec;
    color: #04840c !important;
    border: 1.5px solid #77bf7e;
    cursor: pointer;
}

/* Botón deshabilitado */
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    background: #f4f8f6;
    color: #bdbdbd !important;
    border: 1px solid #e0e0e0;
    cursor: not-allowed;
    opacity: 0.7;
}

.page-link {
    background: transparent !important;
    color: #222 !important;
    border: 1px solid #dee2e6;
    font-weight: 500;
    border-radius: 6px;
    transition: background 0.2s, color 0.2s;
}
.page-link:hover, .page-link:focus {
    background: #eaf7ec !important;
    color: #04840c !important;
    border-color: #77bf7e;
}
    </style>

    {{-- Custom Stylesheets (post AdminLTE) --}}
    @yield('adminlte_css')

    {{-- Favicon --}}
    @if (config('adminlte.use_ico_only'))
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    @elseif(config('adminlte.use_full_favicon'))
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicons/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicons/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicons/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicons/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicons/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicons/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicons/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicons/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicons/android-icon-192x192.png') }}">
        <link rel="manifest" crossorigin="use-credentials" href="{{ asset('favicons/manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    @endif
    <style>
        .content-header h1 {
            color: black;
            font-family: "Times New Roman", Times, serif;
        }
    </style>

</head>

<body class="@yield('classes_body')" @yield('body_data')>

    {{-- Body Content --}}
    @yield('body')

    {{-- Base Scripts --}}
    @if (!config('adminlte.enabled_laravel_mix'))
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @else
        <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    @endif

    {{-- Extra Configured Plugins Scripts --}}
    @include('adminlte::plugins', ['type' => 'js'])

    {{-- Livewire Script --}}
    @if (config('adminlte.livewire'))
        @if (intval(app()->version()) >= 7)
            @livewireScripts
        @else
            <livewire:scripts />
        @endif
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Verificar si hay errores en la sesión
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    html: `
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                    confirmButtonText: 'Aceptar'
                });
            @endif

            // Verificar si hay un mensaje de éxito en la sesión
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'Aceptar'
                });
            @endif

            // Verificar si hay un mensaje de advertencia en la sesión
            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: '¡Advertencia!',
                    text: '{{ session('warning') }}',
                    confirmButtonText: 'Aceptar'
                });
            @endif

            // Verificar si hay un mensaje de información en la sesión
            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Información',
                    text: '{{ session('info') }}',
                    confirmButtonText: 'Aceptar'
                });
            @endif
        });
    </script>
    
   <script type="module" src="/js/notificaciones-sw.js"></script>
   <script src="{{ asset('js/sw.js') }}"></script>
    {{-- Custom Scripts --}}
    @yield('adminlte_js')

</body>

</html>
