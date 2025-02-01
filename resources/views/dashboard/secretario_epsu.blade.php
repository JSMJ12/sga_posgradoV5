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
                            {{ $pendiente->alumno->nombre1 }} {{ $pendiente->alumno->nombre2 }} {{ $pendiente->alumno->apellidop }} {{ $pendiente->alumno->apellidom }} (Cedula-Pasaporte: {{ $pendiente->alumno->dni }})
                            <span class="badge bg-danger text-white">Pendiente</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5>Resumen de Pagos por Cohorte</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Cohorte</th>
                            <th>Total Monto</th>
                            <th>Cantidad de Pagos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($montoPorCohorte as $cohorte => $monto)
                            <tr>
                                <td>{{ $cohorte }}</td>
                                <td>${{ number_format($monto, 2) }}</td>
                                <td>{{ $cantidadPorCohorte[$cohorte] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Resumen por maestría -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5>Resumen de Pagos por Maestría</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Maestría</th>
                            <th>Total Monto</th>
                            <th>Cantidad de Pagos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($montoPorMaestria as $maestria => $monto)
                            <tr>
                                <td>{{ $maestria }}</td>
                                <td>${{ number_format($monto, 2) }}</td>
                                <td>{{ $cantidadPorMaestria[$maestria] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
