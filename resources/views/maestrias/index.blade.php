@extends('adminlte::page')
@section('title', 'Maestrias')
@section('content_header')
    <h1><i class="fas fa-chalkboard"></i> Gestión de Maestrias</h1>
@stop
@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Maestrias</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#createMaestriaModal">
                        <i class="fas fa-plus"></i> Nueva Maestria
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    @if ($maestrias->isEmpty())
                        <div class="alert alert-warning text-center">
                            No hay maestrías registradas. Por favor, crea una nueva maestría haciendo clic en el botón de
                            arriba.
                        </div>
                    @else
                        <table class="table table-hover table-bordered table-striped" id="maestrias">
                            <thead style="background-color: #28a745; color: white;">
                                <tr>
                                    <th>ID</th>
                                    <th>Codigo</th>
                                    <th>Nombre</th>
                                    <th>Coordinador</th>
                                    <th>Asignaturas</th>
                                    <th>Precios</th>
                                    <th></th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($maestrias as $maestria)
                                    @include('modales.editar_maestria_modal')
                                    @include('modales.anadir_asignatura_modal')
                                    <tr>
                                        <td>{{ $maestria->id }}</td>
                                        <td>{{ $maestria->codigo }}</td>
                                        <td>{{ $maestria->nombre }}</td>
                                        <td>
                                            @foreach ($docentes as $docente)
                                                @if ($docente->dni === $maestria->coordinador)
                                                    {{ $docente->nombre1 }} {{ $docente->nombre2 }}
                                                    {{ $docente->apellidop }} {{ $docente->apellidom }}
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <ul>
                                                @if ($maestria->asignaturas->count() > 0)
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                        data-target="#asignaturasModal{{ $maestria->id }}"
                                                        title="Ver Asignaturas">
                                                        <i class="fas fa-book"></i>
                                                    </button>
                                                    @include('modales.mostrar_asignaturas_modal')
                                                @else
                                                    <li>No hay asignaturas</li>
                                                @endif
                                            </ul>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>Matricula: </strong> ${{ $maestria->matricula }}<br>
                                                <strong>Arancel:</strong> ${{ $maestria->arancel }}<br>
                                                <strong>Inscripción:</strong> ${{ $maestria->inscripcion }}
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#addAsignaturaModal{{ $maestria->id }}"
                                                title="Agregar Asignatura">
                                                <i class="fas fa-plus-circle"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 10px;">
                                                <a href="#" class="btn btn-sm btn-primary" data-toggle="modal"
                                                    data-target="#editMaestriaModal{{ $maestria->id }}" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @if ($maestria->status == 'ACTIVO')
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-disable-maestria"
                                                        data-id="{{ $maestria->id }}" title="Deshabilitar">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                    <form id="disable-form-{{ $maestria->id }}"
                                                        action="{{ route('maestrias.disable', $maestria->id) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf
                                                        @method('PUT')
                                                    </form>
                                                @else
                                                    <form action="{{ route('maestrias.enable', $maestria->id) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            title="Reactivar">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('modales.crear_maestria_modal')
@stop
@section('js')

    <script>
        $(document).ready(function() {
            $('#maestrias').DataTable({
                lengthMenu: [5, 10, 15, 20, 40, 45, 50, 100],
                pageLength: {{ $perPage }},
                responsive: true,
                colReorder: true,
                keys: true,
                autoFill: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                columnDefs: [{
                        targets: -1,
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: -2,
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.btn-disable-maestria').click(function(e) {
                e.preventDefault();
                var maestriaId = $(this).data('id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, deshabilitar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Envía el formulario
                        document.getElementById('disable-form-' + maestriaId).submit();
                    }
                });
            });
        });
    </script>
@stop
