@extends('adminlte::page')
@section('title', 'Periodos Academicos')
@section('content_header')
    <h1><i class="fas fa-chalkboard"></i> Gestión de Periodos Académicos</h1>
@stop
@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Periodos Académicos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#createPeriodoModal">
                        <i class="fas fa-plus"></i> Crear Periodo Academico
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="periodos_academicos">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($periodos_academicos as $periodo_academico)
                                @include('modales.editar_periodo_modal')
                                <tr>
                                    <td>{{ $periodo_academico->id }}</td>
                                    <td>{{ $periodo_academico->nombre }}</td>
                                    <td>{{ $periodo_academico->isVigente() ? 'Vigente' : 'No vigente' }}</td>
                                    <td>{{ $periodo_academico->fecha_inicio }}</td>
                                    <td>{{ $periodo_academico->fecha_fin }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary" data-toggle="modal"
                                            data-target="#editPeriodoModal{{ $periodo_academico->id }}" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form id="delete-form-{{ $periodo_academico->id }}" action="{{ route('periodos_academicos.destroy', $periodo_academico->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $periodo_academico->id }}" type="button" title="Eliminar">
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
        </div>
    </div>

    <!-- Modal de creación -->
    @include('modales.crear_periodo_modal')
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#periodos_academicos').DataTable({
                lengthMenu: [5, 10, 15, 20, 40, 45, 50, 100],
                pageLength: {{ $perPage }},
                responsive: true,
                colReorder: true,
                keys: true,
                autoFill: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });

            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var id = $(this).data('id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop
