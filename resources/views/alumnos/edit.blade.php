@extends('adminlte::page')

@section('title', 'Editar Alumno')

@section('content_header')
    <h1>Editar Alumno</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('alumnos.update', $alumno->dni) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Columna 1 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="maestria_id"><i class="fas fa-graduation-cap"></i> Maestría:</label>
                            <select class="form-control" id="maestria_id" name="maestria_id" required>
                                <option value="">Seleccione una maestría</option>
                                @foreach ($maestrias as $maestria)
                                    <option value="{{ $maestria->id }}" {{ $alumno->maestria_id == $maestria->id ? 'selected' : '' }}>
                                        {{ $maestria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dni">Cédula / Pasaporte</label>
                            <input type="text" class="form-control" id="dni" name="dni" value="{{ $alumno->dni }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nombre1"><i class="fas fa-user"></i> Primer Nombre:</label>
                            <input type="text" name="nombre1" id="nombre1" class="form-control" value="{{ $alumno->nombre1 }}" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre2"><i class="fas fa-user"></i> Segundo Nombre:</label>
                            <input type="text" name="nombre2" id="nombre2" class="form-control" value="{{ $alumno->nombre2 }}">
                        </div>
                        <div class="form-group">
                            <label for="apellidop"><i class="fas fa-user"></i> Apellido Paterno:</label>
                            <input type="text" name="apellidop" id="apellidop" class="form-control" value="{{ $alumno->apellidop }}" required>
                        </div>
                        <div class="form-group">
                            <label for="apellidom"><i class="fas fa-user"></i> Apellido Materno:</label>
                            <input type="text" name="apellidom" id="apellidom" class="form-control" value="{{ $alumno->apellidom }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email_personal"><i class="fas fa-envelope"></i> Email Personal:</label>
                            <input type="email" name="email_personal" id="email_personal" class="form-control" value="{{ $alumno->email_personal }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email_ins"><i class="fas fa-envelope"></i> Email Institucional:</label>
                            <input type="email" name="email_ins" id="email_ins" class="form-control" value="{{ $alumno->email_institucional }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="sexo"><i class="fas fa-venus-mars"></i> Sexo:</label>
                            <select name="sexo" id="sexo" class="form-control" required>
                                <option value="M" {{ $alumno->sexo == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ $alumno->sexo == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="estado_civil"><i class="fas fa-heart"></i> Estado Civil:</label>
                            <select class="form-control" id="estado_civil" name="estado_civil" required>
                                <option value="Soltero/a" {{ $alumno->estado_civil == 'Soltero/a' ? 'selected' : '' }}>Soltero/a</option>
                                <option value="Casado/a" {{ $alumno->estado_civil == 'Casado/a' ? 'selected' : '' }}>Casado/a</option>
                                <option value="Viudo/a" {{ $alumno->estado_civil == 'Viudo/a' ? 'selected' : '' }}>Viudo/a</option>
                                <option value="Divorciado/a" {{ $alumno->estado_civil == 'Divorciado/a' ? 'selected' : '' }}>Divorciado/a</option>
                            </select>
                        </div>
                    </div>

                    <!-- Columna 2 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_nacimiento"><i class="fas fa-calendar-alt"></i> Fecha de nacimiento:</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ $alumno->fecha_nacimiento }}" required>
                        </div>
                        <div class="form-group">
                            <label for="provincia"><i class="fas fa-map-marker-alt"></i> Provincia:</label>
                            <select name="provincia" id="provincia" class="form-control" required>
                                @foreach ($provincias as $provincia)
                                    <option value="{{ $provincia }}" {{ $alumno->provincia == $provincia ? 'selected' : '' }}>{{ $provincia }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="canton"><i class="fas fa-building"></i> Ciudad / Cantón:</label>
                            <input type="text" class="form-control" id="canton" name="canton" value="{{ $alumno->canton }}" required>
                        </div>
                        <div class="form-group">
                            <label for="barrio"><i class="fas fa-home"></i> Parroquia / Barrio:</label>
                            <input type="text" class="form-control" id="barrio" name="barrio" value="{{ $alumno->barrio }}" required>
                        </div>
                        <div class="form-group">
                            <label for="direccion"><i class="fas fa-address-card"></i> Dirección:</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="3" required>{{ $alumno->direccion }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="discapacidad"><i class="fas fa-wheelchair"></i> ¿Tiene discapacidad?</label>
                            <select class="form-control" id="discapacidad" name="discapacidad">
                                <option value="0" {{ $alumno->discapacidad == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $alumno->discapacidad == 1 ? 'selected' : '' }}>Sí</option>
                            </select>
                        </div>

                        <div class="discapacidad-campos" style="{{ $alumno->discapacidad ? 'display: block;' : 'display: none;' }}">
                            <div class="form-group">
                                <label for="carnet_discapacidad"><i class="fas fa-id-card-alt"></i> Carnet de discapacidad:</label>
                                <input type="text" class="form-control" id="carnet_discapacidad" name="carnet_discapacidad" value="{{ $alumno->carnet_discapacidad }}">
                            </div>
                            <div class="form-group">
                                <label for="tipo_discapacidad"><i class="fas fa-wheelchair"></i> Tipo de Discapacidad:</label>
                                <select class="form-control" id="tipo_discapacidad" name="tipo_discapacidad">
                                    <option value="Física" {{ $alumno->tipo_discapacidad == 'Física' ? 'selected' : '' }}>Física</option>
                                    <option value="Sensorial" {{ $alumno->tipo_discapacidad == 'Sensorial' ? 'selected' : '' }}>Sensorial</option>
                                    <option value="Intelectual" {{ $alumno->tipo_discapacidad == 'Intelectual' ? 'selected' : '' }}>Intelectual</option>
                                    <option value="Mental" {{ $alumno->tipo_discapacidad == 'Mental' ? 'selected' : '' }}>Mental</option>
                                    <option value="Otra" {{ $alumno->tipo_discapacidad == 'Otra' ? 'selected' : '' }}>Otra</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="porcentaje_discapacidad"><i class="fas fa-percent"></i> Porcentaje:</label>
                                <input type="number" class="form-control" id="porcentaje_discapacidad" name="porcentaje_discapacidad" value="{{ $alumno->porcentaje_discapacidad }}">
                            </div>
                            
                        </div>
                        <div class="form-group">
                            <label for="nacionalidad"><i class="fas fa-flag"></i> Nacionalidad:</label>
                            <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" value="{{ $alumno->nacionalidad }}" required>
                        </div>

                        <div class="form-group">
                            <label for="etnia"><i class="fas fa-users"></i> Etnia:</label>
                            <input type="text" class="form-control" id="etnia" name="etnia" value="{{ $alumno->etnia }}" required>
                        </div>

                        <div class="form-group">
                            <label for="image"><i class="fas fa-camera"></i> Foto:</label>
                            <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                            <div id="preview-container" style="margin-top: 10px;">
                                <img id="preview-image" src="#" alt="Vista previa" style="display: none; max-width: 150px; max-height: 150px; object-fit: cover;" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

@stop

@section('js')
    <script>
        document.getElementById('discapacidad').addEventListener('change', function() {
            var campos = document.querySelector('.discapacidad-campos');
            campos.style.display = this.value == '1' ? 'block' : 'none';
        });

        document.getElementById('image').addEventListener('change', function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('preview-image');
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        });
    </script>
@stop