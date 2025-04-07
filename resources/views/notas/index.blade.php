@extends('adminlte::page')

@section('title', 'Notas')

@section('content_header')
    <h1 class="mb-3">Listado de Notas</h1>
@stop

@section('content')
    <div class="card shadow">
        <div class="card-body">
            <div class="container-fluid">
                @if ($notas->count())
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover text-sm" id="notas">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>Asignatura</th>
                                    <th>Aula</th>
                                    <th>Paralelo</th>
                                    <th>Periodo</th>
                                    <th>Cohorte</th>
                                    <th>Docente</th>
                                    <th>Alumno</th>
                                    <th>DNI</th>
                                    <th>Actividades</th>
                                    <th>Prácticas</th>
                                    <th>Autónomo</th>
                                    <th>Examen</th>
                                    <th>Recuperación</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notas as $nota)
                                    <tr class="text-center">
                                        <td>{{ $nota->asignatura->nombre }}</td>
                                        <td>{{ $nota->cohorte->aula->nombre ?? 'Sin aula' }}</td>
                                        <td>{{ $nota->cohorte->aula->paralelo ?? 'Sin paralelo' }}</td>
                                        <td>{{ $nota->cohorte->periodo_academico->nombre }}</td>
                                        <td>{{ $nota->cohorte->nombre }}</td>
                                        <td>{{ $nota->docente->nombre1 }} {{ $nota->docente->nombre2 }} {{ $nota->docente->apellidop }} {{ $nota->docente->apellidom }}</td>
                                        <td>{{ $nota->alumno->nombre1 }} {{ $nota->alumno->nombre2 }} {{ $nota->alumno->apellidop }} {{ $nota->alumno->apellidom }}</td>
                                        <td>{{ $nota->alumno->dni }}</td>
                                        <td>{{ number_format($nota->nota_actividades, 2) }}</td>
                                        <td>{{ number_format($nota->nota_practicas, 2) }}</td>
                                        <td>{{ number_format($nota->nota_autonomo, 2) }}</td>
                                        <td>{{ number_format($nota->examen_final, 2) }}</td>
                                        <td>{{ number_format($nota->recuperacion, 2) }}</td>
                                        <td><strong>{{ number_format($nota->total, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">No hay notas registradas.</p>
                @endif
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card {
            margin-top: 20px;
        }

        th, td {
            vertical-align: middle !important;
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
        }

        .thead-dark th {
            background-color: #343a40;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
@stop
