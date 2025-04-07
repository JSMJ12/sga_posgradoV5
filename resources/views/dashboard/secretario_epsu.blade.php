@extends('adminlte::page')
@section('title', 'Dashboard Secretario EPSSU')
@section('content_header')
    <h1 class="mb-4">Dashboard Secretario EPSU</h1>
@stop
@section('content')
    <div class="container">
        <div class="row">
            <!-- Métricas principales -->
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0"
                    style="background: linear-gradient(135deg, #055bd3, #457b9d); color: white;">
                    <div class="card-body">
                        <h5 class="card-title">Pagos Hoy</h5>
                        <p class="card-text">Monto: ${{ number_format($pagosPorDia, 2) }}</p>
                        <p class="card-text">Cantidad: {{ $cantidadPorDia }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0"
                    style="background: linear-gradient(135deg, #2efb38, #48cae4); color: white;">
                    <div class="card-body">
                        <h5 class="card-title">Pagos Este Mes</h5>
                        <p class="card-text">Monto: ${{ number_format($pagosPorMes, 2) }}</p>
                        <p class="card-text">Cantidad: {{ $cantidadPorMes }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0"
                    style="background: linear-gradient(135deg, #f4ae2d, #e0f161); color: white;">
                    <div class="card-body">
                        <h5 class="card-title">Pagos Este Año</h5>
                        <p class="card-text">Monto: ${{ number_format($pagosPorAnio, 2) }}</p>
                        <p class="card-text">Cantidad: {{ $cantidadPorAnio }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pagos por verificar -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h5>Pagos Pendientes de Verificación</h5>
                <a href="{{ route('pagos.index') }}" class="btn btn-dark btn-sm">Ver Todos</a>
            </div>
            <div class="card-body">
                <p>Total Pendientes: {{ $pagosPorVerificar }}</p>
                <ul class="list-group">
                    @foreach ($alumnosPendientes as $pendiente)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $pendiente->alumno->nombre1 }} {{ $pendiente->alumno->nombre2 }}
                            {{ $pendiente->alumno->apellidop }} {{ $pendiente->alumno->apellidom }} (Cedula-Pasaporte:
                            {{ $pendiente->alumno->dni }})
                            <span class="badge bg-danger text-white">Pendiente</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Resumen por maestría -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5>Resumen de Pagos por Maestría</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="maestriasAccordion">
                    @foreach ($maestriasConCohortes as $maestria => $cohortes)
                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header" id="heading{{ Str::slug($maestria) }}">
                                <button
                                    class="accordion-button collapsed w-100 d-flex justify-content-between align-items-center text-start fw-semibold bg-light border-0 rounded-0 px-4 py-3"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ Str::slug($maestria) }}" aria-expanded="false"
                                    aria-controls="collapse{{ Str::slug($maestria) }}"
                                    style="font-size: 1.1rem; box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);">

                                    <div class="me-auto">
                                        <i class="bi bi-journal-richtext me-2 text-primary"></i>
                                        {{ $maestria }}
                                    </div>
                                    <div class="text-muted small text-end">
                                        Total: <span
                                            class="fw-bold text-success">${{ number_format(collect($cohortes)->sum('monto'), 2) }}</span><br>
                                        Pagos: <span
                                            class="fw-bold text-info">{{ collect($cohortes)->sum('cantidad') }}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ Str::slug($maestria) }}" class="accordion-collapse collapse"
                                aria-labelledby="heading{{ Str::slug($maestria) }}" data-bs-parent="#maestriasAccordion">
                                <div class="accordion-body">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Cohorte</th>
                                                <th>Total Monto</th>
                                                <th>Cantidad</th>
                                                <th>PDF</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cohortes as $cohorteNombre => $datos)
                                                <tr>
                                                    <td>{{ $cohorteNombre }}</td>
                                                    <td>${{ number_format($datos['monto'], 2) }}</td>
                                                    <td>{{ $datos['cantidad'] }}</td>
                                                    <td>
                                                        <a href="{{ route('pagos.pdf', ['cohorte' => $cohorteNombre]) }}"
                                                            class="btn btn-danger btn-sm" target="_blank">
                                                            Descargar PDF
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

@stop
