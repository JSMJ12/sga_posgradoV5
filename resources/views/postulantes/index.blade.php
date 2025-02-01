@extends('adminlte::page')

@section('title', 'Postulantes')

@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Postulantes</h1>
@stop
@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Postulantes</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="postulantes">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>Cedula</th>
                                <th>Nombre</th>
                                <th>Correo Electrónico</th>
                                <th>Celular</th>
                                <th>Título Profesional</th>
                                <th>Maestría</th>
                                <th>PDFs</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($postulantes as $postulante)
                                <tr>
                                    <td>{{ $postulante->dni }}</td>
                                    <td>{{ $postulante->apellidop }} <br> {{ $postulante->apellidom }} <br>
                                        {{ $postulante->nombre1 }} <br> {{ $postulante->nombre2 }}</td>
                                    <td>{{ $postulante->correo_electronico }}</td>
                                    <td>{{ $postulante->celular }}</td>
                                    <td>{{ $postulante->titulo_profesional }}</td>
                                    <td>{{ $postulante->maestria->nombre }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" 
                                                    class="btn btn-sm text-white" 
                                                    style="background-color: #064584; border-color: #032546;" 
                                                    data-toggle="modal" 
                                                    data-target="#verificarDocumentosModal_{{ $postulante->dni }}"
                                                    title="Verificar Documentos">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </div>                                        
                                        @include('modales.verificar_documento_modal')
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <ul class="list-group">
                                                <li class="list-group-item text-center">
                                                    <a href="{{ route('postulaciones.show', $postulante->dni) }}"
                                                        class="btn btn-outline-info btn-sm mb-1" title="Ver Detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </li>

                                                <li class="list-group-item text-center">
                                                    <form action="{{ route('postulaciones.destroy', $postulante->dni) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este postulante?')"
                                                            title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </li>

                                                @if (
                                                    !$postulante->status &&
                                                        $postulante->pdf_cedula &&
                                                        $postulante->pdf_papelvotacion &&
                                                        $postulante->pdf_titulouniversidad &&
                                                        $postulante->pdf_hojavida)
                                                    <li class="list-group-item text-center">
                                                        <form action="{{ route('postulantes.aceptar', $postulante->dni) }}"
                                                            method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" class="btn btn-outline-success btn-sm"
                                                                onclick="return confirm('¿Estás seguro de que deseas marcar a este postulante como Apto?')"
                                                                title="Marcar como Apto">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    </li>
                                                @elseif ($postulante->pago_matricula)
                                                    <li class="list-group-item text-center">
                                                        <form
                                                            action="{{ route('postulantes.convertir', $postulante->dni) }}"
                                                            method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" class="btn btn-outline-primary btn-sm"
                                                                onclick="return confirm('¿Estás seguro de que deseas convertir a este postulante en estudiante?')"
                                                                title="Convertir en Estudiante">
                                                                <i class="fas fa-user-graduate"></i>
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        $('#postulantes').DataTable({
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
@section('css')
    <style>
        .btn-outline-lila {
            color: #6f42c1;
            border-color: #6f42c1;
        }

        .btn-outline-lila:hover {
            color: #fff;
            background-color: #6f42c1;
            border-color: #6f42c1;
        }
    </style>
@stop
