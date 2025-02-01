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
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="notas">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Asignatura</th>
                                    <th>Aula</th>
                                    <th>Paralelo</th>
                                    <th>Periodo</th>
                                    <th>Cohorte</th>
                                    <th>Docente</th>
                                    <th>Alumno</th>
                                    <th>DNI</th>
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
                                        <td>
                                            {{ $nota->docente->nombre1 }}<br>
                                            {{ $nota->docente->nombre2 }}<br>
                                            {{ $nota->docente->apellidop }}<br>
                                            {{ $nota->docente->apellidom }}
                                        </td>
                                        <td>
                                            {{ $nota->alumno->nombre1 }}<br>
                                            {{ $nota->alumno->nombre2 }}<br>
                                            {{ $nota->alumno->apellidop }}<br>
                                            {{ $nota->alumno->apellidom }}
                                        </td>
                                        <td>{{ $nota->alumno->dni }}</td>
                                        <td>{{ number_format($nota->nota_actividades, 2) }}</td>
                                        <td>{{ number_format($nota->nota_practicas, 2) }}</td>
                                        <td>{{ number_format($nota->nota_autonomo, 2) }}</td>
                                        <td>{{ number_format($nota->examen_final, 2) }}</td>
                                        <td>{{ number_format($nota->recuperacion, 2) }}</td>
                                        <td>{{ number_format($nota->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center">No hay notas registradas</p>
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
