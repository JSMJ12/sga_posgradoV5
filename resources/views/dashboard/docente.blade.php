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
        @php
            // Agrupar asignaturas por maestría
            $asignaturasPorMaestria = $data->groupBy(function($asignatura) {
                return $asignatura['maestria_id'] ?? 'sin_maestria';
            });
        @endphp

        @foreach ($asignaturasPorMaestria as $maestriaId => $asignaturasGrupo)
            @php
                $maestriaNombre = $asignaturasGrupo->first()['maestria_nombre'] ?? 'Sin Maestría';
                $maestriaCodigo = $asignaturasGrupo->first()['maestria_codigo'] ?? 'N/A';
            @endphp
            <div class="card mb-5 border-primary shadow">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $maestriaNombre }}</h4>
                            <small>Código: <span class="badge badge-light">{{ $maestriaCodigo }}</span></small>
                        </div>
                        <i class="fas fa-university fa-2x"></i>
                    </div>
                </div>
                <div class="card-body">
                    @foreach ($asignaturasGrupo as $asignatura)
                        <div class="card mb-4 shadow-sm border-info">
                            <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
                                <strong>{{ $asignatura['nombre'] }}</strong>
                                @if ($asignatura['silabo'])
                                    <a href="{{ asset('storage/' . $asignatura['silabo']) }}" target="_blank"
                                        class="btn btn-outline-light btn-sm">Ver Sílabo</a>
                                @else
                                    <form action="{{ route('updateSilabo') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="asignatura_id" value="{{ $asignatura['id'] }}">
                                        <input type="file" name="silabo" class="form-control form-control-sm d-inline w-auto" style="display: inline-block;">
                                        <button type="submit" class="btn btn-success btn-sm">Subir Sílabo</button>
                                    </form>
                                @endif
                            </div>
                            <div class="card-body">
                                @foreach ($asignatura['cohortes'] as $cohorte)
                                    <div class="card mb-3 border-0">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center toggle-body"
                                            data-toggle="cohorte_{{ $loop->parent->parent->index }}_{{ $loop->parent->index }}_{{ $loop->index }}">
                                            <div>
                                                <strong>{{ $cohorte['nombre'] }}</strong>
                                                <span class="badge badge-secondary">Aula: {{ $cohorte['aula'] ?? 'N/A' }}</span>
                                                <span class="badge badge-info">Paralelo: {{ $cohorte['paralelo'] ?? 'N/A' }}</span>
                                                <span class="badge badge-warning">Fecha límite: {{ $cohorte['fechaLimite']->format('d-m-Y') }}</span>
                                            </div>
                                            <div>
                                                <a href="{{ $cohorte['excelUrl'] }}" class="btn btn-success btn-sm mr-2">
                                                    <i class="fas fa-file-excel"></i> Lista Alumnos
                                                </a>
                                                @if ($cohorte['calificarUrl'])
                                                    <a href="{{ $cohorte['calificarUrl'] }}" class="btn btn-primary btn-sm mr-2">Calificar</a>
                                                @endif
                                                @if ($cohorte['pdfNotasUrl'])
                                                    <a href="{{ $cohorte['pdfNotasUrl'] }}" target="_blank" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-file-pdf"></i> Notas PDF
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body p-0" id="cohorte_{{ $loop->parent->parent->index }}_{{ $loop->parent->index }}_{{ $loop->index }}" style="display: none;">
                                            <table class="table table-hover table-bordered">
                                                <thead class="thead-info">
                                                    <tr>
                                                        <th>Nombre Completo</th>
                                                        <th>Imagen</th>
                                                        <th>Actividades </th>
                                                        <th>Prácticas </th>
                                                        <th>Autónomo </th>
                                                        <th>Examen Final </th>
                                                        <th>Recuperación</th>
                                                        <th>Total (sin recuperación)</th> 
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $alumnosOrdenados = collect($cohorte['alumnos'])->sortBy('nombreCompleto')->values();
                                                    @endphp
                                                    @forelse ($alumnosOrdenados as $alumno)
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
                                                            <td>{{ $alumno['notas']['total'] }}</td> {{-- total calculado sin recuperación --}}
                                                            <td>
                                                                <a href="{{ $alumno['verNotasUrl'] }}" class="btn btn-info btn-sm">Ver Notas</a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="9" class="text-center">No hay alumnos en este cohorte.</td>
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
        font-size: 1.15rem;
    }
    .badge {
        font-size: 0.95rem;
        margin-left: 5px;
    }
    .card-header.bg-primary {
        border-bottom: 2px solid #0f982f;
    }
    .card-header.bg-info {
        border-bottom: 2px solid #115420;
    }
    .card {
        border-radius: 12px;
    }
    .table th, .table td {
        vertical-align: middle !important;
    }
    .img-thumbnail {
        border: 2px solid #252a61;
    }
</style>
@stop
