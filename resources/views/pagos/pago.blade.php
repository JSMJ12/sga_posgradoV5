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
                        <p><strong>Deuda de Arancel:</strong>
                            @if ($alumno->monto_total < 0)
                                <span style="color: green;">Reembolso a recibir:
                                    ${{ number_format(abs($alumno->monto_total), 2) }}</span>
                            @else
                                ${{ number_format($alumno->monto_total, 2) }}
                            @endif
                        </p>

                        <p><strong>Deuda de Matrícula:</strong>
                            @if ($alumno->monto_matricula < 0)
                                <span style="color: green;">Reembolso a recibir:
                                    ${{ number_format(abs($alumno->monto_matricula), 2) }}</span>
                            @else
                                ${{ number_format($alumno->monto_matricula, 2) }}
                            @endif
                        </p>

                        <p><strong>Deuda de Inscripción:</strong>
                            @if ($alumno->monto_inscripcion < 0)
                                <span style="color: green;">Reembolso a recibir:
                                    ${{ number_format(abs($alumno->monto_inscripcion), 2) }}</span>
                            @else
                                ${{ number_format($alumno->monto_inscripcion, 2) }}
                            @endif
                        </p>


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
                                ${{ number_format($alumno->maestria->arancel - $programa['descuento'], 2) }}
                            </p>
                        @else
                            <p>Sin descuento aplicado</p>
                            <p>Total a Pagar de Arancel: ${{ number_format($alumno->maestria->arancel, 2) }}</p>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalidadPago = document.getElementById('modalidad_pago');
            const tipoPago = document.getElementById('tipo_pago');
            const montoInput = document.getElementById('monto');
            const fechaPago = document.getElementById('fecha_pago');
            const form = document.querySelector('form[action="{{ route('pagos.store') }}"]');

            const montoMatricula = parseFloat(@json($alumno->monto_matricula));
            const montoInscripcion = parseFloat(@json($alumno->monto_inscripcion));
            const montoTotalAlumno = parseFloat(@json($alumno->monto_total));
            const montoArancel = parseFloat(@json($alumno->maestria->arancel));
            const montoMatricula_t = parseFloat(@json($alumno->maestria->matricula));
            const montoInscripcion_t = parseFloat(@json($alumno->maestria->inscripcion));

            // Deshabilitar opciones si el monto es 0
            Array.from(tipoPago.options).forEach(opt => {
                if (opt.value === 'arancel' && montoTotalAlumno === 0) opt.disabled = true;
                if (opt.value === 'matricula' && montoMatricula === 0) opt.disabled = true;
                if (opt.value === 'inscripcion' && montoInscripcion === 0) opt.disabled = true;
            });

            function updatePaymentDetails() {
                const modalidad = modalidadPago.value;
                const tipo = tipoPago.value;
                let montoFinal = 0;

                if (tipo === 'arancel') {
                    if (modalidad === 'unico') {
                        montoFinal = montoTotalAlumno < 0 ? 0 : montoTotalAlumno;
                    } else if (modalidad === 'trimestral') {
                        montoFinal = montoArancel / 3;
                    }
                } else if (tipo === 'matricula') {
                    if (modalidad === 'unico') {
                        montoFinal = montoMatricula < 0 ? 0 : montoMatricula;
                    } else if (modalidad === 'trimestral') {
                        montoFinal = montoMatricula_t / 3;
                    }
                } else if (tipo === 'inscripcion') {
                    if (modalidad === 'unico') {
                        montoFinal = montoInscripcion < 0 ? 0 : montoInscripcion;
                    } else if (modalidad === 'trimestral') {
                        montoFinal = montoInscripcion_t / 3;
                    }
                }

                if (modalidad === 'otro') {
                    montoInput.value = '';
                    montoInput.readOnly = false;
                } else {
                    montoInput.value = montoFinal.toFixed(2);
                    montoInput.readOnly = true;
                }
            }

            modalidadPago.addEventListener('change', updatePaymentDetails);
            tipoPago.addEventListener('change', updatePaymentDetails);

            updatePaymentDetails();

            // Validación y alerta antes de enviar
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const montoForm = parseFloat(montoInput.value);
                const fechaForm = fechaPago.value;
                const archivo = document.getElementById('archivo_comprobante').files[0];

                if (!archivo) {
                    Swal.fire('Error', 'Debes subir el comprobante.', 'error');
                    return;
                }

                // Opcional: puedes extraer el monto y fecha del nombre del archivo si lo deseas
                // Ejemplo: comprobante_100.00_2025-08-20.pdf
                // let nombre = archivo.name.split('_');
                // let montoArchivo = parseFloat(nombre[1]);
                // let fechaArchivo = nombre[2]?.split('.')[0];

                Swal.fire({
                    title: '¿Confirmar pago?',
                    html: `Monto a enviar: <b>$${montoForm.toFixed(2)}</b><br>Fecha: <b>${fechaForm}</b><br>Verifica que el comprobante corresponda al monto y fecha seleccionados.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, enviar pago',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop
