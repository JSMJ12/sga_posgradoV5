@extends('adminlte::page')

@section('title', 'Dashboard Alumno')

@section('content_header')
    <h1 class="text-success"><i class="fas fa-user-graduate"></i> Panel del Estudiante</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <!-- Perfil del Estudiante -->
        <div class="col-md-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header text-white text-center rounded-top-4" style="background: #218838">
                    <h3 class="mb-0"><i class="fas fa-id-badge me-2"></i>Perfil del Estudiante</h3>
                </div>
                <div class="card-body bg-light">
                    <div class="profile-picture text-center mb-4">
                        <img src="{{ asset('storage/' . $alumno->image) }}" alt="Foto de perfil"
                            class="img-thumbnail shadow-sm" style="width: 130px; height: 130px; object-fit: cover; border-radius: 50%;">
                    </div>
                    <div class="profile-info">
                        <h4 class="text-center fw-semibold text-dark mb-3">{{ $alumno->nombre1 }} {{ $alumno->nombre2 }} {{ $alumno->apellidop }} {{ $alumno->apellidom }}</h4>
                        <ul class="list-unstyled text-sm text-muted mb-4">
                            <li class="mb-2"><i class="fas fa-id-card me-2 text-secondary"></i> <strong class="text-dark">Cedula / Pasaporte:</strong> {{ $alumno->dni }}</li>
                            <li class="mb-2"><i class="fas fa-hashtag me-2 text-secondary"></i> <strong class="text-dark">N° Estudiante:</strong> {{ $alumno->registro }}</li>
                            <li class="mb-2"><i class="fas fa-envelope me-2 text-secondary"></i> <strong class="text-dark">Email Institucional:</strong><br><span class="ms-4">{{ $alumno->email_institucional }}</span></li>
                            <li class="mb-2"><i class="fas fa-envelope-open me-2 text-secondary"></i> <strong class="text-dark">Email Personal:</strong><br><span class="ms-4">{{ $alumno->email_personal }}</span></li>
                            <li><i class="fas fa-user-tie me-2 text-secondary"></i> <strong class="text-dark">Título Profesional:</strong><br><span class="ms-4">{{ $alumno->titulo_profesional }}</span></li>
                        </ul>

                        <a href="{{ route('certificado.descargar') }}" class="btn btn-outline-primary w-100 mb-2 rounded-pill">
                            <i class="fas fa-file-download me-1"></i> Descargar ficha socioeconómica
                        </a>
                        <button class="btn btn-outline-success w-100 mb-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#subirFichaModal">
                            <i class="fas fa-upload me-1"></i> Subir ficha socioeconómica
                        </button>
                        <button id="retirarse-btn" class="btn btn-outline-danger w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#retiroModal">
                            <i class="fas fa-sign-out-alt me-1"></i> Retirarse
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Asignaturas Matriculadas -->
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center">
                    <h2 class="mb-0"><i class="fas fa-book-open"></i> Asignaturas Matriculadas</h2>
                </div>
                <div class="card-body">
                    <div id="maestriasAccordion">
                        @forelse ($asignaturasPorMaestria as $maestriaId => $data)
                            <div class="card mb-2">
                                <div class="card-header" id="heading-{{ $maestriaId }}">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-left w-100" type="button" data-toggle="collapse" data-target="#collapse-{{ $maestriaId }}" aria-expanded="false" aria-controls="collapse-{{ $maestriaId }}"
                                            style="color: black; text-decoration: none; font-size: 20px; font-family: 'Arial', serif;">
                                            {{ $data['nombre'] }}
                                        </button>
                                    </h5>
                                </div>

                                <div id="collapse-{{ $maestriaId }}" class="collapse" aria-labelledby="heading-{{ $maestriaId }}" data-parent="#maestriasAccordion">
                                    <ul class="list-group list-group-flush">
                                        @forelse ($data['asignaturas'] as $asignatura)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="fas fa-book text-primary"></i> {{ $asignatura->nombre }}
                                                </span>
                                                @if ($asignatura->silabo)
                                                    <a href="{{ asset('storage/' . $asignatura->silabo) }}" target="_blank" class="btn btn-outline-success btn-sm">
                                                        <i class="fas fa-file-pdf"></i> Ver Sílabo
                                                    </a>
                                                @endif
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay asignaturas para esta maestría.
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        @empty
                            <li class="list-group-item text-center text-muted">
                                <i class="fas fa-info-circle"></i> Aún no se asignan Docentes a las asignaturas.
                            </li>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<!-- Modal para solicitud de retiro -->
<div class="modal fade" id="retiroModal" tabindex="-1" aria-labelledby="retiroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="retiroModalLabel"><i class="fas fa-sign-out-alt"></i> Solicitud de Retiro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="retiro-form" action="{{ route('alumno.retirarse', $alumno->dni) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="alumno_dni" value="{{ $alumno->dni }}">

                    <div class="mb-3">
                        <label for="retiro-file" class="form-label">Documento de Justificación</label>
                        <input type="file" id="retiro-file" name="retiro_documento" class="form-control" required>
                        <small class="text-muted">Por favor, cargue un documento que explique los motivos de su retiro.</small>
                    </div>
                    <div class="text-danger">
                        <small>* El documento de justificación es obligatorio para procesar la solicitud.</small>
                        <br>
                        <small>* Una vez aceptada la solicitud de retiro, no se reembolsará el importe pagado.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="retiro-form" class="btn btn-danger">Subir y confirmar retiro</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para subir ficha socioeconómica -->
<div class="modal fade" id="subirFichaModal" tabindex="-1" aria-labelledby="subirFichaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="subirFichaModalLabel"><i class="fas fa-upload"></i> Subir ficha socioeconómica</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="ficha-form" action="{{ route('alumnos.subirFicha', $alumno->dni) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="ficha_socioeconomica" class="form-label">Seleccione el archivo (PDF o DOCX):</label>
                    <input type="file" name="ficha_socioeconomica" id="ficha_socioeconomica" accept=".pdf,.doc,.docx" class="form-control mb-2" required>
                    <small class="text-muted">Solo se permiten archivos PDF o DOCX.</small>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="ficha-form" class="btn btn-success">Subir ficha</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<!-- FontAwesome para iconos -->
<script src="https://kit.fontawesome.com/4e4b8b9b3a.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    document.getElementById('retiro-form').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('retiro-file');
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Debes subir un documento para continuar.');
        }
    });
</script>
@stop

@section('css')
<style>
    body {
        background: #f8fafc;
    }
    .card {
        border-radius: 18px;
        overflow: hidden;
    }
    .card-header.bg-gradient-success {
        background: linear-gradient(90deg, #28a745 0%, #218838 100%);
    }
    .card-header.bg-gradient-primary {
        background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    }
    .profile-picture img {
        box-shadow: 0 4px 16px rgba(40,167,69,0.15);
        border: 4px solid #e9ecef;
    }
    .profile-info h4 {
        font-size: 22px;
        margin-bottom: 10px;
    }
    .list-group-item {
        background: #fff;
        border: none;
        border-bottom: 1px solid #e9ecef;
        font-size: 15px;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    .btn-primary, .btn-danger, .btn-outline-success {
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 8px;
    }
    .btn-primary {
        background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
        border: none;
    }
    .btn-danger {
        background: linear-gradient(90deg, #dc3545 0%, #b21f2d 100%);
        border: none;
    }
    .btn-outline-success {
        border: 2px solid #28a745;
        color: #28a745;
        background: #fff;
    }
    .btn-outline-success:hover {
        background: #28a745;
        color: #fff;
    }
    .modal-content {
        border-radius: 16px;
    }
    .modal-header.bg-danger {
        background: linear-gradient(90deg, #dc3545 0%, #b21f2d 100%);
    }
    .modal-title i {
        margin-right: 8px;
    }
    @media (max-width: 767px) {
        .profile-picture img {
            width: 100px !important;
            height: 100px !important;
        }
        .profile-info h4 {
            font-size: 18px;
        }
    }
</style>
@stop
