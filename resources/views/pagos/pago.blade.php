@extends('adminlte::page')
@section('title', 'Pagos')

@section('content_header')
    <h1>Pagos y Descuentos</h1>
@stop

@section('content')
<div class="container">
    <div class="row">
        <!-- Información del Alumno (lado izquierdo) -->
        <div class="col-md-6 col-sm-12 mb-3">
            <div class="payment-history-box mt-4" style="min-height: 400px;"> <!-- Añade una altura mínima -->
                <div class="payment-history-header">
                    <h3>Historial de Pagos Realizados</h3>
                </div>
                <div class="payment-history-body">
                    <p><strong>Cedula-Pasaporte:</strong> {{ $alumno->dni }}</p>
                    <p><strong>Maestría:</strong> {{ $programa['nombre'] }}</p>
                    <p><strong>Arancel:</strong> ${{ number_format($programa['arancel'], 2) }}</p>
                    <p><strong>Deuda Total:</strong> ${{ number_format($alumno->monto_total, 2) }}</p>
                    <table class="table table-striped" id="pagosTable">
                        <thead>
                            <tr>
                                <th>Monto</th>
                                <th>Fecha de Pago</th>
                                <th>Comprobante</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pagos as $pago)
                                <tr>
                                    <td>${{ number_format($pago->monto, 2) }}</td>
                                    <td>{{ $pago->fecha_pago }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $pago->archivo_comprobante) }}" target="_blank"
                                            class="btn btn-info btn-sm" title="Ver Comprobante">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    </td>
                                    <td>
                                        @if ($pago->verificado)
                                            <span class="badge badge-success">Verificado</span>
                                        @else
                                            <span class="badge badge-danger">No Verificado</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Aún no has realizado ningún pago.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detalles del Pago y Modalidad de Pago (lado derecho) -->
        <div class="col-md-6 col-sm-12">
            <!-- Cuadro de detalles del pago -->
            <div class="payment-details-box mt-4" style="min-height: 400px;"> <!-- Añade una altura mínima -->
                <div class="payment-details-header">
                    <h3>Detalles del Monto a Pagar</h3>
                </div>
                <div class="payment-details-body">
                    @if ($alumno->descuento == 'academico')
                        <p>Descuento Académico (30%): ${{ number_format($programa['descuento'], 2) }}</p>
                        <p>Total a Pagar con Descuento Académico: ${{ number_format($programa['total_pagar'], 2) }}</p>
                    @elseif ($alumno->descuento == 'socioeconomico')
                        <p>Descuento Socioeconómico (20%): ${{ number_format($programa['descuento'], 2) }}</p>
                        <p>Total a Pagar con Descuento Socioeconómico:
                            ${{ number_format($programa['total_pagar'], 2) }}</p>
                    @elseif ($alumno->descuento == 'graduados')
                        <p>Descuento Graduados (20%): ${{ number_format($programa['descuento'], 2) }}</p>
                        <p>Total a Pagar con Descuento Graduados: ${{ number_format($programa['total_pagar'], 2) }}</p>
                    @elseif ($alumno->descuento == 'mejor_graduado')
                        <p>Descuento Mejor Graduado (100%): ${{ number_format($programa['descuento'], 2) }}</p>
                        <p>Total a Pagar con Descuento Mejor Graduado:
                            ${{ number_format($programa['total_pagar'], 2) }}</p>
                    @else
                        <p>Sin descuento aplicado</p>
                        <p>Total a Pagar: ${{ number_format($programa['arancel'], 2) }}</p>
                    @endif
                    <form action="{{ route('pagos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="dni" name="dni" value="{{ $alumno->dni }}">

                        <div class="form-group">
                            <label for="modalidad_pago">Selecciona la Modalidad de Pago:</label>
                            <select class="form-control" id="modalidad_pago" name="modalidad_pago">
                                <option value="unico">Pago Único</option>
                                <option value="trimestral">Pago Trimestral</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <!-- Campo de monto ajustable según la modalidad -->
                        <div class="form-group">
                            <label for="monto">Monto a Pagar:</label>
                            <input type="number" class="form-control" id="monto" name="monto" step="0.01"
                                value="{{ $alumno->monto_total }}" readonly>
                        </div>

                        <!-- Fecha de pago -->
                        <div class="form-group">
                            <label for="fecha_pago">Fecha de Pago:</label>
                            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago"
                                value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Subir comprobante -->
                        <div class="form-group">
                            <label for="archivo_comprobante">Subir Comprobante:</label>
                            <input type="file" class="form-control" id="archivo_comprobante"
                                name="archivo_comprobante" required>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-fw fa-dollar-sign"></i> Pagar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/pagos.css') }}">
@stop

@section('js')
    <script>
        function updatePaymentDetails() {
            let modalidadPago = document.getElementById('modalidad_pago').value;
            let montoInput = document.getElementById('monto');
            let montoTotal = {{ $alumno->maestria->arancel }}; 
            let montoTotalAlumno = {{ $alumno->monto_total }}; 

            if (modalidadPago === 'unico') {
                montoInput.value = montoTotalAlumno.toFixed(2);
                montoInput.readOnly = true;
            } else if (modalidadPago === 'trimestral') {
                montoInput.value = (montoTotal / 3).toFixed(2); 
                montoInput.readOnly = true;
            } else if (modalidadPago === 'otro') {
                montoInput.value = '';
                montoInput.readOnly = false;
            }
        }

        document.getElementById('modalidad_pago').addEventListener('change', updatePaymentDetails);

        document.addEventListener('DOMContentLoaded', updatePaymentDetails);
    </script>

@stop
