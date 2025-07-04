<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => '',
    'title_prefix' => 'SGA Posgrado | ',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => true,
    'use_full_favicon' => true,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>SGA </b>Posgrado',
    'logo_img' => 'vendor/adminlte/dist/img/LOGO.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/LOGO.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 100,
            'height' => 100,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/posgrado-20.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 150,
            'height' => 100,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-success',
    'usermenu_image' => true,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => 'bg-green',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-light-success elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => true,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => '/inicio',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'Buscar',
        ],

        // Acceso General
        ['header' => 'Administración', 'can' => 'dashboard_admin'],
        [
            'text' => 'Usuarios',
            'route'  => 'usuarios.index',
            'icon' => 'fas fa-fw fa-users',
            'can' => 'dashboard_admin'
        ],
        [
            'text' => 'Secretarios',
            'route'  => 'secretarios.index',
            'icon' => 'fas fa-fw fa-user-tie',
            'can' => 'dashboard_admin'
        ],
        [
            'text' => 'Maestrías',
            'route'  => 'maestrias.index',
            'icon' => 'fas fa-fw fa-graduation-cap',
            'can' => 'dashboard_admin'
        ],
        [
            'text' => 'Aulas',
            'route'  => 'aulas.index',
            'icon' => 'fas fa-chalkboard',
            'can' => 'dashboard_admin'
        ],
        [
            'text' => 'Periodos Académicos',
            'route'  => 'periodos_academicos.index',
            'icon' => 'fas fa-clipboard-list',
            'can' => 'dashboard_admin'
        ],
        [
            'text' => 'Secciones',
            'route'  => 'secciones.index',
            'icon' => 'fas fa-cubes',
            'can' => 'dashboard_admin'
        ],
        [
            'text' => 'Descuento',
            'route' => 'descuentos.index',
            'icon' => 'fa fa-percent',
            'can' => 'dashboard_admin'
        ],

        ['header' => 'Dirección Académica', 'can' => 'dashboard_director'],
        [
            'text' => 'Dirección',
            'route' => 'dashboard_director',
            'icon' => 'fas fa-fw fa-tachometer-alt',
            'can' => 'dashboard_director'
        ],

        ['header' => 'Coordinación', 'can' => 'dashboard_coordinador'],
        [
            'text' => 'Coordinación de Maestría',
            'route'  => 'dashboard_coordinador',
            'icon' => 'fas fa-fw fa-home',
            'can' => 'dashboard_coordinador'
        ],
        [
            'text' => 'Solicitudes de Tesis',
            'route'  => 'tesis.index',
            'icon' => 'fas fa-fw fa-graduation-cap',
            'can' => 'dashboard_coordinador'
        ],
        [
            'text' => 'Examen Complexivo',
            'route' => 'examen_complexivo.index',
            'icon' => 'fas fa-fw fa-pencil-alt',
            'can' => 'dashboard_coordinador'
        ],
        [
            'text' => 'Docentes',
            'route'  => 'docentes.index',
            'icon' => 'fas fa-fw fa-chalkboard-teacher',
            'can' => ['secretarios.crear', 'dashboard_coordinador']
        ],
        [
            'text' => 'Postulantes',
            'icon' => 'fas fa-book-reader',
            'route'  => 'postulaciones.index',
            'can' => ['secretarios.crear', 'dashboard_coordinador']
        ],
        [
            'text' => 'Alumnos',
            'route'  => 'alumnos.index',
            'icon' => 'fas fa-fw fa-user-graduate',
            'can' => ['secretarios.crear', 'dashboard_coordinador']
        ],
        [
            'text' => 'Cohortes',
            'route'  => 'cohortes.index',
            'icon' => 'fas fa-university',
            'can' => ['secretarios.crear', 'dashboard_coordinador']
        ],

        ['header' => 'Docencia', 'can' => 'dashboard_docente'],
        [
            'text' => 'Administración Docencia',
            'route' => 'dashboard_docente',
            'icon' => 'fas fa-chalkboard-teacher',
            'can' => 'dashboard_docente'
        ],

        ['header' => 'Secretaría EPSU', 'can' => 'dashboard_secretario_epsu'],
        [
            'text' => 'Pagos',
            'route'  => 'pagos.index',
            'icon' => 'fas fa-fw fa-dollar-sign',
            'can' => 'dashboard_secretario_epsu'
        ],
        [
            'text' => 'Descuento',
            'route' => 'descuentos.alumnos',
            'icon' => 'fa fa-percent',
            'can' => 'dashboard_secretario_epsu'
        ],

        ['header' => 'Secretaría Académica', 'can' => 'dashboard_secretario'],
        [
            'text' => 'Examen Complexivo',
            'route' => 'examen-complexivo.calificar',
            'icon' => 'fas fa-fw fa-pencil-alt',
            'can' => 'secretarios.crear'
        ],
        [
            'text' => 'Tasa de Titulación',
            'route' => 'tasa_titulacion.index',
            'icon' => 'fas fa-fw fa-chart-line',
            'can' => 'secretarios.crear'
        ],

        ['header' => 'Estudiantes', 'can' => 'dashboard_alumno'],
        [
            'text' => 'Pagos',
            'route' => 'pagos.pago',
            'icon' => 'fas fa-fw fa-dollar-sign',
            'can' => 'alumno_descuento'
        ],
        [
            'text' => 'Datos Personales',
            'route' => 'edit_datosAlumnos',
            'icon' => 'fas fa-fw fa-user',
            'can' => 'alumno_descuento'
        ],
        [
            'text' => 'Notas',
            'route'  => 'dashboard_alumno.notas',
            'icon' => 'far fa-fw fa-file-alt',
            'can' => 'dashboard_alumno'
        ],

        ['header' => 'Postulación', 'can' => 'dashboard_postulante'],
        [
            'text' => 'Documentación',
            'route'  => 'dashboard_postulante',
            'icon' => 'far fa-fw fa-file-alt',
            'can' => 'dashboard_postulante'
        ],
        [
            'text' => 'Editar Postulación',
            'route' => 'postulaciones.edit',
            'icon' => 'fas fa-user-edit',
            'can'  => 'dashboard_postulante'
        ],

        ['header' => 'Tutorías de Tesis', 'can' => 'revisar_tesis'],
        [
            'text' => 'Tutorías',
            'route'  => 'tutorias.index',
            'icon' => 'fas fa-fw fa-chalkboard-teacher',
            'can' => 'revisar_tesis'
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/chart.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
