@extends('adminlte::page')

@section('title', 'Notas')

@section('content_header')
    <h1>Notas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="container-fluid">
                @if (count($notas) > 0)
                    <!-- Información del Alumno -->
                    <div class="card mb-3">
                        <div class="card-header text-white" style="background-color: #003366">Información del Alumno</div>
                        <div class="card-body">
                            <p><strong>Nombre:</strong> {{ $notas[0]->alumno->nombre1 }} {{ $notas[0]->alumno->nombre2 }} {{ $notas[0]->alumno->apellidop }} {{ $notas[0]->alumno->apellidom }}</p>
                            <p><strong>DNI:</strong> {{ $notas[0]->alumno->dni }}</p>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered" id="notas">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Asignatura</th>
                                    <th>Aula</th>
                                    <th>Paralelo</th>
                                    <th>Periodo</th>
                                    <th>Cohorte</th>
                                    <th>Docente</th>
                                    <th>Actividades de Aprendizaje (2.5)</th>
                                    <th>Componentes de Prácticas (2.5)</th>
                                    <th>Componente de Aprendizaje Autónomo (2.5)</th>
                                    <th>Examen Final (2.5)</th>
                                    <th>Recuperación</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notas as $nota)
                                    <tr>
                                        <td>{{ $nota->asignatura->nombre }}</td>
                                        <td>{{ $nota->cohorte->aula->nombre ?? 'Sin aula' }}</td>
                                        <td>{{ $nota->cohorte->aula->paralelo ?? 'Sin paralelo' }}</td>
                                        <td>{{ $nota->cohorte->periodo_academico->nombre }}</td>
                                        <td>{{ $nota->cohorte->nombre }}</td>
                                        <td>{{ $nota->docente->nombre1 }} {{ $nota->docente->nombre2 }} {{ $nota->docente->apellidop }} {{ $nota->docente->apellidom }}</td>
                                        <td>{{ number_format($nota->nota_actividades, 2) }}</td>
                                        <td>{{ number_format($nota->nota_practicas, 2) }}</td>
                                        <td>{{ number_format($nota->nota_autonomo, 2) }}</td>
                                        <td>{{ number_format($nota->examen_final, 2) }}</td>
                                        <td>{{ number_format($nota->recuperacion, 2) }}</td>
                                        <td class="font-weight-bold text-primary">{{ number_format($nota->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-danger font-weight-bold">No hay notas registradas</p>
                @endif
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Estilos personalizados para la tabla */
        .table {
            margin-bottom: 0;
            border-radius: 0.25rem;
        }

        .thead-dark th {
            background-color: #343a40;
            color: #ffffff;
        }

        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        .card {
            margin-top: 20px;
        }

        .text-center {
            margin-top: 20px;
        }
    </style>
@stop
