@extends('adminlte::page')

@section('title', 'Dashboard Docente')

@section('content_header')
    <h1>Dashboard Docente</h1>
@stop

@section('content')
    <div class="container">
        @if ($data->isEmpty())
            <div class="alert alert-warning text-center">
                No hay asignaturas registradas.
            </div>
        @else
            @foreach ($data as $asignatura)
                <div class="card mb-4 shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>{{ $asignatura['nombre'] }}</strong>
                        @if ($asignatura['silabo'])
                            <a href="{{ asset('storage/' . $asignatura['silabo']) }}" target="_blank"
                                class="btn btn-danger btn-sm">Ver Sílabo</a>
                        @else
                            <form action="{{ route('updateSilabo') }}" method="POST" enctype="multipart/form-data"
                                class="d-inline">
                                @csrf
                                <input type="hidden" name="asignatura_id" value="{{ $asignatura['id'] }}">
                                <input type="file" name="silabo" class="form-control form-control-sm d-inline w-auto"
                                    style="display: inline-block;">
                                <button type="submit" class="btn btn-success btn-sm">Subir Sílabo</button>
                            </form>
                        @endif
                    </div>
                    <div class="card-body">
                        @foreach ($asignatura['cohortes'] as $cohorte)
                            <div class="card mb-3 border-0">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center toggle-body"
                                    data-toggle="cohorte_{{ $loop->parent->index }}_{{ $loop->index }}">
                                    <div>
                                        <strong>{{ $cohorte['nombre'] }}</strong>
                                        <span class="badge badge-secondary">Aula: {{ $cohorte['aula'] ?? 'N/A' }}</span>
                                        <span class="badge badge-info">Paralelo: {{ $cohorte['paralelo'] ?? 'N/A' }}</span>
                                        <span class="badge badge-warning">Fecha límite:
                                            {{ $cohorte['fechaLimite']->format('d-m-Y') }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ $cohorte['excelUrl'] }}" class="btn btn-success btn-sm mr-2">
                                            <i class="fas fa-file-excel"></i> Lista Alumnos
                                        </a>
                                        @if ($cohorte['calificarUrl'])
                                            <a href="{{ $cohorte['calificarUrl'] }}"
                                                class="btn btn-primary btn-sm mr-2">Calificar</a>
                                        @endif
                                        @if ($cohorte['pdfNotasUrl'])
                                            <a href="{{ $cohorte['pdfNotasUrl'] }}" target="_blank"
                                                class="btn btn-danger btn-sm">
                                                <i class="fas fa-file-pdf"></i> Notas PDF
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body p-0" id="cohorte_{{ $loop->parent->index }}_{{ $loop->index }}"
                                    style="display: none;">
                                    <table class="table table-hover">
                                        <thead class="thead-info">
                                            <tr>
                                                <th>Nombre Completo</th>
                                                <th>Imagen</th>
                                                <th>Actividades</th>
                                                <th>Prácticas</th>
                                                <th>Autónomo</th>
                                                <th>Examen Final</th>
                                                <th>Recuperación</th>
                                                <th>Total</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($cohorte['alumnos'] as $alumno)
                                                <tr>
                                                    <td>{{ $alumno['nombreCompleto'] }}</td>
                                                    <td>
                                                        <img src="{{ asset('storage/imagenes_usuarios/' . basename(parse_url($alumno['imagen'], PHP_URL_PATH))) }}" 
                                                             alt="Imagen" class="img-thumbnail rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                                                    </td>                                                    
                                                    <td>{{ $alumno['notas']['nota_actividades'] }}</td>
                                                    <td>{{ $alumno['notas']['nota_practicas'] }}</td>
                                                    <td>{{ $alumno['notas']['nota_autonomo'] }}</td>
                                                    <td>{{ $alumno['notas']['examen_final'] }}</td>
                                                    <td>{{ $alumno['notas']['recuperacion'] }}</td>
                                                    <td>{{ $alumno['notas']['total'] }}</td>
                                                    <td>
                                                        <a href="{{ $alumno['verNotasUrl'] }}"
                                                            class="btn btn-info btn-sm">Ver
                                                            Notas</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No hay alumnos en este cohorte.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.toggle-body').click(function() {
                var target = $(this).data('toggle');
                $('#' + target).slideToggle();
            });
        });
    </script>
@stop

@section('css')
    <style>
        .card-header strong {
            font-size: 1.25rem;
        }

        .badge {
            font-size: 0.9rem;
            margin-left: 5px;
        }
    </style>
@stop
