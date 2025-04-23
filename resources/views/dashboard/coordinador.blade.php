@extends('adminlte::page')
@section('title', 'Dashboard Coordinador')

@section('content_header')
    <h1>Dashboard - Coordinador</h1>
@stop

@section('content')
    <div class="container">
        <h3 class="text-center text-info my-4">{{ $maestria->nombre ?? 'Maestría no disponible' }}</h3>

        <!-- Estadísticas Generales -->
        <div class="row mb-4">
            <!-- Alumnos -->
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="card shadow border-0 h-100">
                    <div class="card-body text-center bg-gradient-primary text-white rounded">
                        <div class="mb-2">
                            <i class="fa fa-users fa-3x"></i>
                        </div>
                        <h3>{{ $totalAlumnos ?? 0 }}</h3>
                        <p class="mb-0">Alumnos</p>
                    </div>
                </div>
            </div>

            <!-- Docentes -->
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="card shadow border-0 h-100">
                    <div class="card-body text-center bg-gradient-success text-white rounded">
                        <div class="mb-2">
                            <i class="fa fa-chalkboard-teacher fa-3x"></i>
                        </div>
                        <h3>{{ $totalDocentes ?? 0 }}</h3>
                        <p class="mb-0">Docentes</p>
                    </div>
                </div>
            </div>

            <!-- Postulantes -->
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="card shadow border-0 h-100">
                    <div class="card-body text-center bg-gradient-warning text-white rounded">
                        <div class="mb-2">
                            <i class="fa fa-user-graduate fa-3x"></i>
                        </div>
                        <h3>{{ $totalPostulantes ?? 0 }}</h3>
                        <p class="mb-0">Postulantes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
            <!-- Gráfico de Matriculados -->
            <div class="col-md-6">
                <div class="card shadow-lg rounded-lg">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Gráfico de Matriculados</h5>
                    </div>
                    <div class="card-body">
                        @if (!empty($graficoMatriculadosData))
                            <canvas id="matriculadosChart" width="400" height="200"></canvas>
                        @else
                            <p class="text-center text-muted">No hay datos disponibles para mostrar el gráfico.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Resumen de Pagos por Cohorte -->
            <div class="col-md-6">
                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5>Resumen de Pagos por Cohorte</h5>
                    </div>
                    <div class="card-body">
                        @if (!empty($graficoCohorteData))
                            <canvas id="cohorteChart"></canvas>
                        @else
                            <p class="text-center text-muted">No hay pagos registrados aún.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Pagos por Cohorte -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5>Resumen de Pagos por Cohorte</h5>
                    </div>
                    <div class="card-body">
                        @if (count($cohortes) > 0)
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Cohorte</th>
                                        <th>Total Monto</th>
                                        <th>Cantidad de Pagos</th>
                                        <th>PDF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cohortes as $index => $cohorte)
                                        <tr>
                                            <td>{{ $cohorte }}</td>
                                            <td>${{ number_format($monto[$index] ?? 0, 2) }}</td>
                                            <td>{{ $cantidad[$index] ?? 0 }}</td>
                                            <td>
                                                <a href="{{ route('pagos.pdf', ['cohorte' => $cohorte]) }}"
                                                    class="btn btn-danger btn-sm" target="_blank">
                                                    Descargar PDF
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-center text-muted">No hay cohortes con pagos registrados aún.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Asignaturas -->
        <div class="row">
            <!-- Deuda pendiente -->
            <div class="col-md-6">
                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5>Deuda Pendiente por Cohorte</h5>
                    </div>
                    <div class="card-body">
                        @if (!empty($graficoDeudaData))
                            <canvas id="deudaPendienteChart"></canvas>
                        @else
                            <p class="text-center text-muted">No hay datos de deuda pendientes.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lista de asignaturas -->
            <div class="col-md-6">
                <div class="card shadow-lg rounded-lg">
                    <div class="card-header bg-info text-white" id="asignaturasHeader" style="cursor: pointer;">
                        <h5 class="mb-0">Asignaturas</h5>
                    </div>
                    <div class="card-body" id="asignaturasList" style="display: none; max-height: 300px; overflow-y: auto;">
                        @if ($asignaturas->isNotEmpty())
                            <ul class="list-group list-group-flush">
                                @foreach ($asignaturas as $asignatura)
                                    <li class="list-group-item">
                                        {{ $asignatura->nombre }}
                                        <br>
                                        <span style="font-weight: bold;">Docente:</span>
                                        {{ $asignatura->docentes->first()->nombre1 ?? 'Docente' }}
                                        {{ $asignatura->docentes->first()->nombre2 ?? 'no' }}
                                        {{ $asignatura->docentes->first()->apellidop ?? 'asignado' }}
                                        {{ $asignatura->docentes->first()->apellidom ?? '' }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-center text-muted">No hay asignaturas registradas aún.</p>
                        @endif
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
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card-body h3 {
            font-size: 2rem;
            margin: 10px 0;
        }

        .card-body p {
            font-size: 1.2rem;
            margin: 0;
        }

        .card-body i {
            margin-bottom: 15px;
        }

        .card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
    </style>
@stop

@section('js')
    <script>
        var ctx = document.getElementById('deudaPendienteChart').getContext('2d');
        var deudaPendienteChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($cohortes), // Los nombres de las cohortes
                datasets: [{
                    label: 'Deuda Pendiente (USD)',
                    data: @json($monto), // Los montos de deuda
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <script>
        var ctx = document.getElementById('cohorteChart').getContext('2d');
        var cohorteChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($cohortes) !!},
                datasets: [{
                        label: 'Monto Total',
                        data: {!! json_encode($monto) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Cantidad de Pagos',
                        data: {!! json_encode($cantidad) !!},
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>


    <script>
        // Datos del gráfico
        var matriculadosIds = {!! $matriculadosPorMaestria->pluck('nombre') !!}; // IDs de las maestrías
        var matriculadosValues = {!! $matriculadosPorMaestria->pluck('alumnos_count') !!}; // Cantidad de alumnos matriculados

        var matriculadosData = {
            labels: matriculadosIds, // Usamos los IDs de las maestrías como etiquetas
            datasets: [{
                label: 'Cantidad de Alumnos Matriculados',
                data: matriculadosValues,
                backgroundColor: matriculadosValues.map(() => {
                    // Generamos colores aleatorios para cada barra
                    return `rgba(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, 0.8)`;
                }),
                borderColor: matriculadosValues.map(() => 'rgba(0, 0, 0, 0.2)'), // Borde suave en negro
                borderWidth: 1, // Grosor del borde
                hoverBackgroundColor: matriculadosValues.map(() => {
                    // Fondo más oscuro al pasar el mouse
                    return `rgba(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, 0.9)`;
                }),
                hoverBorderColor: 'rgba(255, 255, 255, 1)' // Borde blanco al pasar el mouse
            }]
        };

        var matriculadosOptions = {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(200, 200, 200, 0.3)', // Líneas suaves y modernas
                        borderDash: [5, 5], // Líneas punteadas
                    },
                    ticks: {
                        color: 'black', // Color de las etiquetas del eje Y
                    }
                },
                x: {
                    ticks: {
                        color: 'black', // Color de las etiquetas del eje X (IDs)
                    },
                    grid: {
                        display: false, // Sin líneas verticales para un diseño más limpio
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // Ocultamos la leyenda para un diseño minimalista
                },
                tooltip: {
                    backgroundColor: 'rgba(54, 162, 235, 0.8)', // Color del tooltip
                    titleFont: {
                        family: 'Roboto',
                        size: 14,
                        weight: 'bold',
                        color: '#fff' // Texto blanco en el tooltip
                    },
                    bodyFont: {
                        family: 'Roboto',
                        size: 12,
                        color: '#fff' // Texto blanco en el tooltip
                    }
                }
            }
        };

        var matriculadosChart = new Chart(document.getElementById('matriculadosChart'), {
            type: 'bar',
            data: matriculadosData,
            options: matriculadosOptions
        });
    </script>
    <script>
        // Obtener el header y el cuerpo de las asignaturas
        const header = document.getElementById('asignaturasHeader');
        const list = document.getElementById('asignaturasList');

        // Agregar un evento para mostrar/ocultar la lista al hacer clic en el header
        header.addEventListener('click', function() {
            // Alternar la visibilidad de la lista de asignaturas
            if (list.style.display === 'none') {
                list.style.display = 'block'; // Mostrar la lista
            } else {
                list.style.display = 'none'; // Ocultar la lista
            }
        });
    </script>


@stop
