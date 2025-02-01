@extends('adminlte::page')
@section('title', 'Dashboard Secretario')
@section('content_header')
    <h1>Dashboard</h1>
@stop
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $totalUsuarios }}</h3>
                        <p>Usuarios Generales</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>

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
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $totalSecretarios }}</h3>
                        <p>Secretarios</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-user-tie"></i>
                    </div>
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
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalMaestrias }}</h3>
                        <p>Maestrías</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
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
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header" style="background-color: #003366; color: white;">
                        <h5 class="card-title">Alumnos Matriculados por Maestría</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="matriculadosChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header" style="background-color: #003366; color: white;">
                        <h5 class="card-title">Postulantes por Maestría</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="postulantesPorMaestriaChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
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

        // Configuración para el gráfico de postulantes por maestría
        var postulantesPorMaestria = {!! json_encode($postulantesPorMaestria) !!};
        var maestrias = postulantesPorMaestria.map(item => item.maestria);
        var postulantes = postulantesPorMaestria.map(item => item.cantidad_postulantes);

        var postulantesData = {
            labels: maestrias,
            datasets: [{
                label: 'Postulantes por maestría',
                data: postulantes,
                backgroundColor: postulantes.map((_, index) => {
                    const gradient = document.createElement('canvas').getContext('2d')
                        .createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(75, 192, 192, 0.8)');
                    gradient.addColorStop(1, 'rgba(75, 192, 192, 0.2)');
                    return gradient;
                }),
                borderColor: postulantes.map(() => 'rgba(75, 192, 192, 1)'),
                borderWidth: 2,
                hoverBorderColor: 'rgba(255, 255, 255, 1)',
                hoverBackgroundColor: postulantes.map(() => 'rgba(75, 192, 192, 0.9)')
            }]
        };

        var postulantesOptions = {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        borderDash: [5, 5],
                    },
                    ticks: {
                        color: 'black',
                    }
                },
                x: {
                    ticks: {
                        color: 'black',
                    },
                    grid: {
                        display: false,
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    titleFont: {
                        family: 'Roboto',
                        size: 16,
                        weight: 'bold',
                        color: '#fff'
                    },
                    bodyFont: {
                        family: 'Roboto',
                        size: 14,
                        color: '#fff'
                    }
                }
            }
        };

        var postulantesChart = new Chart(document.getElementById('postulantesPorMaestriaChart'), {
            type: 'bar',
            data: postulantesData,
            options: postulantesOptions
        });
    </script>

@stop
@section('css')
    <style>
        .hide-by-default {
            display: none;
        }

        .maestria-nombre {
            font-size: 12px;
            font-family: 'Times New Roman', Times, serif;
            /* Ajusta el tamaño de fuente según tu preferencia */
        }

        /* Estilo para el ID de maestría */
        .maestria-id {
            font-weight: bold;
            /* Texto en negrita para el ID */
            margin-right: 3px;
            /* Espacio entre el ID y el nombre */
            cursor: pointer;
            /* Cambia el cursor al hacer hover sobre el ID */
        }

        #matriculadosChart,
        #postulantesPorMaestriaChart {
            background-color: white;
            /* Fondo oscuro semitransparente */
            border-radius: 15px;
            /* Bordes redondeados */
            padding: 15px;

            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            /* Sombras suaves */
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

@stop
