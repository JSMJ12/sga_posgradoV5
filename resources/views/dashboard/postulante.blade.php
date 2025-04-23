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
                                        <td><span class="text-muted">Precio de Matriculación:</span> ${{ $postulante->maestria->matricula }}</td>
                                    </tr>
                                    @if ($postulante->maestria->cohortes)
                                    @php
                                        
                                        // Obtenemos la fecha actual
                                        $fecha_actual = now();
                                        
                                        // Filtramos los cohortes cuya fecha de inicio esté dentro de los próximos 5 días
                                        $cohortes_filtrados = $postulante->maestria->cohortes->filter(function ($cohorte) use ($fecha_actual) {
                                            return $cohorte->fecha_inicio >= $fecha_actual && $cohorte->fecha_inicio <= $fecha_actual->addDays(5);
                                        });
                                        
                                        // Si no hay cohortes dentro de los próximos 5 días
                                        if ($cohortes_filtrados->isEmpty()) {
                                            // Buscamos el siguiente cohorte más cercano en el tiempo que esté al menos a 10 días de distancia
                                            $cohortes_filtrados = $postulante->maestria->cohortes->sortBy('fecha_inicio')->filter(function ($cohorte) use ($fecha_actual) {
                                                return $cohorte->fecha_inicio > $fecha_actual->addDays(5);
                                            })->take(1);
                                        }
                                    @endphp
                                
                                        @foreach($cohortes_filtrados as $cohorte)
                                            <tr>
                                                <td><span class="text-muted">Inicio:</span> {{ $cohorte->fecha_inicio ?? 'N/A' }}</td>
                                                <td><span class="text-muted">Fin:</span> {{ $cohorte->fecha_fin ?? 'N/A' }}</td>
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
                        @if ($postulante->status == 1 && ($postulante->pdf_cedula !== null || $postulante->pdf_papelvotacion !== null || $postulante->pdf_titulouniversidad !== null || $postulante->pdf_hojavida !== null || ($postulante->discapacidad == 'Si' && $postulante->pdf_conadis !== null)) && $postulante->pago_matricula !== null)
                            <div class="alert alert-info text-center" role="alert">
                                Su solicitud está en proceso de revisión. Por favor, espere mientras verificamos sus archivos.
                            </div>
                        @endif
                        
                        <a href="{{ route('postulantes.carta_aceptacion', $postulante->dni) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Descargar el formato de la  Carta de Aceptación
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
                                                $docVerificado = $documentosVerificados->firstWhere('tipo_documento', $titulo);
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
                                                        <form action="{{ route('dashboard_postulante.store') }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="file" name="{{ $titulo }}" class="form-control-file mb-2" accept=".pdf">
                                                            <button type="submit" class="btn btn-primary btn-sm">Subir</button>
                                                        </form>
                                                    @else
                                                        <a href="{{ asset('storage/' . $archivo) }}" target="_blank" class="btn btn-info btn-sm">Abrir</a>
                                                        <!-- Botón para abrir el modal de edición -->
                                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal{{ str_replace(' ', '', $titulo) }}">Editar</button>
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
                                                    : ($documentosVerificados->firstWhere('tipo_documento', 'CONADIS') && $documentosVerificados->firstWhere('tipo_documento', 'CONADIS')->verificado
                                                        ? '<span class="text-success">Aprobado</span>'
                                                        : '<span class="text-warning">Subido</span>');
                                            @endphp
                                            <tr>
                                                <td>CONADIS</td>
                                                <td>{!! $estadoConadis !!}</td>
                                                <td>
                                                    @if (is_null($archivoConadis))
                                                        <form action="{{ route('dashboard_postulante.store') }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="file" name="CONADIS" class="form-control-file mb-2" accept=".pdf">
                                                            <button type="submit" class="btn btn-primary btn-sm">Subir</button>
                                                        </form>
                                                    @else
                                                        <a href="{{ asset('storage/' . $archivoConadis) }}" target="_blank" class="btn btn-info btn-sm">Abrir</a>
                                                        <!-- Botón para abrir el modal de edición -->
                                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModalCONADIS">Editar</button>
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
                                'CONADIS' => $postulante->pdf_conadis
                            ] as $titulo => $archivo)
                                <div class="modal fade" id="editModal{{ str_replace(' ', '', $titulo) }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ str_replace(' ', '', $titulo) }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ str_replace(' ', '', $titulo) }}">Editar: {{ $titulo }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Formulario de edición -->
                                                <form action="{{ route('dashboard_postulante.store') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="file" name="{{ $titulo }}" class="form-control-file mb-2" accept=".pdf">
                                                    <button type="submit" class="btn btn-primary btn-sm">Subir/Editar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        
                            @if ($postulante->status == 1 && $postulante->pago_matricula == null)
                                <p style="font-weight: bold; font-size: 1.2em; color: #333;">
                                    Al realizar el pago usted está de acuerdo con los términos y condiciones establecidos por la
                                    institución.
                                </p>
                                <p style="font-weight: bold; font-size: 1.2em; color: #cc0000;">
                                    Tenga en cuenta que cualquier intento de falsificar o modificar el comprobante de pago puede
                                    resultar en sanciones según las políticas de la institución.
                                </p>
                                <p style="font-weight: bold; font-size: 1.2em; color: #333;">
                                    Los reembolsos están sujetos a los términos y condiciones establecidos por la institución.
                                </p>
                                <div class="mt-4">
                                    <p>Estas son las cuentas oficiales para realizar los pagos. Toda transacción debe hacerse a
                                        las siguientes cuentas:</p>
                                    <div class="text-center">
                                        <img src="{{ asset('images/numero_cuenta.jpeg') }}" alt="Cuentas oficiales"
                                            style="max-width: 50%; height: auto;">
                                    </div>
                                </div>
                                <form action="{{ route('dashboard_postulante.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="comprobante_pago">Comprobante de Pago Matrícula:</label>
                                        <input type="file" id="comprobante_pago" name="pago_matricula" class="form-control-file"
                                            accept=".pdf">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Subir</button>
                                </form>
                            @endif
                        
                        </div>
                        
                        
                </div>
            </div>
        </div>
    </div>
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