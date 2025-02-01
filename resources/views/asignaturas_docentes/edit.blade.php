@extends('adminlte::page')
@section('title', 'Asignatura-Docentes')
@section('content_header')
    <h1>Editar asignaciones de {{ $docente->nombre1 }} {{ $docente->apellidop }} {{ $docente->apellidom }}</h1>
@stop
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar asignación</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('asignaturas_docentes.update', $asignacion->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="docente_id">Docente:</label>
                            <select class="form-control" id="docente_id" name="docente_id">
                                @foreach ($docentes as $docente)
                                    <option value="{{ $docente->id }}" {{ $asignacion->docente_id == $docente->id ? 'selected' : '' }}>{{ $docente->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Asignaturas:</label><br>
                            @foreach ($asignaturas as $asignatura)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="asignatura_{{ $asignatura->id }}" name="asignaturas[]" value="{{ $asignatura->id }}" {{ in_array($asignatura->id, $asignadas) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="asignatura_{{ $asignatura->id }}">{{ $asignatura->nombre }}</label>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@stop