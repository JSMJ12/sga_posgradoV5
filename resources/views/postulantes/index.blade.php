@extends('adminlte::page')

@section('title', 'Postulantes')

@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Postulantes</h1>
@stop
@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #446f5f;">
                <h3 class="card-title">Listado de Postulantes</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="postulantes">
                        <thead style="background-color: #315d50; color: white;">
                            <tr>
                                <th>Cedula</th>
                                <th>Nombre</th>
                                <th>Correo Electrónico</th>
                                <th>Celular</th>
                                <th>Título Profesional</th>
                                <th>Maestría</th>
                                <th>Mensaje</th>
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
                                        @php
                                            $usuario = \App\Models\User::where(
                                                'email',
                                                $postulante->correo_electronico,
                                            )->first();
                                        @endphp

                                        @if ($usuario)
                                            <button type="button" class="btn btn-outline-info btn-sm btn-message"
                                                data-id="{{ $usuario->id }}" data-nombre="{{ $usuario->name }}"
                                                data-toggle="modal" data-target="#sendMessageModal" title="Enviar mensaje">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">Usuario no encontrado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn btn-sm text-white"
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
                                                    <a href="{{ route('postulante.ficha_inscripcion_pdf', $postulante->dni) }}"
                                                        target="_blank" class="btn btn-outline-danger btn-sm" title="Ver Ficha de Admisión PDF">
                                                        <i class="fas fa-file-pdf"></i>
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
                                                @php
                                                    $documentos_requeridos = [
                                                        'Cédula',
                                                        'Papel de Votación',
                                                        'Título de Universidad',
                                                        'Hoja de Vida',
                                                        'Carta de Aceptación',
                                                    ];

                                                    $documentos_verificados = $postulante->documentos_verificados->where(
                                                        'verificado',
                                                        1,
                                                    );
                                                    $verificados = $documentos_verificados
                                                        ->pluck('tipo_documento')
                                                        ->toArray();

                                                    $apto =
                                                        count(array_intersect($documentos_requeridos, $verificados)) ===
                                                        count($documentos_requeridos);

                                                    // Buscar pago de matrícula verificado
                                                    $pago_matricula_verificado = \App\Models\Pago::whereHas(
                                                        'user',
                                                        function ($query) use ($postulante) {
                                                            $query->where('email', $postulante->correo_electronico);
                                                        },
                                                    )
                                                        ->where('verificado', true)
                                                        ->exists();
                                                @endphp

                                                @if (!$postulante->status && $apto)
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
                                                @elseif ($pago_matricula_verificado)
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
    @include('modales.enviar_mensaje_modal')
@stop

@section('js')
@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#postulantes').DataTable({
                lengthMenu: [5, 10, 15, 20, 40, 45, 50, 100],
                pageLength: 10, // Valor predeterminado
                responsive: true,
                colReorder: true,
                autoFill: true,
                keys: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });
        });
    </script>
    <script>
        $(document).on('click', '.btn-message', function() {
            var userId = $(this).data('id');
            var userName = $(this).data('nombre');

            $('#sendMessageModalLabel').text('Enviar mensaje a ' + userName);

            $('#receiver_id').val(userId);
        });
    </script>
@stop

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
