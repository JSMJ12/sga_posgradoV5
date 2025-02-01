@extends('adminlte::page')

@section('title', 'Docentes')

@section('content_header')
    <h1><i class="fas fa-chalkboard"></i> Gestión de Docentes</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Docentes</h3>
                <div class="card-tools">
                    <a href="{{ route('docentes.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Agregar nuevo
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="docentes">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>Cédula / Pasaporte</th>
                                <th>Foto</th>
                                <th>Nombre completo</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('modales.asignaturas_docente_modal')
    @include('modales.cohortes_docente_modal')

@stop

@section('js')
    <script>
        $(document).ready(function() {
            const table = $('#docentes').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('docentes.index') }}",
                columns: [{
                        data: 'dni',
                        name: 'dni'
                    },
                    {
                        data: 'foto',
                        name: 'foto',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nombre_completo',
                        name: 'nombre_completo',
                        orderable: false
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'tipo',
                        name: 'tipo'
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                colReorder: true,
                keys: true,
                autoFill: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });


        });
    </script>
    <script>
        $(document).ready(function() {
            // Delegado de eventos para manejar botones dinámicos
            $(document).on('click', '.btn-modal', function() {
                const docenteDni = $(this).data('id'); // DNI del docente
                const modal = $('#asignaturasModal');
                const modalTitle = modal.find('#asignaturasModalLabel');
                const modalContent = modal.find('#modal-asignaturas-content');

                // Cambiar el título mientras carga
                modalTitle.text('Cargando asignaturas del docente...');

                // Mostrar spinner de carga
                modalContent.html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        `);

                // Hacer la solicitud AJAX
                $.ajax({
                    url: `/docentes/${docenteDni}/asignaturas`,
                    method: 'GET',
                    success: function(response) {
                        if (response.length === 0) {
                            modalContent.html(`
                        <div class="alert alert-info" role="alert">
                            No hay asignaturas asignadas a este docente.
                        </div>
                    `);
                        } else {
                            let tableRows = response.map(asignatura => `
                        <tr>
                            <td>${asignatura.nombre}</td>
                            <td>${asignatura.codigo_asignatura}</td>
                            <td>${asignatura.credito}</td>
                            
                            <td>
                                <form action="/docentes/${docenteDni}/asignaturas/${asignatura.id}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    `).join('');

                            modalContent.html(`
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Asignatura</th>
                                        <th>Código</th>
                                        <th>Créditos</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableRows}
                                </tbody>
                            </table>
                        </div>
                    `);
                        }

                        // Actualizar el título del modal
                        modalTitle.text('Asignaturas del Docente');
                    },
                    error: function() {
                        modalContent.html(`
                    <div class="alert alert-danger" role="alert">
                        Ocurrió un error al cargar las asignaturas.
                    </div>
                `);
                    }
                });

                // Mostrar el modal
                modal.modal('show');
            });
        });
    </script>
    
@stop
