@extends('adminlte::page')
@section('title', 'Dashboard Postulante')
@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <img src="{{ asset('images/unesum.png') }}" alt="University Logo" class="logo">
                        <img src="{{ asset('images/posgrado-21.png') }}" alt="University Seal" class="seal">
                        <div class="university-info text-center d-flex align-items-center flex-column">
                            <span class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span>
                            <span class="institute">INSTITUTO DE POSGRADO</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="2">Maestría</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>{{ $postulante->maestria->nombre }}</strong></td>
                                        <td><span class="text-muted">Precio de Matriculación:</span>
                                            ${{ $postulante->maestria->matricula }}</td>
                                    </tr>
                                    @if ($postulante->maestria->cohortes)
                                        @php

                                            // Obtenemos la fecha actual
                                            $fecha_actual = now();

                                            // Filtramos los cohortes cuya fecha de inicio esté dentro de los próximos 5 días
                                            $cohortes_filtrados = $postulante->maestria->cohortes->filter(function (
                                                $cohorte,
                                            ) use ($fecha_actual) {
                                                return $cohorte->fecha_inicio >= $fecha_actual &&
                                                    $cohorte->fecha_inicio <= $fecha_actual->addDays(5);
                                            });

                                            // Si no hay cohortes dentro de los próximos 5 días
                                            if ($cohortes_filtrados->isEmpty()) {
                                                // Buscamos el siguiente cohorte más cercano en el tiempo que esté al menos a 10 días de distancia
                                                $cohortes_filtrados = $postulante->maestria->cohortes
                                                    ->sortBy('fecha_inicio')
                                                    ->filter(function ($cohorte) use ($fecha_actual) {
                                                        return $cohorte->fecha_inicio > $fecha_actual->addDays(5);
                                                    })
                                                    ->take(1);
                                            }
                                        @endphp

                                        @foreach ($cohortes_filtrados as $cohorte)
                                            <tr>
                                                <td><span class="text-muted">Inicio:</span>
                                                    {{ $cohorte->fecha_inicio ?? 'N/A' }}</td>
                                                <td><span class="text-muted">Fin:</span> {{ $cohorte->fecha_fin ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2">No hay cohortes asociados a esta maestría.</td>
                                        </tr>
                                    @endif
                                </tbody>

                            </table>
                        </div>
                        @if (
                            $postulante->status == 1 &&
                                ($postulante->pdf_cedula !== null ||
                                    $postulante->pdf_papelvotacion !== null ||
                                    $postulante->pdf_titulouniversidad !== null ||
                                    $postulante->pdf_hojavida !== null ||
                                    ($postulante->discapacidad == 'Si' && $postulante->pdf_conadis !== null)) &&
                                $postulante->pago_matricula !== null)
                            <div class="alert alert-info text-center" role="alert">
                                Su solicitud está en proceso de revisión. Por favor, espere mientras verificamos sus
                                archivos.
                            </div>
                        @endif

                        <a href="{{ route('postulantes.carta_aceptacion', $postulante->dni) }}"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Descargar el formato de la Carta de Aceptación
                        </a>
                        <div class="card-body">
                            <div class="form-group">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Documento</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $documentosVerificados = $postulante->documentos_verificados ?? collect(); // Asegurar que sea una colección
                                        @endphp

                                        @foreach ([
            'Cédula' => $postulante->pdf_cedula,
            'Papel de Votación' => $postulante->pdf_papelvotacion,
            'Título de Universidad' => $postulante->pdf_titulouniversidad,
            'Hoja de Vida' => $postulante->pdf_hojavida,
            'Carta de Aceptación' => $postulante->carta_aceptacion,
        ] as $titulo => $archivo)
                                            @php
                                                $docVerificado = $documentosVerificados->firstWhere(
                                                    'tipo_documento',
                                                    $titulo,
                                                );
                                                $estado = is_null($archivo)
                                                    ? '<span class="text-danger">Pendiente</span>'
                                                    : ($docVerificado && $docVerificado->verificado
                                                        ? '<span class="text-success">Aprobado</span>'
                                                        : '<span class="text-warning">Subido</span>');
                                            @endphp
                                            <tr>
                                                <!-- Nombre del documento -->
                                                <td>{{ $titulo }}</td>

                                                <!-- Estado del documento -->
                                                <td>{!! $estado !!}</td>

                                                <!-- Acción: Subir, Abrir o Editar -->
                                                <td>
                                                    @if (is_null($archivo))
                                                        <form action="{{ route('dashboard_postulante.store') }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="file" name="{{ $titulo }}"
                                                                class="form-control-file mb-2" accept=".pdf">
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm">Subir</button>
                                                        </form>
                                                    @else
                                                        <a href="{{ asset('storage/' . $archivo) }}" target="_blank"
                                                            class="btn btn-info btn-sm">Abrir</a>
                                                        <!-- Botón para abrir el modal de edición -->
                                                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                            data-target="#editModal{{ str_replace(' ', '', $titulo) }}">Editar</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach

                                        <!-- Incluir CONADIS solo si la discapacidad es 'Sí' -->
                                        @if ($postulante->discapacidad == 'Sí')
                                            @php
                                                $archivoConadis = $postulante->pdf_conadis;
                                                $estadoConadis = is_null($archivoConadis)
                                                    ? '<span class="text-danger">Pendiente</span>'
                                                    : ($documentosVerificados->firstWhere(
                                                        'tipo_documento',
                                                        'CONADIS',
                                                    ) &&
                                                    $documentosVerificados->firstWhere('tipo_documento', 'CONADIS')
                                                        ->verificado
                                                        ? '<span class="text-success">Aprobado</span>'
                                                        : '<span class="text-warning">Subido</span>');
                                            @endphp
                                            <tr>
                                                <td>CONADIS</td>
                                                <td>{!! $estadoConadis !!}</td>
                                                <td>
                                                    @if (is_null($archivoConadis))
                                                        <form action="{{ route('dashboard_postulante.store') }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="file" name="CONADIS"
                                                                class="form-control-file mb-2" accept=".pdf">
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm">Subir</button>
                                                        </form>
                                                    @else
                                                        <a href="{{ asset('storage/' . $archivoConadis) }}" target="_blank"
                                                            class="btn btn-info btn-sm">Abrir</a>
                                                        <!-- Botón para abrir el modal de edición -->
                                                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                            data-target="#editModalCONADIS">Editar</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>

                            <!-- Modal de edición para cada documento -->
                            @foreach ([
            'Cédula' => $postulante->pdf_cedula,
            'Papel de Votación' => $postulante->pdf_papelvotacion,
            'Título de Universidad' => $postulante->pdf_titulouniversidad,
            'Hoja de Vida' => $postulante->pdf_hojavida,
            'Carta de Aceptación' => $postulante->carta_aceptacion,
            'CONADIS' => $postulante->pdf_conadis,
        ] as $titulo => $archivo)
                                <div class="modal fade" id="editModal{{ str_replace(' ', '', $titulo) }}" tabindex="-1"
                                    role="dialog" aria-labelledby="editModalLabel{{ str_replace(' ', '', $titulo) }}"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="editModalLabel{{ str_replace(' ', '', $titulo) }}">Editar:
                                                    {{ $titulo }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Formulario de edición -->
                                                <form action="{{ route('dashboard_postulante.store') }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="file" name="{{ $titulo }}"
                                                        class="form-control-file mb-2" accept=".pdf">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-sm">Subir/Editar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @php
                                $tienePagoMatricula = \App\Models\Pago::where('user_id', auth()->id())->exists();
                                $maestria = $postulante->maestria; // Obtener la maestría asociada al postulante
                                $inscripcion = $maestria ? $maestria->inscripcion : null; // Obtener el valor de inscripción (si existe)
                                $matricula = $maestria ? $maestria->matricula : null;
                            @endphp

                            @if ($postulante->status == 1 && !$tienePagoMatricula)

                                <p style="font-weight: bold; font-size: 1.2em; color: #333;">
                                    @if ($inscripcion && $inscripcion > 0)
                                        Primero debe realizar el pago de inscripción antes de proceder con el pago de
                                        matrícula.
                                    @else
                                        Puede proceder con el pago de matrícula.
                                    @endif
                                </p>

                                <div class="mt-4">
                                    <p>Estas son las cuentas oficiales para realizar el pago. Toda transacción debe hacerse
                                        a las siguientes cuentas:</p>
                                    <div class="text-center">
                                        <img src="{{ asset('images/numero_cuenta.jpeg') }}" alt="Cuentas oficiales"
                                            style="max-width: 50%; height: auto;">
                                    </div>
                                </div>

                                <div class="container mt-4">
                                    <div class="card shadow-lg">
                                        <div
                                            class="card-header text-center bg-success text-white d-flex justify-content-between align-items-center">
                                            <h5 class="m-0">Formulario de Pago de
                                                {{ $inscripcion && $inscripcion > 0 ? 'Inscripción' : 'Matrícula' }}
                                            </h5>
                                            <i class="fas fa-credit-card fa-2x"></i> <!-- Icono de tarjeta de crédito -->
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('pagos.store') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <!-- Campo oculto para tipo de pago -->
                                                <input type="hidden" name="tipo_pago"
                                                    value="{{ $inscripcion && $inscripcion > 0 ? 'inscripcion' : 'matricula' }}">

                                                <!-- Modalidad de pago -->
                                                <div class="form-group mb-3">
                                                    <label for="modalidad_pago" class="font-weight-bold">Modalidad de
                                                        Pago:</label>
                                                    <select id="modalidad_pago" name="modalidad_pago"
                                                        class="form-control @error('modalidad_pago') is-invalid @enderror"
                                                        required>
                                                        <option value="unico">Pago Único</option>
                                                        <option value="otro">Otro</option>
                                                    </select>
                                                    @error('modalidad_pago')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Monto de pago -->
                                                <div class="form-group mb-3" id="monto_group">
                                                    <label for="monto" class="font-weight-bold">
                                                        <i class="fas fa-money-bill-wave"></i> <!-- Icono de dinero -->
                                                        Monto de
                                                        {{ $inscripcion && $inscripcion > 0 ? 'Inscripción' : 'Matrícula' }}:
                                                    </label>
                                                    <div class="alert alert-info">
                                                        <strong>${{ $inscripcion && $inscripcion > 0 ? $inscripcion : $matricula }}</strong>
                                                        <!-- Muestra el monto de Inscripción o Matrícula de forma destacada -->
                                                    </div>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                            <!-- Símbolo de dólar -->
                                                        </div>
                                                        <input type="number" id="monto" name="monto"
                                                            class="form-control @error('monto') is-invalid @enderror"
                                                            value="{{ $inscripcion && $inscripcion > 0 ? $inscripcion : $matricula }}"
                                                            readonly>
                                                    </div>
                                                    @error('monto')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Fecha de pago -->
                                                <div class="form-group mb-3">
                                                    <label for="fecha_pago" class="font-weight-bold">
                                                        <i class="fas fa-calendar-day"></i> <!-- Icono de fecha -->
                                                        Fecha de Pago:
                                                    </label>
                                                    <input type="date" id="fecha_pago" name="fecha_pago"
                                                        class="form-control @error('fecha_pago') is-invalid @enderror"
                                                        required>
                                                    @error('fecha_pago')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Comprobante de pago -->
                                                <div class="form-group mb-3">
                                                    <label for="archivo_comprobante" class="font-weight-bold">
                                                        <i class="fas fa-file-upload"></i> <!-- Icono de archivo -->
                                                        Comprobante de Pago:
                                                    </label>
                                                    <input type="file" id="archivo_comprobante"
                                                        name="archivo_comprobante"
                                                        class="form-control-file @error('archivo_comprobante') is-invalid @enderror"
                                                        accept=".pdf,.jpg,.jpeg,.png" required>
                                                    @error('archivo_comprobante')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-upload"></i> Subir Comprobante de
                                                        {{ $inscripcion && $inscripcion > 0 ? 'Inscripción' : 'Matrícula' }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @stop
    @section('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalidadPagoSelect = document.getElementById('modalidad_pago');
                const montoInput = document.getElementById('monto');
                const montoGroup = document.getElementById('monto_group');

                // Verificar si los elementos existen antes de agregar el evento
                if (modalidadPagoSelect && montoInput && montoGroup) {
                    // Habilitar o deshabilitar el campo monto según la modalidad de pago
                    modalidadPagoSelect.addEventListener('change', function() {
                        if (modalidadPagoSelect.value === 'otro') {
                            // Si elige "Otro", habilitar el campo para que el usuario ingrese un monto personalizado
                            montoInput.readOnly = false;
                            montoInput.value =
                                ''; // Limpiar el valor para permitir la entrada de un nuevo monto
                        } else {
                            // Si elige "Unico" o "Trimestral", deshabilitar el campo y mostrar el monto fijo
                            montoInput.readOnly = true;
                            // Aquí, debes asegurarte de que el monto esté correctamente actualizado según la modalidad seleccionada.
                            if (modalidadPagoSelect.value === 'unico') {
                                // Ajustar el monto para el pago único
                                montoInput.value =
                                    '{{ $inscripcion && $inscripcion > 0 ? $inscripcion : $matricula }}';
                            } else if (modalidadPagoSelect.value === 'trimestral') {
                                // Ajustar el monto para el pago trimestral si es necesario
                                montoInput.value =
                                    '{{ $inscripcion && $inscripcion > 0 ? $inscripcion : $matricula }}'; // Ajustar si es necesario
                            }
                        }
                    });

                    // Inicializar el estado del campo según el valor seleccionado en el combo (por si ya está seleccionado antes de cargar)
                    if (modalidadPagoSelect.value === 'otro') {
                        montoInput.readOnly = false;
                        montoInput.value = ''; // Limpiar si es "otro"
                    } else {
                        montoInput.readOnly = true;
                        montoInput.value = '{{ $inscripcion && $inscripcion > 0 ? $inscripcion : $matricula }}';
                    }
                }
            });
        </script>

    @stop

    @section('css')
        <style>
            .header {
                text-align: center;
                margin-top: 10px;
            }

            .logo {
                width: 74px;
                height: 80px;
                position: absolute;
                top: 10px;
                left: 10px;
            }

            .seal {
                width: 100px;
                height: 103px;
                position: absolute;
                top: 10px;
                right: 10px;
            }

            .university-name {
                font-size: 14pt;
                font-weight: bold;
            }

            .institute {
                font-size: 10pt;
            }

            .divider {
                width: 100%;
                height: 2px;
                background-color: #000;
                margin: 10px 0;
            }

            .custom-select-wrapper {
                position: relative;
                display: inline-block;
                width: 100%;
            }

            .card-header {
                height: 120px;
                padding: 20px;
            }
        </style>
    @stop
