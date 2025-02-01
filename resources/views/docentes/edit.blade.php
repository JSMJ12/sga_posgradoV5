@extends('adminlte::page')
@section('title', 'Editar Docente')
@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Docente</h1>
@stop
@section('content')
    <div class="card shadow-lg">
        <div class="card-body">
            <form method="POST" action="{{ route('docentes.update', $docente->dni) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Columna Izquierda -->
                    <div class="col-md-6">
                        <!-- Primer Nombre -->
                        <div class="form-group">
                            <label for="nombre"><i class="fas fa-user"></i> Primer Nombre:</label>
                            <input type="text" name="nombre1" id="nombre" class="form-control" value="{{ $docente->nombre1 }}" required>
                        </div>

                        <!-- Segundo Nombre -->
                        <div class="form-group">
                            <label for="nombre2"><i class="fas fa-user"></i> Segundo Nombre:</label>
                            <input type="text" name="nombre2" id="nombre2" class="form-control" value="{{ $docente->nombre2 }}">
                        </div>

                        <!-- Apellido Paterno -->
                        <div class="form-group">
                            <label for="apellidop"><i class="fas fa-user-tag"></i> Apellido Paterno:</label>
                            <input type="text" name="apellidop" id="apellidop" class="form-control" value="{{ $docente->apellidop }}" required>
                        </div>

                        <!-- Apellido Materno -->
                        <div class="form-group">
                            <label for="apellidom"><i class="fas fa-user-tag"></i> Apellido Materno:</label>
                            <input type="text" name="apellidom" id="apellidom" class="form-control" value="{{ $docente->apellidom }}">
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ $docente->email }}" required>
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div class="col-md-6">
                        <!-- Sexo -->
                        <div class="form-group">
                            <label for="sexo"><i class="fas fa-venus-mars"></i> Sexo:</label>
                            <select name="sexo" id="sexo" class="form-control" required>
                                <option value="M" {{ $docente->sexo == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ $docente->sexo == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>

                        <!-- DNI -->
                        <div class="form-group">
                            <label for="dni"><i class="fas fa-id-card"></i> Cédula / Pasaporte:</label>
                            <input type="text" name="dni" id="dni" class="form-control" value="{{ $docente->dni }}" required>
                        </div>

                        <!-- Tipo de Docente -->
                        <div class="form-group">
                            <label for="tipo"><i class="fas fa-chalkboard-teacher"></i> Tipo de Docente:</label>
                            <select name="tipo" id="tipo" class="form-control" required>
                                <option value="NOMBRADO" {{ $docente->tipo == 'NOMBRADO' ? 'selected' : '' }}>Nombrado</option>
                                <option value="CONTRATADO" {{ $docente->tipo == 'CONTRATADO' ? 'selected' : '' }}>Contratado</option>
                            </select>
                        </div>

                        <!-- Foto -->
                        <div class="form-group">
                            <label for="docen_foto"><i class="fas fa-camera"></i> Foto:</label>
                            <input type="file" name="docen_foto" id="docen_foto" class="form-control-file" accept="image/*">
                            <div id="preview-container" class="text-center mt-3">
                                <img id="preview-image" src="{{ asset('storage/' . $docente->image) }}" alt="Vista previa" 
                                    style="max-width: 60%; max-height: 100px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd;" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('docentes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.getElementById('docen_foto').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewImage = document.getElementById('preview-image');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };

                reader.readAsDataURL(file);
            }
        });
    </script>
@stop
