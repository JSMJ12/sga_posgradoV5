@extends('adminlte::page')
@section('title', 'Calificar')

@section('content')
@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Notas del Alumno</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
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
                                @foreach ($notas as $nota)
                                    <tr>
                                        <td>{{ $nota->nota_actividades }}</td>
                                        <td>{{ $nota->nota_practicas }}</td>
                                        <td>{{ $nota->nota_autonomo }}</td>
                                        <td>{{ $nota->examen_final }}</td>
                                        <td>{{ $nota->recuperacion }}</td>
                                        <td>{{ $nota->total }}</td>
                                        <td>
                                            @if ($tienePermisoVerNotas)
                                                @if ($fechaLimite >= now())
                                                    <a href="{{ route('calificaciones.edit1', [$nota->alumno_dni, $nota->docente_dni, $nota->asignatura_id, $nota->cohorte_id]) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                @else
                                                    <span class="text-danger">
                                                        <i class="fas fa-ban"></i> No se puede editar las notas después de la fecha límite.
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-danger">
                                                    <i class="fas fa-lock"></i> No tienes permiso para editar las notas.
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
