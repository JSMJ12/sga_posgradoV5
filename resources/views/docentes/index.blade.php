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
                                <th>Asignaturas</th>
                                <th>Cohortes</th>
                                <th>Editar</th>
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
                        data: 'asignaturas',
                        name: 'asignaturas',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'cohortes',
                        name: 'cohortes',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'editar',
                        name: 'editar',
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: true,
                colReorder: true,
                keys: true,
                autoFill: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });
            setInterval(function() {
                table.ajax.reload(null, false);
            }, 50000);

        });
    </script>

@stop
