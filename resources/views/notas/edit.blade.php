@extends('adminlte::page')
@section('title', 'Editar Nota')
@section('content_header')
    <h1>Editar Nota</h1>
@stop
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('notas.update', $nota->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group row">
                            <label for="alumno_id" class="col-md-4 col-form-label text-md-right">Alumno</label>
                            <div class="col-md-6">
                                <select id="alumno_dni" class="form-control @error('alumno_dni') is-invalid @enderror" name="alumno_id" required autofocus>
                                    <option value="">Seleccione un alumno</option>
                                    @foreach($alumnos as $alumno)
                                        <option value="{{ $alumno->dni }}" @if($nota->alumno_id == $alumno->dni) selected @endif>{{ $alumno->nombre1 }} {{ $alumno->nombre2 }} {{ $alumno->apellidop }} {{ $alumno->apellidom }}</option>
                                    @endforeach
                                </select>
                                @error('alumno_dni')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror                        
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="asignatura_id" class="col-md-4 col-form-label text-md-right">Asignatura</label>
                            <div class="col-md-6">
                                <select id="asignatura_id" class="form-control @error('asignatura_id') is-invalid @enderror" name="asignatura_id" required>
                                    <option value="">Seleccione una asignatura</option>
                                    @foreach($asignaturas as $asignatura)
                                        <option value="{{ $asignatura->id }}" @if($nota->asignatura_id == $asignatura->id) selected @endif>{{ $asignatura->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('asignatura_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="docencia_p1" class="col-md-4 col-form-label text-md-right">Docencia Primer Parcial</label>
                            <div class="col-md-6">
                                <input id="docencia_p1" type="number" step="0.01" class="form-control @error('docencia_p1') is-invalid @enderror" name="docencia_p1" value="{{ $nota->docencia_p1 }}" required>
                                @error('docencia_p1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="practicas_p1" class="col-md-4 col-form-label text-md-right">Prácticas Primer Parcial</label>
                            <div class="col-md-6">
                                <input id="practicas_p1" type="number" step="0.01" class="form-control @error('practicas_p1') is-invalid @enderror" name="practicas_p1" value="{{ $nota->practicas_p1 }}" required>
                                @error('practicas_p1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="trabajo_autonomo_p1" class="col-md-4 col-form-label text-md-right">Trabajo Autónomo Primer Parcial</label>
                            <div class="col-md-6">
                                <input id="trabajo_autonomo_p1" type="number" step="0.01" class="form-control @error('trabajo_autonomo_p1') is-invalid @enderror" name="trabajo_autonomo_p1"  value="{{ $nota->trabajo_autonomo_p1 }}" required>
                                @error('trabajo_autonomo_p1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="examen_p1" class="col-md-4 col-form-label text-md-right">Examen Primer Parcial</label>
                            <div class="col-md-6">
                                <input id="examen_p1" type="number" step="0.01" class="form-control @error('examen_p1') is-invalid @enderror" name="examen_p1" value="{{ $nota->examen_p1 }}" required>
                                @error('examen_p1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Campos para el segundo parcial -->
                        <div class="form-group row">
                            <label for="docencia_p2" class="col-md-4 col-form-label text-md-right">Docencia Segundo Parcial</label>
                            <div class="col-md-6">
                                <input id="docencia_p2" type="number" step="0.01" class="form-control @error('docencia_p2') is-invalid @enderror" name="docencia_p2" value="{{ $nota->docencia_p2 }}" required>
                                @error('docencia_p2')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="practicas_p2" class="col-md-4 col-form-label text-md-right">Prácticas Segundo Parcial</label>
                            <div class="col-md-6">
                                <input id="practicas_p2" type="number" step="0.01" class="form-control @error('practicas_p2') is-invalid @enderror" name="practicas_p2"  value="{{ $nota->practicas_p2 }}" required>
                                @error('practicas_p2')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="trabajo_autonomo_p2" class="col-md-4 col-form-label text-md-right">Trabajo Autónomo Segundo Parcial</label>
                            <div class="col-md-6">
                                <input id="trabajo_autonomo_p2" type="number" step="0.01" class="form-control @error('trabajo_autonomo_p2') is-invalid @enderror" name="trabajo_autonomo_p2" value="{{ $nota->trabajo_autonomo_p2 }}" required>
                                @error('trabajo_autonomo_p1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="examen_p2" class="col-md-4 col-form-label text-md-right">Examen Segundo Parcial</label>
                            <div class="col-md-6">
                                <input id="examen_p2" type="number" step="0.01" class="form-control @error('examen_p2') is-invalid @enderror" name="examen_p2" value="{{ $nota->examen_p2 }}"  required>
                                @error('examen_p1')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop