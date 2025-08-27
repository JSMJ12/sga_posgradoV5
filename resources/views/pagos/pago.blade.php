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
                <div class="payment-history-box mt-4" style="min-height: 400px;">
                    <div class="payment-history-header">
                        <h3>Historial de Pagos Realizados</h3>
                    </div>
                    <div class="payment-history-body">
                        <p><strong>Cédula/Pasaporte:</strong> {{ $alumno->dni }}</p>

                        @foreach($programas as $programa)
                            <div class="mb-2">
                                <p><strong>Maestría:</strong> {{ $programa['nombre'] }}</p>

                                <p><strong>Deuda de Arancel:</strong>
                                    @if (($programa['monto_arancel'] ?? 0) < 0)
                                        <span style="color: green;">Reembolso a recibir: ${{ number_format(abs($programa['monto_arancel']), 2) }}</span>
                                    @else
                                        ${{ number_format($programa['monto_arancel'], 2) }}
                                    @endif
                                </p>

                                <p><strong>Deuda de Matrícula:</strong>
                                    @if (($programa['monto_matricula'] ?? 0) < 0)
                                        <span style="color: green;">Reembolso a recibir: ${{ number_format(abs($programa['monto_matricula']), 2) }}</span>
                                    @else
                                        ${{ number_format($programa['monto_matricula'], 2) }}
                                    @endif
                                </p>

                                <p><strong>Deuda de Inscripción:</strong>
                                    @if (($programa['monto_inscripcion'] ?? 0) < 0)
                                        <span style="color: green;">Reembolso a recibir: ${{ number_format(abs($programa['monto_inscripcion']), 2) }}</span>
                                    @else
                                        ${{ number_format($programa['monto_inscripcion'], 2) }}
                                    @endif
                                </p>
                                <hr>
                            </div>
                        @endforeach

                        <!-- Tabla de pagos -->
                        <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                            <table class="table table-striped" id="pagosTable">
                                <thead>
                                    <tr>
                                        <th>Monto</th>
                                        <th>Fecha de Pago</th>
                                        <th>Comprobante</th>
                                        <th>Estado</th>
                                        <th>Tipo de Pago</th>
                                        <th>Modalidad</th>
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

                        @php
                            $maestriasConDeuda = collect($programas)->filter(function($p){
                                return ($p['monto_arancel'] ?? 0) > 0 || ($p['monto_matricula'] ?? 0) > 0 || ($p['monto_inscripcion'] ?? 0) > 0;
                            });
                            $mostrarSelectMaestria = $maestriasConDeuda->count() > 1;
                            $maestriaInicial = $maestriasConDeuda->first();
                        @endphp

                        @if($mostrarSelectMaestria)
                            <div class="form-group">
                                <label for="maestria_select">Selecciona Maestría:</label>
                                <select class="form-control" id="maestria_select">
                                    @foreach($maestriasConDeuda as $m)
                                        <option value="{{ $m['maestria_id'] }}"
                                                data-arancel="{{ $m['monto_arancel'] ?? 0 }}"
                                                data-matricula="{{ $m['monto_matricula'] ?? 0 }}"
                                                data-inscripcion="{{ $m['monto_inscripcion'] ?? 0 }}">
                                            {{ $m['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <p><strong>Maestría a Pagar:</strong> {{ $maestriaInicial['nombre'] }}</p>
                        @endif

                        <form action="{{ route('pagos.store') }}" method="POST" enctype="multipart/form-data" id="pagoForm">
                            @csrf
                            <input type="hidden" id="dni" name="dni" value="{{ $alumno->dni }}">
                            <input type="hidden" id="maestria_id" name="maestria_id" value="{{ $maestriaInicial['maestria_id'] ?? '' }}">

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
                                <input type="number" class="form-control" id="monto" name="monto" step="0.01" readonly>
                            </div>

                            <!-- Fecha -->
                            <div class="form-group">
                                <label for="fecha_pago">Fecha de Pago:</label>
                                <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Comprobante -->
                            <div class="form-group">
                                <label for="archivo_comprobante">Subir Comprobante:</label>
                                <input type="file" class="form-control" id="archivo_comprobante" name="archivo_comprobante" required>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalidadPago   = document.getElementById('modalidad_pago');
            const tipoPago        = document.getElementById('tipo_pago');
            const montoInput      = document.getElementById('monto');
            const fechaPago       = document.getElementById('fecha_pago');
            const form            = document.getElementById('pagoForm');
            const maestriaSelect  = document.getElementById('maestria_select');
            const maestriaIdInput = document.getElementById('maestria_id');
            const archivoInput    = document.getElementById('archivo_comprobante');

            // Valores iniciales de deuda según maestría seleccionada
            let montoArancel     = parseFloat(maestriaSelect ? maestriaSelect.selectedOptions[0].dataset.arancel     : {{ $maestriaInicial['monto_total'] ?? 0 }});
            let montoMatricula   = parseFloat(maestriaSelect ? maestriaSelect.selectedOptions[0].dataset.matricula   : {{ $maestriaInicial['monto_matricula'] ?? 0 }});
            let montoInscripcion = parseFloat(maestriaSelect ? maestriaSelect.selectedOptions[0].dataset.inscripcion : {{ $maestriaInicial['monto_inscripcion'] ?? 0 }});

            /** 🔄 Actualiza el monto mostrado según tipo/modalidad */
            function updatePaymentDetails() {
                const modalidad = modalidadPago.value;
                const tipo = tipoPago.value;
                let montoFinal = 0;

                switch (tipo) {
                    case 'arancel':
                        montoFinal = modalidad === 'trimestral' ? montoArancel / 3 : montoArancel;
                        break;
                    case 'matricula':
                        montoFinal = modalidad === 'trimestral' ? montoMatricula / 3 : montoMatricula;
                        break;
                    case 'inscripcion':
                        montoFinal = modalidad === 'trimestral' ? montoInscripcion / 3 : montoInscripcion;
                        break;
                }

                if (modalidad === 'otro') {
                    montoInput.value = '';
                    montoInput.readOnly = false;
                } else {
                    montoInput.value = Math.max(0, montoFinal).toFixed(2);
                    montoInput.readOnly = true;
                }
            }

            /** 🔄 Cambia de maestría y actualiza montos */
            if (maestriaSelect) {
                maestriaSelect.addEventListener('change', () => {
                    const selected = maestriaSelect.selectedOptions[0];

                    montoArancel     = parseFloat(selected.dataset.arancel)     || 0;
                    montoMatricula   = parseFloat(selected.dataset.matricula)   || 0;
                    montoInscripcion = parseFloat(selected.dataset.inscripcion) || 0;

                    maestriaIdInput.value = selected.value;

                    // Habilitar/deshabilitar tipo de pago según deuda
                    [...tipoPago.options].forEach(opt => {
                        opt.disabled = false;
                        if (opt.value === 'arancel'     && montoArancel     <= 0) opt.disabled = true;
                        if (opt.value === 'matricula'   && montoMatricula   <= 0) opt.disabled = true;
                        if (opt.value === 'inscripcion' && montoInscripcion <= 0) opt.disabled = true;
                    });

                    updatePaymentDetails();
                });
            }

            modalidadPago.addEventListener('change', updatePaymentDetails);
            tipoPago.addEventListener('change', updatePaymentDetails);

            // Cargar valores iniciales
            updatePaymentDetails();

            /** ✅ Validación y confirmación antes de enviar */
            form.addEventListener('submit', e => {
                e.preventDefault();

                const montoForm = parseFloat(montoInput.value || 0);
                const fechaForm = fechaPago.value;
                const archivo   = archivoInput.files[0];

                if (!archivo) {
                    Swal.fire('Error', 'Debes subir el comprobante', 'error');
                    return;
                }
                if (isNaN(montoForm) || montoForm <= 0) {
                    Swal.fire('Error', 'El monto debe ser mayor a 0', 'error');
                    return;
                }

                Swal.fire({
                    title: '¿Confirmar pago?',
                    html: `Monto a enviar: <b>$${montoForm.toFixed(2)}</b><br>Fecha: <b>${fechaForm}</b>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, enviar pago',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>

@stop
