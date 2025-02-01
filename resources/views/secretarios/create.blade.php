@extends('adminlte::page')
@section('title', 'Crear Secretario')
@section('content_header')
    <h1><i class="fas fa-user-plus"></i> Crear Secretario</h1>
@stop
@section('content')
    <div class="card shadow-lg">
        <div class="card-body">
            <form method="POST" action="{{ route('secretarios.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Primera columna -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre"><i class="fas fa-user"></i> Primer Nombre:</label>
                            <input type="text" name="nombre1" id="nombre" class="form-control" placeholder="Ingrese el primer nombre">
                        </div>
                        <div class="form-group">
                            <label for="nombre2"><i class="fas fa-user"></i> Segundo Nombre:</label>
                            <input type="text" name="nombre2" id="nombre2" class="form-control" placeholder="Ingrese el segundo nombre">
                        </div>
                        <div class="form-group">
                            <label for="apellidop"><i class="fas fa-user-tag"></i> Apellido Paterno:</label>
                            <input type="text" name="apellidop" id="apellidop" class="form-control" placeholder="Ingrese el apellido paterno">
                        </div>
                        <div class="form-group">
                            <label for="apellidom"><i class="fas fa-user-tag"></i> Apellido Materno:</label>
                            <input type="text" name="apellidom" id="apellidom" class="form-control" placeholder="Ingrese el apellido materno">
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese el correo electrónico">
                        </div>
                    </div>

                    <!-- Segunda columna -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sexo"><i class="fas fa-venus-mars"></i> Sexo:</label>
                            <select name="sexo" id="sexo" class="form-control">
                                <option value="">Seleccione el sexo</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dni"><i class="fas fa-id-card"></i> Cédula / Pasaporte:</label>
                            <input type="text" class="form-control" id="dni" name="dni" placeholder="Ingrese la cédula o pasaporte" required>
                        </div>
                        <div class="form-group">
                            <label for="seccion_id"><i class="fas fa-layer-group"></i> Sección:</label>
                            <select name="seccion_id" id="seccion_id" class="form-control">
                                <option value="">Seleccione una sección</option>
                                @foreach($secciones as $seccion)
                                    <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="image"><i class="fas fa-camera"></i> Foto:</label>
                            <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                            <div id="preview-container" class="mt-3 text-center">
                                <img id="preview-image" src="#" alt="Vista previa" 
                                     style="display: none; max-width: 150px; max-height: 150px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('secretarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    document.getElementById('image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewImage = document.getElementById('preview-image');

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
            };

            reader.readAsDataURL(file);
        } else {
            previewImage.src = '#';
            previewImage.style.display = 'none';
        }
    });
</script>
@stop
