@extends('adminlte::page')

@section('title', 'Dashboard Coordinador')

@section('content_header')
    <h1>Dashboard - Coordinador</h1>
@stop

@section('content')
    <div class="container">
        <h3 class="text-center text-info my-4">{{ $maestria->nombre ?? 'Maestría no disponible' }}</h3>
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalDocentes }}</h3>
                        <p>Docentes</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-chalkboard-teacher"></i>
                    </div>
                    <a href="{{ route('docentes.index') }}" class="small-box-footer">
                        <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $totalAlumnos }}</h3>
                        <p>Alumnos</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-user-graduate"></i>
                    </div>
                    <a href="{{ route('alumnos.index') }}" class="small-box-footer">
                        <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $totalPostulantes }}</h3>
                        <p>Postulantes</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-user-tie"></i>
                    </div>
                    <a href="{{ route('postulaciones.index') }}" class="small-box-footer">
                        <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabla de Pagos por Cohorte -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-lg rounded-lg">
                    <div class="card-header bg-info text-white">
                        <h5>Resumen Financiero por Cohorte</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Cohorte</th>
                                    <th>Deuda Arancel</th>
                                    <th>Recaudado Arancel</th>
                                    <th>Deuda Matrícula</th>
                                    <th>Recaudado Matrícula</th>
                                    <th>Deuda Inscripción</th>
                                    <th>Recaudado Inscripción</th>
                                    <th>% General Recaudado</th>
                                    <th>PDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cohortes as $index => $cohorte)
                                    @php
                                        $deudaArancel = $deudaArancelPorCohorte[$cohorte] ?? 0;
                                        $deudaMatricula = $deudaMatriculaPorCohorte[$cohorte] ?? 0;
                                        $deudaInscripcion = $deudaInscripcionPorCohorte[$cohorte] ?? 0;

                                        $recaudadoArancel = $recaudadoArancelPorCohorte[$cohorte] ?? 0;
                                        $recaudadoMatricula = $recaudadoMatriculaPorCohorte[$cohorte] ?? 0;
                                        $recaudadoInscripcion = $recaudadoInscripcionPorCohorte[$cohorte] ?? 0;

                                        $totalDeuda = $deudaArancel + $deudaMatricula + $deudaInscripcion;
                                        $totalRecaudado =
                                            $recaudadoArancel + $recaudadoMatricula + $recaudadoInscripcion;
                                        $porcentaje = $totalDeuda > 0 ? ($totalRecaudado / $totalDeuda) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $cohorte }}</td>
                                        <td>${{ number_format($deudaArancel, 2) }}</td>
                                        <td>${{ number_format($recaudadoArancel, 2) }}</td>
                                        <td>${{ number_format($deudaMatricula, 2) }}</td>
                                        <td>${{ number_format($recaudadoMatricula, 2) }}</td>
                                        <td>${{ number_format($deudaInscripcion, 2) }}</td>
                                        <td>${{ number_format($recaudadoInscripcion, 2) }}</td>
                                        <td>{{ number_format($porcentaje, 2) }}%</td>
                                        <td>
                                            <a href="{{ route('pagos.pdf', ['cohorte' => $cohorte]) }}"
                                                class="btn btn-danger btn-sm" target="_blank" title="Descargar PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
            <!-- Gráfico Comparativo Arancel -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-lg rounded-lg">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Arancel: Deuda vs Recaudado</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="arancelChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico Comparativo Matrícula -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-lg rounded-lg">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Matrícula: Deuda vs Recaudado</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="matriculaChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico Comparativo Inscripción -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-lg rounded-lg">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">Inscripción: Deuda vs Recaudado</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="inscripcionChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop

@section('css')
    <style>
        .card {
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            text-align: center;
        }

        .card-body i {
            margin-bottom: 10px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const cohortes = @json(array_values($cohortes));
        const deudaArancel = @json(array_values($deudaArancelPorCohorte));
        const recaudadoArancel = @json(array_values($recaudadoArancelPorCohorte));
        const deudaMatricula = @json(array_values($deudaMatriculaPorCohorte));
        const recaudadoMatricula = @json(array_values($recaudadoMatriculaPorCohorte));
        const deudaInscripcion = @json(array_values($deudaInscripcionPorCohorte));
        const recaudadoInscripcion = @json(array_values($recaudadoInscripcionPorCohorte));

        const createBarChart = (canvasId, labelDeuda, labelRecaudado, dataDeuda, dataRecaudado) => {
            const ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: cohortes,
                    datasets: [{
                            label: labelDeuda,
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            data: dataDeuda
                        },
                        {
                            label: labelRecaudado,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            data: dataRecaudado
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        createBarChart('arancelChart', 'Deuda Arancel ($)', 'Recaudado Arancel ($)', deudaArancel, recaudadoArancel);
        createBarChart('matriculaChart', 'Deuda Matrícula ($)', 'Recaudado Matrícula ($)', deudaMatricula,
            recaudadoMatricula);
        createBarChart('inscripcionChart', 'Deuda Inscripción ($)', 'Recaudado Inscripción ($)', deudaInscripcion,
            recaudadoInscripcion);
    </script>
@stop
