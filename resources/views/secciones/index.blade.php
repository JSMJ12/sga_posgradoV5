@extends('adminlte::page')

@section('title', 'Secciones')

@section('content_header')
    <h1><i class="fas fa-chalkboard"></i> Gestión de Secciones</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Secciones</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#createModal">
                        <i class="fas fa-plus"></i> Crear nueva
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="secciones">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Maestrías</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($secciones as $seccion)
                                <tr>
                                    <td>{{ $seccion->id }}</td>
                                    <td>{{ $seccion->nombre }}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal"
                                            data-target="#maestriasModal{{ $seccion->id }}">
                                            Ver Maestrías
                                        </button>
                                        
                                        @include('modales.maestrias_seccion_modal')

                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-primary btn-sm mr-2"
                                                data-toggle="modal" data-target="#editModal{{ $seccion->id }}"
                                                title="Editar Sección">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('secciones.destroy', $seccion) }}" method="POST"
                                                onsubmit="return confirm('¿Está seguro de que desea eliminar esta sección?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    title="Eliminar Sección">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                @include('modales.edit_seccion_modal')
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('modales.create_seccion_modal')
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <style>
        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(4, 2fr);
            grid-gap: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JS de DataTables -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $('#secciones').DataTable({
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
    </script>
@stop
