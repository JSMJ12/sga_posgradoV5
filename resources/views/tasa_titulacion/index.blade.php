@extends('adminlte::page')

@section('title', 'Dashboard Tasa Titulación')

@section('content_header')
    <h1 class="text-dark" style="font-family: 'Times New Roman', serif; font-weight: bold;">Tasa de Titulación</h1>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="card shadow-lg border-0">
            <div class="card-body custom-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Selección de Maestrías -->
                        <div class="mb-4">
                            <label for="maestria" class="form-label font-weight-bold">
                                <i class="fas fa-graduation-cap me-2"></i>Seleccionar Maestría
                            </label>
                            <div class="input-group shadow-sm rounded">
                                <span class="input-group-text bg-light text-primary">
                                    <i class="fas fa-book"></i>
                                </span>
                                <select id="maestria" class="form-control rounded-end">
                                    <option value="">-- Seleccione una Maestría --</option>
                                    @foreach ($maestrias as $maestria)
                                        <option value="{{ $maestria->id }}">{{ $maestria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Selección de Cohortes -->
                        <div>
                            <label for="cohorte" class="form-label font-weight-bold">
                                <i class="fas fa-users me-2"></i>Seleccionar Cohorte
                            </label>
                            <div class="input-group shadow-sm rounded">
                                <span class="input-group-text bg-light text-success">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <select id="cohorte" class="form-control rounded-end" disabled>
                                    <option value="">-- Seleccione una Cohorte --</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3 text-center" id="export-button-container" style="display: none;">
                            <a id="export-excel" class="btn btn-success shadow-sm">
                                <i class="fas fa-file-excel"></i> Descargar SIIES Graduados
                            </a>
                        </div>
                        <div class="mt-3 text-center" id="export-estudiantes-button-container" style="display: none;">
                            <a id="export-excel-estudiantes" class="btn btn-info shadow-sm">
                                <i class="fas fa-file-excel"></i> Descargar SIIES Estudiantes
                            </a>
                        </div>
                    </div>

                    <div class="col-md-5 col-12 d-flex align-items-center justify-content-center custom-chart">
                        <canvas id="titulacionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .custom-card-body {
                max-height: 450px;
                overflow-y: auto;
                /* Scroll si se excede el tamaño */
            }

            @media (max-width: 768px) {
                .custom-chart {
                    transform: none;
                    /* Elimina desplazamiento en dispositivos pequeños */
                }

                .custom-card-body {
                    max-height: none;
                }
            }

            #titulacionChart {
                max-width: 100% !important;
                height: auto !important;
                min-height: 250px;
                /* Tamaño mínimo para pantallas pequeñas */
            }
        </style>


        <!-- Tabla de Resultados -->
        <div class="card mt-4 shadow-lg border-0" id="table-container" style="display: none;">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Resultados de la Tasa de Titulación</h5>
            </div>
            <div class="card-body">
                <!-- Contenedor responsivo -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Número de Matriculados</th>
                                <th>Número de Aprobados</th>
                                <th>Retirados</th>
                                <th>Graduados</th>
                                <th>No Graduados</th>
                                <th>% Retención</th>
                                <th>% Retirados</th>
                                <th>% Graduados</th>
                                <th>% No Graduados</th>
                            </tr>
                        </thead>
                        <tbody id="tasa-data" class="table-light"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            let chart = null;
            let maestriaId = null;
            let cohorteId = null;

            // Cargar Cohortes al seleccionar Maestría
            $('#maestria').change(function() {
                maestriaId = $(this).val(); // Usar la variable global maestriaId
                let cohorteSelect = $('#cohorte');

                if (maestriaId) {
                    $.ajax({
                        url: '{{ route('tasa_titulacion.cohortes', ':id') }}'.replace(':id',
                            maestriaId),
                        method: 'GET',
                        success: function(data) {
                            cohorteSelect.prop('disabled', false);
                            cohorteSelect.empty().append(
                                '<option value="">-- Seleccione una Cohorte --</option>');
                            data.forEach(cohorte => {
                                cohorteSelect.append(
                                    `<option value="${cohorte.id}">${cohorte.nombre}</option>`
                                );
                            });
                        },
                        error: function() {
                            alert('Error al cargar las cohortes.');
                        }
                    });
                } else {
                    cohorteSelect.prop('disabled', true).empty().append(
                        '<option value="">-- Seleccione una Cohorte --</option>');
                    $('#table-container').hide();
                    $('#titulacionChart').hide();
                }
            });

            // Mostrar tabla y gráfico al seleccionar Cohorte
            $('#cohorte').change(function() {
                cohorteId = $(this).val(); // Usar la variable global cohorteId

                if (cohorteId) {
                    $.ajax({
                        url: '{{ route('tasa_titulacion.show', ':id') }}'.replace(':id', cohorteId),
                        success: function(data) {
                            if (data) {
                                const cleanValue = (value) => value === null || value ===
                                    undefined ? 0 : value;

                                let numeroMatriculados = cleanValue(data.numero_matriculados);
                                let numeroAprobados = cleanValue(data
                                    .numero_maestrantes_aprobados);
                                let retirados = cleanValue(data.retirados);
                                let graduados = cleanValue(data.graduados);
                                let noGraduados = numeroAprobados - graduados;

                                let porcentajeRetencion = ((numeroAprobados /
                                    numeroMatriculados) * 100).toFixed(2);
                                let porcentajeRetirados = ((retirados / numeroMatriculados) *
                                    100).toFixed(2);
                                let porcentajeGraduados = ((graduados / numeroAprobados) * 100)
                                    .toFixed(2);
                                let porcentajeNoGraduados = ((noGraduados / numeroAprobados) *
                                    100).toFixed(2);

                                $('#table-container').show();

                                let tableRow = `
                            <tr>
                                <td>${numeroMatriculados}</td>
                                <td>${numeroAprobados}</td>
                                <td>${retirados}</td>
                                <td>${graduados}</td>
                                <td>${noGraduados}</td>
                                <td>${isNaN(porcentajeRetencion) ? '0.00%' : porcentajeRetencion + '%'}</td>
                                <td>${isNaN(porcentajeRetirados) ? '0.00%' : porcentajeRetirados + '%'}</td>
                                <td>${isNaN(porcentajeGraduados) ? '0.00%' : porcentajeGraduados + '%'}</td>
                                <td>${isNaN(porcentajeNoGraduados) ? '0.00%' : porcentajeNoGraduados + '%'}</td>
                            </tr>
                        `;

                                $('#tasa-data').html(tableRow);

                                $('#titulacionChart').show();

                                // Crear o actualizar gráfico
                                if (chart) chart.destroy();

                                let ctx = document.getElementById('titulacionChart').getContext(
                                    '2d');
                                chart = new Chart(ctx, {
                                    type: 'doughnut',
                                    data: {
                                        labels: ['Graduados', 'No Graduados',
                                            'Retirados'
                                        ],
                                        datasets: [{
                                            data: [graduados, noGraduados,
                                                retirados
                                            ],
                                            backgroundColor: ['#007bff',
                                                '#e83e8c', '#17a2b8'
                                            ],
                                            hoverBackgroundColor: ['#0056b3',
                                                '#c82333', '#138496'
                                            ],
                                            borderColor: ['#fff', '#fff',
                                                '#fff'
                                            ],
                                            borderWidth: 2
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'top', // Leyenda al lado derecho
                                                labels: {
                                                    boxWidth: 20,
                                                    font: {
                                                        size: 14
                                                    }
                                                }
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return `${context.label}: ${context.raw}`;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        },
                        error: function() {
                            alert('Error al cargar los datos de la cohorte.');
                        }
                    });

                    if (maestriaId && cohorteId) {
                        $('#export-button-container').show(); // Mostrar el botón
                        // Actualizar el enlace con parámetros
                        let exportUrl =
                            '{{ route('tasa_titulacion.export', ['maestria_id' => ':maestria', 'cohorte_id' => ':cohorte']) }}'
                            .replace(':maestria', maestriaId)
                            .replace(':cohorte', cohorteId);
                        $('#export-excel').attr('href', exportUrl);
                    } else {
                        $('#export-button-container').hide();
                    }
                    if (maestriaId && cohorteId) {
                        $('#export-estudiantes-button-container').show(); // Mostrar el botón
                        // Actualizar el enlace con parámetros
                        let exportUrl =
                            '{{ route('estdiantes.export', ['maestria_id' => ':maestria', 'cohorte_id' => ':cohorte']) }}'
                            .replace(':maestria', maestriaId)
                            .replace(':cohorte', cohorteId);
                        $('#export-excel-estudiantes').attr('href', exportUrl);
                    } else {
                        $('#export-estudiantes-button-container').hide();
                    }
                } else {
                    $('#table-container').hide();
                    $('#titulacionChart').hide();
                }
            });
        });
    </script>
@endsection
