@extends('adminlte::page')

@section('title', 'Aulas')

@section('content_header')
    <h1><i class="fas fa-chalkboard"></i> Gestión de Aulas</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Aulas</h3>
                <div class="card-tools">
                    <button id="crearAulaBtn" class="btn btn-light btn-sm" data-bs-toggle="modal"
                        data-bs-target="#crearAulaModal">
                        <i class="fas fa-plus"></i> Crear Aula
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="aulas">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Piso</th>
                                <th>Código</th>
                                <th>Paralelo</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($aulas as $aula)
                                @include('modales.editar_aula_modal')
                                <tr>
                                    <td>{{ $aula->id }}</td>
                                    <td>{{ $aula->nombre }}</td>
                                    <td>{{ $aula->piso }}</td>
                                    <td>{{ $aula->codigo }}</td>
                                    <td>{{ $aula->paralelo }}</td>
                                    <td class="text-center">
                                        <!-- Botón Editar -->
                                        <button class="btn btn-outline-primary btn-sm btn-edit-aula"
                                            data-aula-id="{{ $aula->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Formulario Eliminar -->
                                        <form action="{{ route('aulas.destroy', $aula->id) }}" method="POST"
                                            style="display: inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar"
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar esta aula?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <small class="text-muted">Total de Aulas: {{ $aulas->count() }}</small>
            </div>
        </div>
    </div>

    @include('modales.crear_aula_modal')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Inicialización de DataTable
            $('#aulas').DataTable({
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
                    targets: 5,
                    orderable: false
                }]
            });

            // Mostrar modal de creación
            $('#crearAulaBtn').on('click', function() {
                $('#crearAulaModal').modal('show');
            });

            $('.btn-edit-aula').on('click', function() {
                var aulaId = $(this).data('aula-id'); // Obtiene el ID del aula
                $('#editarAulaModal' + aulaId).modal('show'); // Abre el modal correspondiente
            });

        });
    </script>
@stop
