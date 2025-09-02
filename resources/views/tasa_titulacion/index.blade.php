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

            const cleanValue = (value) => value ?? 0; // Manejo de null o undefined

            // Función para actualizar tabla y gráfico
            function actualizarTablaYGrafico(data) {
                let numeroMatriculados = cleanValue(data.numero_matriculados);
                let numeroAprobados = cleanValue(data.numero_maestrantes_aprobados);
                let retirados = cleanValue(data.retirados);
                let graduados = cleanValue(data.graduados);
                let noGraduados = numeroAprobados - graduados;

                let porcentajeRetencion = ((numeroAprobados / numeroMatriculados) * 100).toFixed(2);
                let porcentajeRetirados = ((retirados / numeroMatriculados) * 100).toFixed(2);
                let porcentajeGraduados = ((graduados / numeroMatriculados) * 100).toFixed(2);
                let porcentajeNoGraduados = ((noGraduados / numeroMatriculados) * 100).toFixed(2);

                $('#table-container').show();
                $('#tasa-data').html(`
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
                `);

                $('#titulacionChart').show();

                // Crear o actualizar gráfico
                if (chart) chart.destroy();

                chart = new Chart($('#titulacionChart')[0].getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Graduados', 'No Graduados', 'Retirados'],
                        datasets: [{
                            data: [graduados, noGraduados, retirados],
                            backgroundColor: ['#007bff', '#e83e8c', '#17a2b8'],
                            hoverBackgroundColor: ['#0056b3', '#c82333', '#138496'],
                            borderColor: ['#fff', '#fff', '#fff'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { boxWidth: 20, font: { size: 14 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `${context.label}: ${context.raw}`
                                }
                            }
                        }
                    }
                });
            }

            // Cargar Cohortes al seleccionar Maestría
            $('#maestria').change(function() {
                maestriaId = $(this).val();
                const cohorteSelect = $('#cohorte');

                if (!maestriaId) {
                    cohorteSelect.prop('disabled', true).html('<option value="">-- Seleccione una Cohorte --</option>');
                    $('#table-container, #titulacionChart, #export-button-container, #export-estudiantes-button-container').hide();
                    return;
                }

                $.ajax({
                    url: '{{ route('tasa_titulacion.cohortes', ':id') }}'.replace(':id', maestriaId),
                    method: 'GET',
                    success: function(data) {
                        cohorteSelect.prop('disabled', false).html('<option value="">-- Seleccione una Cohorte --</option>');
                        data.forEach(c => cohorteSelect.append(`<option value="${c.id}">${c.nombre}</option>`));
                    },
                    error: function() { alert('Error al cargar las cohortes.'); }
                });
            });

            // Mostrar tabla y gráfico al seleccionar Cohorte
            $('#cohorte').change(function() {
                cohorteId = $(this).val();

                if (!cohorteId) {
                    $('#table-container, #titulacionChart, #export-button-container, #export-estudiantes-button-container').hide();
                    return;
                }

                $.ajax({
                    url: '{{ route('tasa_titulacion.show', ':id') }}'.replace(':id', cohorteId),
                    success: function(data) { if (data) actualizarTablaYGrafico(data); },
                    error: function() { alert('Error al cargar los datos de la cohorte.'); }
                });

                // Mostrar/Actualizar botones de exportación
                const toggleExportButton = (selector, routeName) => {
                    if (maestriaId && cohorteId) {
                        $(selector).show();
                        let urlTemplate = '';
                        if(routeName === 'tasa_titulacion.export'){
                            urlTemplate = "{{ route('tasa_titulacion.export', ['maestria_id' => ':maestria', 'cohorte_id' => ':cohorte']) }}";
                        } else if(routeName === 'estdiantes.export'){
                            urlTemplate = "{{ route('estdiantes.export', ['maestria_id' => ':maestria', 'cohorte_id' => ':cohorte']) }}";
                        }
                        $(selector + ' a').attr('href', urlTemplate.replace(':maestria', maestriaId).replace(':cohorte', cohorteId));
                    } else {
                        $(selector).hide();
                    }
                };

                toggleExportButton('#export-button-container', 'tasa_titulacion.export');
                toggleExportButton('#export-estudiantes-button-container', 'estdiantes.export');
            });
        });
    </script>

@endsection
