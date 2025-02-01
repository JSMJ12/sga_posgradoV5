@extends('adminlte::page')

@section('title', 'Proceso de Titulación')

@section('content_header')
    <h1 class="text-center">Proceso Completo de Titulación</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white text-center">
                <h4><i class="fas fa-graduation-cap"></i> Solicitud de Aprobación de Tema y Tutorías</h4>
            </div>
            <div class="card-body">
                <div id="formulario-proceso">
                    @if ($tesis == null)
                        <form action="{{ route('tesis.store') }}" method="POST" enctype="multipart/form-data"
                            id="proceso-form">
                            @csrf

                            <div class="mb-4">
                                <label for="tipo" class="form-label fw-bold ">Seleccione el tipo de proceso</label>
                                <div class="input-group">
                                    <select class="form-select shadow-sm" id="tipo" name="tipo" required>
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="trabajo de titulación">Trabajo de Titulación</option>
                                        <option value="artículo científico">Artículo Científico</option>
                                        <option value="examen complexivo">Examen Complexivo</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Paso 1: Información del Tema -->
                            <div class="form-step" id="step-1">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-body">
                                        <h5 class="text-secondary">Paso 1: Información del tema</h5>
                                        <div class="mb-3">
                                            <label for="tema" class="form-label">Tema</label>
                                            <input type="text" class="form-control" id="tema" name="tema"
                                                placeholder="Ingrese el tema" value="{{ old('tema') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                                placeholder="Describa brevemente el tema" required>{{ old('descripcion') }}</textarea>
                                        </div>
                                        <div class="mb-3 text-center">
                                            <a href="{{ route('tesis.downloadPDF') }}" class="btn btn-danger">
                                                <i class="fas fa-download"></i> Descargar Formato de Solicitud
                                            </a>
                                        </div>
                                        <button type="button" class="btn btn-warning next-step w-100">
                                            Siguiente <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Paso 2: Carga de PDF -->
                            <div class="form-step d-none" id="step-2">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="text-secondary">Paso 2: Carga de la solicitud en PDF</h5>
                                        <div class="mb-3">
                                            <label for="solicitud_pdf" class="form-label">Archivo PDF de Solicitud</label>
                                            <input type="file" class="form-control" id="solicitud_pdf"
                                                name="solicitud_pdf" accept="application/pdf" required>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-secondary previous-step">
                                                <i class="fas fa-arrow-left"></i> Anterior
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                Enviar Solicitud <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        @if ($tesis && $tesis->tipo === 'examen complexivo')
                            <div class="alert alert-info text-center">
                                @if ($alumno->examenComplexivo)
                                    <div class="alert alert-info text-center">
                                        <h5><i class="fas fa-info-circle"></i> Examen Complexivo Asignado</h5>
                                        <p><strong>Lugar:</strong> {{ $alumno->examenComplexivo->lugar }}<br>
                                            <strong>Fecha:</strong>
                                            {{ \Carbon\Carbon::parse($alumno->examenComplexivo->fecha_hora)->format('d/m/Y') }}<br>
                                            <strong>Hora:</strong>
                                            {{ \Carbon\Carbon::parse($alumno->examenComplexivo->fecha_hora)->format('h:i A') }}
                                        </p>
                                    </div>
                                @else
                                    <h5><i class="fas fa-info-circle"></i> Has elegido Examen Complexivo</h5>
                                    <p>Se le notificará oportunamente sobre la fecha, lugar y hora del examen.</p>
                                @endif
                            </div>
                        @endif
                        @if ($tesis && $tesis->estado === 'aprobado')
                            <div class="alert alert-success text-center">
                                <h5><i class="fas fa-check-circle"></i> Tu tema ha sido aprobado.</h5>
                                <p>Continúa con las tutorías asignadas.</p>
                            </div>
                        @endif
                        @if ($tesis && $tesis->estado === 'pendiente' && ($tesis->tipo === 'trabajo de titulación' || $tesis->tipo === 'artículo científico'))
                            <div class="alert alert-warning text-center">
                                <h5><i class="fas fa-exclamation-circle"></i> Tu tema está en revisión.</h5>
                                <p>Se te notificará si es aprobada o rechazada.</p>
                            </div>
                        @endif
                    @endif
                    <!-- Tabla de Tutorías -->
                    @if ($tesis && $tesis->tutorias && count($tesis->tutorias) > 0)
                        <div class="mt-4">
                            <!-- Información del Tutor -->
                            @if ($tesis->tutor)
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Información del Tutor</h5>
                                    </div>
                                    <div class="card-body">
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <!-- Imagen del Tutor -->
                                            <div class="me-5"> <!-- Cambiado a me-5 para mayor separación -->
                                                <img src="{{ asset('storage/' . $tesis->tutor->image) }}"
                                                    alt="Foto del Tutor"
                                                    class="rounded-circle border border-primary shadow-sm" width="80"
                                                    height="80">
                                            </div>

                                            <!-- Información del Tutor -->
                                            <div>
                                                <h5 class="fw-bold mb-1">
                                                    {{ $tesis->tutor->nombre1 }} {{ $tesis->tutor->nombre2 }}
                                                    {{ $tesis->tutor->apellidop }} {{ $tesis->tutor->apellidom }}
                                                </h5>
                                                <p class="mb-1 text-muted"><i class="fas fa-envelope"></i>
                                                    {{ $tesis->tutor->email }}</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle"></i> No se encontró información del tutor.
                                </div>
                            @endif

                        </div>

                        <!-- Tabla de Tutorías -->
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Tipo</th>
                                        <th>Ubicación/Link</th>
                                        <th>Estado</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tesis->tutorias as $tutoria)
                                        <tr>
                                            <td>{{ Carbon\Carbon::parse($tutoria->fecha)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $tutoria->tipo == 'presencial' ? 'bg-success' : 'bg-primary' }}">
                                                    {{ ucfirst($tutoria->tipo) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($tutoria->tipo == 'virtual')
                                                    <a href="{{ $tutoria->link_reunion }}" target="_blank">
                                                        <i class="fas fa-link"></i> {{ $tutoria->link_reunion }}
                                                    </a>
                                                @else
                                                    <i class="fas fa-map-marker-alt"></i> {{ $tutoria->lugar }}
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $tutoria->estado == 'realizada' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ ucfirst($tutoria->estado) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $tutoria->observaciones ?? 'Sin observaciones' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif


                </div>
                <div id="mensaje-examen" class="d-none">
                    <div class="alert alert-info text-center">
                        <h5><i class="fas fa-info-circle"></i> Examen Complexivo</h5>
                        <p>Se le notificará oportunamente sobre la fecha, lugar y hora del examen complexivo.</p>
                        <p><strong>Nota:</strong> Una vez que confirme esta opción, no podrá modificar su elección.</p>
                        <form action="{{ route('tesis.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="tipo" value="examen complexivo">
                            <button type="submit" class="btn btn-success">
                                Aceptar <i class="fas fa-check"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo');
            const formularioProceso = document.getElementById('formulario-proceso');
            const mensajeExamen = document.getElementById('mensaje-examen');
            const nextStepButtons = document.querySelectorAll('.next-step');
            const previousStepButtons = document.querySelectorAll('.previous-step');
            const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');

            tipoSelect.addEventListener('change', function() {
                const selectedValue = this.value;

                if (selectedValue === 'trabajo de titulación' || selectedValue === 'artículo científico') {
                    formularioProceso.classList.remove('d-none');
                    mensajeExamen.classList.add('d-none');
                } else if (selectedValue === 'examen complexivo') {
                    formularioProceso.classList.add('d-none');
                    mensajeExamen.classList.remove('d-none');
                }
            });

            nextStepButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Validar campos del paso 1 antes de avanzar
                    const tema = document.getElementById('tema').value.trim();
                    const descripcion = document.getElementById('descripcion').value.trim();

                    if (tipoSelect.value !== 'examen complexivo') {
                        if (!tema || !descripcion) {
                            alert('Por favor, complete todos los campos antes de continuar.');
                            return;
                        }
                    }

                    // Mostrar el siguiente paso
                    step1.classList.add('d-none');
                    step2.classList.remove('d-none');
                });
            });

            previousStepButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Volver al paso anterior
                    step2.classList.add('d-none');
                    step1.classList.remove('d-none');
                });
            });
        });
    </script>


@stop
