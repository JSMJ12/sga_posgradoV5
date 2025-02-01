@extends('adminlte::page')

@section('title', 'Crear Alumno')

@section('content_header')
    <h1>Crear Alumno</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('alumnos.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Columna 1 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="maestria_id"><i class="fas fa-graduation-cap"></i> Maestría:</label>
                            <select class="form-control" id="maestria_id" name="maestria_id" required>
                                <option value="">Seleccione una maestría</option>
                                @foreach ($maestrias as $maestria)
                                    <option value="{{ $maestria->id }}">{{ $maestria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dni">Cédula / Pasaporte</label>
                            <input type="text" class="form-control" id="dni" name="dni" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre1"><i class="fas fa-user"></i> Primer Nombre:</label>
                            <input type="text" name="nombre1" id="nombre1" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre2"><i class="fas fa-user"></i> Segundo Nombre:</label>
                            <input type="text" name="nombre2" id="nombre2" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="apellidop"><i class="fas fa-user"></i> Apellido Paterno:</label>
                            <input type="text" name="apellidop" id="apellidop" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="apellidom"><i class="fas fa-user"></i> Apellido Materno:</label>
                            <input type="text" name="apellidom" id="apellidom" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email_per"><i class="fas fa-envelope"></i> Email Personal:</label>
                            <input type="email" name="email_per" id="email_per" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email_ins"><i class="fas fa-envelope"></i> Email Institucional:</label>
                            <input type="email" name="email_ins" id="email_ins" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="sexo"><i class="fas fa-venus-mars"></i> Sexo:</label>
                            <select name="sexo" id="sexo" class="form-control" required>
                                <option value="">Seleccione el sexo</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="estado_civil"><i class="fas fa-heart"></i> Estado Civil:</label>
                            <select class="form-control" id="estado_civil" name="estado_civil" required>
                                <option value="">Seleccione el estado civil</option>
                                <option value="Soltero/a">Soltero/a</option>
                                <option value="Casado/a">Casado/a</option>
                                <option value="Viudo/a">Viudo/a</option>
                                <option value="Divorciado/a">Divorciado/a</option>
                            </select>
                        </div>
                    </div>

                    <!-- Columna 2 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_nacimiento"><i class="fas fa-calendar-alt"></i> Fecha de nacimiento:</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>
                        <div class="form-group">
                            <label for="provincia"><i class="fas fa-map-marker-alt"></i> Provincia:</label>
                            <select name="provincia" id="provincia" class="form-control" required>
                                <option value="">Selecciona una provincia</option>
                                @foreach ($provincias as $provincia)
                                    <option value="{{ $provincia }}">{{ $provincia }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="canton"><i class="fas fa-building"></i> Ciudad / Cantón:</label>
                            <input type="text" class="form-control" id="canton" name="canton" required>
                        </div>
                        <div class="form-group">
                            <label for="barrio"><i class="fas fa-home"></i> Parroquia / Barrio:</label>
                            <input type="text" class="form-control" id="barrio" name="barrio" required>
                        </div>
                        <div class="form-group">
                            <label for="direccion"><i class="fas fa-address-card"></i> Dirección:</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="discapacidad"><i class="fas fa-wheelchair"></i> ¿Tiene discapacidad?</label>
                            <select class="form-control" id="discapacidad" name="discapacidad">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>

                        <div class="discapacidad-campos" style="display: none;">
                            <div class="form-group">
                                <label for="carnet_discapacidad"><i class="fas fa-id-card-alt"></i> Carnet de discapacidad:</label>
                                <input type="text" class="form-control" id="carnet_discapacidad" name="carnet_discapacidad">
                            </div>
                            <div class="form-group">
                                <label for="tipo_discapacidad"><i class="fas fa-wheelchair"></i> Tipo de Discapacidad:</label>
                                <select class="form-control" id="tipo_discapacidad" name="tipo_discapacidad">
                                    <option value="" disabled selected>Seleccione el tipo de discapacidad</option>
                                    <option value="Física">Física</option>
                                    <option value="Sensorial">Sensorial</option>
                                    <option value="Intelectual">Intelectual</option>
                                    <option value="Mental">Mental</option>
                                    <option value="Otra">Otra</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="porcentaje_discapacidad"><i class="fas fa-percent"></i> Porcentaje de discapacidad:</label>
                                <input type="number" step="0.1" class="form-control" id="porcentaje_discapacidad" name="porcentaje_discapacidad" value="{{ old('porcentaje_discapacidad') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nacionalidad"><i class="fas fa-flag"></i> Nacionalidad:</label>
                            <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" required>
                        </div>

                        <div class="form-group">
                            <label for="etnia"><i class="fas fa-users"></i> Etnia:</label>
                            <input type="text" class="form-control" id="etnia" name="etnia" required>
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
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                    <a href="{{ route('docentes.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
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
