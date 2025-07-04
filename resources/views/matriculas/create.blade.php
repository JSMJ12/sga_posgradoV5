@extends('adminlte::page')

@section('title', 'Matriculación')

@section('content_header')
    <h1><i class="fas fa-file-alt"></i> Matriculación</h1>
@stop

@section('content')
    <div class="card shadow">
        <div class="card-body">
            @if ($cohortes->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No hay cohortes activos disponibles para matriculación.
                </div>
            @else
                @foreach ($cohortes as $cohorte)
                    <div class="card mb-4">
                        <div class="card-header text-white" style="background-color: #3007b8;">
                            <h5 class="mb-0">
                                {{ $cohorte->maestria->nombre }} - {{ $cohorte->nombre }}
                                @if ($cohorte->aula && $cohorte->aula->paralelo)
                                    - Paralelo: {{ $cohorte->aula->paralelo }}
                                @else
                                    - Periodo Académico: {{ $cohorte->periodo_academico->nombre }} ({{ $cohorte->periodo_academico->status }})
                                @endif
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Asignatura</th>
                                            <th>Docente(s)</th>
                                            <th>Aforo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cohorte->maestria->asignaturas as $asignatura)
                                            @php
                                                $docentes = \App\Models\CohorteDocente::where('cohort_id', $cohorte->id)
                                                    ->where('asignatura_id', $asignatura->id)
                                                    ->with('docente')
                                                    ->get();
                                            @endphp
                                            <tr>
                                                <td>{{ $asignatura->nombre }}</td>
                                                <td>
                                                    @if ($docentes->isEmpty())
                                                        <span class="text-danger"><i class="fas fa-user-times"></i> Sin asignar</span>
                                                    @else
                                                        @foreach ($docentes as $registro)
                                                            {{ $registro->docente->nombre1 }} {{ $registro->docente->nombre2 }} {{ $registro->docente->apellidop }} {{ $registro->docente->apellidom }}<br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td>{{ $cohorte->aforo }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <form method="POST" action="{{ route('matriculas.store') }}">
                                @csrf
                                <input type="hidden" name="alumno_dni" value="{{ $alumno->dni }}">
                                <input type="hidden" name="cohorte_id" value="{{ $cohorte->id }}">

                                @foreach ($cohorte->maestria->asignaturas as $asignatura)
                                    @php
                                        $docenteRegistro = \App\Models\CohorteDocente::where('cohort_id', $cohorte->id)
                                            ->where('asignatura_id', $asignatura->id)
                                            ->first();
                                    @endphp

                                    <input type="hidden" name="asignatura_ids[]" value="{{ $asignatura->id }}">

                                    @if ($docenteRegistro)
                                        <input type="hidden" name="docente_dnis[{{ $asignatura->id }}]" value="{{ $docenteRegistro->docente_dni }}">
                                    @endif
                                @endforeach

                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-graduation-cap"></i> Matricular
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@stop
