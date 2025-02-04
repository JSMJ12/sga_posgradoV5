@extends('adminlte::page')
@section('title', 'Cohortes')
@section('content_header')
    <h1><i class="fas fa-chalkboard"></i> Gestión de Cohortes</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #036f1b;">
                <h3 class="card-title">Listado de Cohortes</h3>
                <div class="card-tools">
                    <a href="{{ route('cohortes.create') }}" class="btn btn-light btn-sm"><i class="fas fa-plus"></i> Crear
                        Cohorte</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="cohortes">
                        <thead style="background-color: #003366; color: white;">
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-user"></i> Nombre</th>
                                <th><i class="fas fa-graduation-cap"></i> Maestría</th>
                                <th><i class="fas fa-calendar-alt"></i> Periodo Académico</th>
                                <th><i class="fas fa-door-open"></i> Aula</th>
                                <th><i class="fas fa-users"></i> Aforo</th>
                                <th><i class="fas fa-chalkboard-teacher"></i> Modalidad</th>
                                <th><i class="fas fa-calendar-day"></i> Inicio</th>
                                <th><i class="fas fa-calendar-check"></i> Fin</th>
                                <th class="text-center"><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>                            
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#cohortes').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('cohortes.index') }}', // URL para cargar los datos
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'nombre',
                        name: 'nombre'
                    },
                    {
                        data: 'maestria.nombre',
                        name: 'maestria.nombre'
                    },
                    {
                        data: 'periodo_academico.nombre',
                        name: 'periodo_academico.nombre'
                    },
                    {
                        data: 'aula_nombre',  // Aquí usamos la columna 'aula_nombre'
                        name: 'aula_nombre'
                    },
                    {
                        data: 'aforo',
                        name: 'aforo'
                    },
                    {
                        data: 'modalidad',
                        name: 'modalidad'
                    },
                    {
                        data: 'fecha_inicio',
                        name: 'fecha_inicio'
                    },
                    {
                        data: 'fecha_fin',
                        name: 'fecha_fin'
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                lengthMenu: [5, 10, 15, 20, 40, 45, 50, 100],
                pageLength: {{ $perPage }},
                responsive: true,
                colReorder: true,
                keys: true,
                autoFill: true
            });


        });

        
    </script>

@stop
