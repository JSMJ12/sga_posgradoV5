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
                        <p><strong>Cédula/Pasaporte:</strong> {{ $alumno->dni }}</p>
                        <p><strong>Maestría:</strong> {{ $programa['nombre'] }}</p>
                        <p><strong>Deuda de Arancel:</strong> ${{ number_format($alumno->monto_total, 2) }}</p>
                        <p><strong>Deuda de Matrícula:</strong> ${{ number_format($alumno->monto_matricula, 2) }}</p>
                        <p><strong>Deuda de Inscripción:</strong> ${{ number_format($alumno->monto_inscripcion, 2) }}</p>

                        <!-- Contenedor para hacer que la tabla sea desplazable -->
                        <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                            <table class="table table-striped" id="pagosTable">
                                <thead>
                                    <tr>
                                        <th>Monto</th>
                                        <th>Fecha de Pago</th>
                                        <th>Comprobante</th>
                                        <th>Estado</th>
                                        <th>Tipo de Pago</th> <!-- Nueva columna -->
                                        <th>Modalidad</th> <!-- Nueva columna -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pagos as $pago)
                                        <tr>
                                            <td>${{ number_format($pago->monto, 2) }}</td>
                                            <td>{{ $pago->fecha_pago }}</td>
                                            <td>
                                                <a href="{{ asset('storage/' . $pago->archivo_comprobante) }}"
                                                    target="_blank" class="btn btn-info btn-sm" title="Ver Comprobante">
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
                                            <td>{{ ucfirst($pago->tipo_pago) }}</td>
                                            <td>{{ ucfirst($pago->modalidad_pago) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Aún no has realizado ningún pago.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Detalles del Pago y Modalidad de Pago (lado derecho) -->
            <div class="col-md-6 col-sm-12">
                <div class="payment-details-box mt-4" style="min-height: 400px;">
                    <div class="payment-details-header">
                        <h3>Detalles del Monto a Pagar</h3>
                    </div>
                    <div class="payment-details-body">
                        @if ($alumno->descuento)
                            <p>Descuento {{ $alumno->descuento->nombre }} ({{ $alumno->descuento->porcentaje }}%):
                                ${{ number_format($programa['descuento'], 2) }}
                            </p>
                            <p>Arancel Total a Pagar con Descuento {{ $alumno->descuento->nombre }}:
                                ${{ number_format($alumno->monto_total, 2) }}
                            </p>
                        @else
                            <p>Sin descuento aplicado</p>
                            <p>Total a Pagar de Arancel: ${{ number_format($alumno->monto_total, 2) }}</p>
                        @endif


                        <form action="{{ route('pagos.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="dni" name="dni" value="{{ $alumno->dni }}">

                            <!-- Tipo de Pago -->
                            <div class="form-group">
                                <label for="tipo_pago">Tipo de Pago:</label>
                                <select class="form-control" id="tipo_pago" name="tipo_pago">
                                    <option value="arancel">Arancel</option>
                                    <option value="matricula">Matrícula</option>
                                    <option value="inscripcion">Inscripción</option>
                                </select>
                            </div>


                            <!-- Modalidad -->
                            <div class="form-group">
                                <label for="modalidad_pago">Selecciona la Modalidad de Pago:</label>
                                <select class="form-control" id="modalidad_pago" name="modalidad_pago">
                                    <option value="unico">Pago Único</option>
                                    <option value="trimestral">Pago Trimestral</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>

                            <!-- Monto -->
                            <div class="form-group">
                                <label for="monto">Monto a Pagar:</label>
                                <input type="number" class="form-control" id="monto" name="monto" step="0.01"
                                    value="{{ $alumno->monto_total }}" readonly>
                                <p class="text-success mt-2" id="descuento_aplicado" style="display: none;">
                                    Descuento del 5% aplicado por pago único de arancel.
                                </p>
                            </div>

                            <!-- Fecha -->
                            <div class="form-group">
                                <label for="fecha_pago">Fecha de Pago:</label>
                                <input type="date" class="form-control" id="fecha_pago" name="fecha_pago"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Comprobante -->
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
            let tipoPago = document.getElementById('tipo_pago').value;
            let montoInput = document.getElementById('monto');
            let mensajeDescuento = document.getElementById('descuento_aplicado');

            let montoBaseArancel = {{ $alumno->maestria->arancel }};
            let montoMatricula = {{ $alumno->monto_matricula }};
            let montoInscripcion = {{ $alumno->monto_inscripcion }};
            let montoTotalAlumno = {{ $alumno->monto_total }};
            let tienePagoPrevioArancel = {{ $pagosPreviosArancel ? 'true' : 'false' }};

            let montoFinal = 0;
            let aplicarDescuento = false;

            if (tipoPago === 'arancel') {
                if (modalidadPago === 'unico') {
                    montoFinal = montoTotalAlumno;
                    aplicarDescuento = !tienePagoPrevioArancel;
                } else if (modalidadPago === 'trimestral') {
                    montoFinal = montoBaseArancel / 3;
                }
            } else if (tipoPago === 'matricula') {
                if (modalidadPago === 'unico') {
                    montoFinal = montoMatricula;
                } else if (modalidadPago === 'trimestral') {
                    montoFinal = montoMatricula / 3;
                }
            } else if (tipoPago === 'inscripcion') {
                if (modalidadPago === 'unico') {
                    montoFinal = montoInscripcion;
                } else if (modalidadPago === 'trimestral') {
                    montoFinal = montoInscripcion / 3;
                }
            }

            if (modalidadPago === 'unico' || modalidadPago === 'trimestral') {
                if (aplicarDescuento) {
                    let descuento = montoTotalAlumno * 0.05;
                    montoFinal = montoFinal - descuento;
                    mensajeDescuento.style.display = 'block';
                } else {
                    mensajeDescuento.style.display = 'none';
                }
                montoInput.value = montoFinal.toFixed(2);
                montoInput.readOnly = true;
            } else { // modalidad "otro"
                montoInput.value = '';
                montoInput.readOnly = false;
                mensajeDescuento.style.display = 'none';
            }
        }

        document.getElementById('modalidad_pago').addEventListener('change', updatePaymentDetails);
        document.getElementById('tipo_pago').addEventListener('change', updatePaymentDetails);
        document.addEventListener('DOMContentLoaded', updatePaymentDetails);
    </script>

@stop
