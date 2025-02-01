@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-warning text-white text-center">
                        <h3>Perfil del Estudiante</h3>
                    </div>
                    <div class="card-body">
                        <div class="profile-picture text-center mb-3">
                            <img src="{{ asset('storage/' . $alumno->image) }}" alt="Foto de perfil"
                                class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <div class="profile-info">
                            <h4 class="text-center">{{ $alumno->nombre1 }} {{ $alumno->nombre2 }} {{ $alumno->apellidop }}
                                {{ $alumno->apellidom }}</h4>
                            <hr>
                            <p><strong>ID:</strong> {{ $alumno->dni }}</p>
                            <p><strong>Número de Estudiante:</strong> {{ $alumno->registro }}</p>
                            <p><strong>Email Institucional:</strong> {{ $alumno->email_institucional }}</p>
                            <p><strong>Email Personal:</strong> {{ $alumno->email_personal }}</p>
                            <p><strong>Título Profesional:</strong> {{ $alumno->titulo_profesional }}</p>
                            <button id="retirarse-btn" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#retiroModal">Retirarse</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h2>Asignaturas Matriculadas</h2>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @forelse ($asignaturas as $asignatura)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $asignatura->nombre }}</span>
                                    @if ($asignatura->silabo)
                                        <a href="{{ asset('storage/' . $asignatura->silabo) }}" target="_blank"
                                            class="btn btn-success btn-sm">Ver Sílabo</a>
                                    @endif
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">Aún no se asignan Docentes a las
                                    asignaturas.</li>
                            @endforelse
                        </ul>
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
                    <h5 class="modal-title" id="retiroModalLabel">Solicitud de Retiro</h5>
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
@stop

@section('js')
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
        .profile-picture img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .profile-info h4 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .card-header h2,
        .card-header h3 {
            margin: 0;
        }
    </style>
@stop
