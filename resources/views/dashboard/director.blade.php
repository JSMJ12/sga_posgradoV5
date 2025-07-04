@extends('adminlte::page')

@section('title', 'Dashboard Director')

@section('content_header')
    <h1>Dashboard - Director</h1>
@stop

@section('content')
    <div class="container-fluid px-2 px-md-4">
        <form id="formMaestria" class="mb-5 p-4 bg-white rounded shadow-sm">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <label for="maestriaSelect" class="form-label fs-5 fw-bold text-primary mb-3">
                        Seleccione una Maestría:
                    </label>
                    <select id="maestriaSelect" class="form-control form-select shadow-sm border-primary"
                        style="height: 48px; font-size: 1.1rem;">
                        <option value="" selected disabled>-- Seleccione --</option>
                        @foreach ($maestrias as $maestria)
                            <option value="{{ $maestria->id }}">{{ $maestria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div id="maestriaDetalle" style="display:none;">
            <h3 id="maestriaNombre" class="text-center text-info my-4"></h3>

            <div id="tarjetas" class="row mb-4 gy-3"></div>

            <div class="card shadow rounded mb-5" id="tablaPagosContainer">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0 fw-bold">Resumen Financiero por Cohorte</h5>
                </div>
                <div class="card-body p-0" id="tablaPagosBody"></div>
            </div>

            <div id="graficos" style="display:none;">
                <div class="row mb-4 gy-4">
                    <div class="col-12 col-md-6">
                        <div class="card shadow-lg rounded border-0 h-100">
                            <div class="card-header bg-primary text-white fs-5 fw-bold">Arancel</div>
                            <div class="card-body" style="height: 350px;">
                                <canvas id="arancelChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card shadow-lg rounded border-0 h-100">
                            <div class="card-header bg-success text-white fs-5 fw-bold">Matrícula</div>
                            <div class="card-body" style="height: 350px;">
                                <canvas id="matriculaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4 gy-4">
                    <div class="col-12 col-md-8 mx-auto">
                        <div class="card shadow-lg rounded border-0 h-100">
                            <div class="card-header bg-warning text-white fs-5 fw-bold">Inscripción</div>
                            <div class="card-body" style="height: 350px;">
                                <canvas id="inscripcionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('modales.verificar_calificacion_cohortes_modal')
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('maestriaSelect');
            const detalle = document.getElementById('maestriaDetalle');
            const maestriaNombre = document.getElementById('maestriaNombre');
            const tarjetas = document.getElementById('tarjetas');
            const tablaPagosBody = document.getElementById('tablaPagosBody');
            const graficos = document.getElementById('graficos');

            let arancelChart, matriculaChart, inscripcionChart;

            select.addEventListener('change', function() {
                if (!this.value) {
                    detalle.style.display = 'none';
                    return;
                }

                fetch(`{{ url('/dashboard/director/maestria') }}/${this.value}/resumen`)
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                        return res.json();
                    })
                    .then(data => {
                        detalle.style.display = '';
                        maestriaNombre.textContent = data.maestria.nombre;

                        // Tarjetas
                        tarjetas.innerHTML = `
                            <div class="col-12 col-md-4">
                                <div class="small-box bg-success shadow rounded text-center py-3">
                                    <div class="inner">
                                        <h3>${data.maestria.totalDocentes}</h3>
                                        <p class="fw-bold">Docentes</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-chalkboard-teacher"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="small-box bg-danger shadow rounded text-center py-3">
                                    <div class="inner">
                                        <h3>${data.maestria.totalAlumnos}</h3>
                                        <p class="fw-bold">Alumnos</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-user-graduate"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="small-box bg-secondary shadow rounded text-center py-3">
                                    <div class="inner">
                                        <h3>${data.maestria.totalPostulantes}</h3>
                                        <p class="fw-bold">Postulantes</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-user-tie"></i>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Tabla pagos
                        if (data.maestria.cohortesResumen.length === 0) {
                            tablaPagosBody.innerHTML =
                                `<div class="alert alert-warning text-center m-0">No hay datos de deuda ni recaudación disponibles para mostrar.</div>`;
                            graficos.style.display = 'none';
                            return;
                        }

                        let tablaHTML = `
                            <div class="table-responsive">
                            <table class="table table-bordered table-hover shadow-sm text-center align-middle mb-0">
                                <thead class="table-info text-dark">
                                    <tr>
                                        <th>Cohorte</th>
                                        <th>Deuda Arancel</th>
                                        <th>Recaudado Arancel</th>
                                        <th>Deuda Matrícula</th>
                                        <th>Recaudado Matrícula</th>
                                        <th>Deuda Inscripción</th>
                                        <th>Recaudado Inscripción</th>
                                        <th>% Recaudado</th>
                                        <th>Asignaturas</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                        const labels = [];
                        const arancelDeuda = [];
                        const arancelRecaudado = [];
                        const matriculaDeuda = [];
                        const matriculaRecaudado = [];
                        const inscripcionDeuda = [];
                        const inscripcionRecaudado = [];

                        data.maestria.cohortesResumen.forEach(c => {
                            const totalDeuda = c.deudaArancel + c.deudaMatricula + c.deudaInscripcion;
                            const totalRecaudado = c.recaudadoArancel + c.recaudadoMatricula + c.recaudadoInscripcion;
                            const porcentaje = totalDeuda > 0 ? (totalRecaudado / totalDeuda) * 100 : 0;

                            let badgeClass = 'bg-danger';
                            if (porcentaje >= 75) badgeClass = 'bg-success';
                            else if (porcentaje >= 50) badgeClass = 'bg-warning text-dark';

                            tablaHTML += `
                                <tr>
                                    <td>${c.nombre}</td>
                                    <td>$${c.deudaArancel.toFixed(2)}</td>
                                    <td>$${c.recaudadoArancel.toFixed(2)}</td>
                                    <td>$${c.deudaMatricula.toFixed(2)}</td>
                                    <td>$${c.recaudadoMatricula.toFixed(2)}</td>
                                    <td>$${c.deudaInscripcion.toFixed(2)}</td>
                                    <td>$${c.recaudadoInscripcion.toFixed(2)}</td>
                                    <td><span class="badge ${badgeClass}">${porcentaje.toFixed(2)}%</span></td>
                                    <td>
                                        <button class="btn btn-info btn-sm btn-verificaciones"
                                            data-cohorte-id="${c.id}"
                                            data-toggle="modal"
                                            data-target="#verificacionModal"
                                            title="Ver Asignaturas">
                                            <i class="fas fa-clipboard-check me-1"></i> Asignaturas
                                        </button>
                                    </td>
                                </tr>
                            `;

                            labels.push(c.nombre);
                            arancelDeuda.push(c.deudaArancel);
                            arancelRecaudado.push(c.recaudadoArancel);
                            matriculaDeuda.push(c.deudaMatricula);
                            matriculaRecaudado.push(c.recaudadoMatricula);
                            inscripcionDeuda.push(c.deudaInscripcion);
                            inscripcionRecaudado.push(c.recaudadoInscripcion);
                        });

                        tablaHTML += `</tbody></table></div>`;
                        tablaPagosBody.innerHTML = tablaHTML;

                        // Mostrar gráficos
                        graficos.style.display = '';

                        // Destruir gráficos previos si existen
                        if (arancelChart) arancelChart.destroy();
                        if (matriculaChart) matriculaChart.destroy();
                        if (inscripcionChart) inscripcionChart.destroy();

                        const createChart = (ctx, label1, data1, label2, data2, color1, color2) => {
                            // Gradientes para barras
                            const gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
                            gradient1.addColorStop(0, color1 + 'cc');
                            gradient1.addColorStop(1, color1 + '33');
                            const gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
                            gradient2.addColorStop(0, color2 + 'cc');
                            gradient2.addColorStop(1, color2 + '33');

                            return new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels.length ? labels : ['Sin datos'],
                                    datasets: [{
                                            label: label1,
                                            backgroundColor: gradient1,
                                            borderColor: color1,
                                            borderWidth: 2,
                                            data: data1.length ? data1 : [0],
                                            borderRadius: 6,
                                            maxBarThickness: 40,
                                            hoverBackgroundColor: color1,
                                        },
                                        {
                                            label: label2,
                                            backgroundColor: gradient2,
                                            borderColor: color2,
                                            borderWidth: 2,
                                            data: data2.length ? data2 : [0],
                                            borderRadius: 6,
                                            maxBarThickness: 40,
                                            hoverBackgroundColor: color2,
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    layout: {
                                        padding: {
                                            top: 20,
                                            bottom: 20,
                                            left: 10,
                                            right: 10,
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                            labels: {
                                                font: {
                                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
                                                    size: 14,
                                                    weight: '600',
                                                },
                                                color: '#333',
                                                padding: 20,
                                            }
                                        },
                                        tooltip: {
                                            enabled: true,
                                            backgroundColor: 'rgba(0,0,0,0.8)',
                                            titleFont: {
                                                size: 16,
                                                weight: 'bold'
                                            },
                                            bodyFont: {
                                                size: 14
                                            },
                                            padding: 10,
                                            cornerRadius: 8,
                                            mode: 'index',
                                            intersect: false,
                                        }
                                    },
                                    scales: {
                                        x: {
                                            stacked: true,
                                            grid: {
                                                display: false,
                                            },
                                            ticks: {
                                                font: {
                                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
                                                    size: 12,
                                                },
                                                color: '#555',
                                                maxRotation: 45,
                                                minRotation: 30,
                                            }
                                        },
                                        y: {
                                            stacked: true,
                                            beginAtZero: true,
                                            grid: {
                                                color: '#e0e0e0',
                                                borderDash: [5, 5],
                                            },
                                            ticks: {
                                                font: {
                                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
                                                    size: 12,
                                                },
                                                color: '#555',
                                                stepSize: Math.max(...data1.concat(data2)) / 5 || 10,
                                            }
                                        }
                                    },
                                    animation: {
                                        duration: 1000,
                                        easing: 'easeOutQuart',
                                    }
                                }
                            });
                        };

                        arancelChart = createChart(
                            document.getElementById('arancelChart').getContext('2d'),
                            'Deuda Arancel', arancelDeuda,
                            'Recaudado Arancel', arancelRecaudado,
                            '#1976d2', '#4caf50'
                        );
                        matriculaChart = createChart(
                            document.getElementById('matriculaChart').getContext('2d'),
                            'Deuda Matrícula', matriculaDeuda,
                            'Recaudado Matrícula', matriculaRecaudado,
                            '#388e3c', '#ffb300'
                        );
                        inscripcionChart = createChart(
                            document.getElementById('inscripcionChart').getContext('2d'),
                            'Deuda Inscripción', inscripcionDeuda,
                            'Recaudado Inscripción', inscripcionRecaudado,
                            '#fbc02d', '#e53935'
                        );
                    })
                    .catch(err => {
                        detalle.style.display = 'none';
                        graficos.style.display = 'none';
                        tablaPagosBody.innerHTML =
                            `<div class="alert alert-danger text-center m-0">Error al cargar los datos de la maestría: <br><b>${err.message}</b></div>`;
                    });
            });
        });

        // Modal verificaciones
        $(document).on('click', '.btn-verificaciones', function() {
            let cohorteId = $(this).data('cohorte-id');
            $.ajax({
                url: '/cohortes/' + cohorteId + '/verificaciones',
                method: 'GET',
                success: function(data) {
                    let tbody = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            let icon = item.calificado ?
                                '<i class="fas fa-check-circle text-success"></i>' :
                                '<i class="fas fa-times-circle text-danger"></i>';

                            let pdfButton = item.notas_existen ?
                                `<a href="${item.pdf_notas_url}" class="btn btn-danger btn-sm" title="Descargar PDF" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>` :
                                'No disponible';

                            tbody += `
                            <tr>
                                <td>${item.docente}</td>
                                <td>${item.asignatura}</td>
                                <td>${icon}</td>
                                <td>${pdfButton}</td>
                            </tr>
                        `;
                        });
                    } else {
                        tbody =
                            '<tr><td colspan="4" class="text-muted">No hay registros de verificación.</td></tr>';
                    }
                    $('#verificacionesTableBody').html(tbody);
                },
                error: function(xhr, status, error) {
                    let msg = `Error al cargar los datos del modal.<br>
                        <b>Estado:</b> ${status}<br>
                        <b>Mensaje:</b> ${xhr.status} - ${xhr.statusText}<br>
                        <b>Detalle:</b> ${error}`;
                    $('#verificacionesTableBody').html(
                        `<tr><td colspan="4" class="text-danger">${msg}</td></tr>`
                    );
                }
            });
        });
    </script>
    <style>
        @media (max-width: 767.98px) {
            .small-box .inner h3 {
                font-size: 2rem;
            }
            .small-box .inner p {
                font-size: 1rem;
            }
            .card .card-header {
                font-size: 1rem !important;
            }
            .table-responsive {
                font-size: 0.95rem;
            }
        }
        .small-box {
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .small-box .icon {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 2.5rem;
            opacity: 0.2;
        }
        .small-box .inner {
            position: relative;
            z-index: 2;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .card {
            min-width: 0;
        }
    </style>
@stop
