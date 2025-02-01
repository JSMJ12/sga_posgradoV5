@extends('adminlte::page')
@section('title', 'Docente Cohorte')
@section('content_header')
    <h1>Asignar Cohortes al Docente</h1>
@stop

@section('content')
    <div class="container mt-2">
        <!-- Información del Docente -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8 text-center">
                <div class="card shadow-sm">
                    <div class="card-body" style="background-color: green">
                        <img src="{{ asset('storage/' . $docente->image) }}" alt="Imagen de {{ $docente->nombre }}"
                            class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        <h4 style="color: white">{{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }}
                            {{ $docente->apellidom }}</h4>
                        <h5 class="text-muted">{{ $docente->tipo }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Cohortes -->
        @if (count($maestriaCohortes) > 0)
            <form action="{{ route('cohortes_docentes.store') }}" method="post">
                @csrf
                <input type="hidden" name="docente_dni" value="{{ $docente->dni }}">

                <div class="card shadow-sm">
                    <div class="card-header bg-white text-center">
                        <h5 class="mb-0 text-success">Cohortes Disponibles</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th>Maestría</th>
                                        <th>Asignatura</th>
                                        <th>Cohortes Disponibles</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($maestriaCohortes as $mc)
                                        @foreach ($mc['asignaturas'] as $key => $asignatura)
                                            <tr>
                                                @if ($key === 0)
                                                    <td rowspan="{{ count($mc['asignaturas']) }}">
                                                        {{ $mc['maestria']->nombre }}</td>
                                                @endif
                                                <td>{{ $asignatura->nombre }}</td>
                                                <td class="align-left">
                                                    @foreach ($mc['cohortes'] as $cohorte)
                                                    <div class="form-check" style="text-align: left;">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="asignatura_cohorte[{{ $asignatura->id }}][]"
                                                                value="{{ $cohorte->id }}"
                                                                id="cohorte{{ $cohorte->id }}"
                                                                {{ in_array($cohorte->id, $cohortesAsignados) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="cohorte{{ $cohorte->id }}">
                                                                {{ $cohorte->nombre }} {{ $cohorte->modalidad }}
                                                                @if ($cohorte->aula)
                                                                    - {{ $cohorte->aula->nombre }}
                                                                    @if ($cohorte->aula->paralelo)
                                                                        ({{ $cohorte->aula->paralelo }})
                                                                    @endif
                                                                @endif
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group text-center mt-3">
                            <button type="submit" class="btn btn-danger">Inscribir</button>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <div class="alert alert-warning text-center">
                No hay cohortes disponibles para asignar en este momento.
            </div>
        @endif
    </div>
@stop
@section('css')
    <style>
        .align-left {
            text-align: left;
        }
    </style>
@stop
