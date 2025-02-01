@extends('adminlte::page')
@section('title', 'Datos Personales')

@section('content_header')
    <h1>Actualizar Datos Personales</h1>
@stop

@section('content')
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('update_datosAlumnos') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-center mb-4">
                                        <img src="{{ asset('storage/' . $alumno->image) }}" alt="Imagen del Alumno" id="currentImage" class="img-fluid shadow" style="max-width: 150px;">
                                        <br>
                                        <label for="image" class="mt-2">Cambiar foto:</label>
                                        <input type="file" id="imageInput" name="image" accept="image/*" class="form-control">
                                        <img id="previewImage" src="#" alt="Previsualización de la Imagen" class="img-fluid shadow mt-2" style="display: none; max-width: 150px;">
                                    </div>

                                    <div class="form-group">
                                        <label for="correo_electronico"><i class="fas fa-envelope"></i> Correo Electrónico Personal:</label>
                                        <input type="email" name="correo_electronico" class="form-control" placeholder="correo@example.com" value="{{ old('correo_electronico', $alumno->email_personal) }}" required>
                                        @error('correo_electronico')
                                            <div class="alert alert-danger">
                                                <strong>Error:</strong> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="celular"><i class="fas fa-phone"></i> Celular:</label>
                                        <input type="text" name="celular" class="form-control" placeholder="Número de celular" value="{{ old('celular', $alumno->celular) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="estado_civil"><i class="fas fa-heart"></i> Estado Civil:</label>
                                        <select name="estado_civil" id="estado_civil" class="form-control">
                                            <option value="">Selecciona tu Estado Civil</option>
                                            @foreach ($estadosCiviles as $estadoCivil)
                                                <option value="{{ $estadoCivil }}" {{ old('estado_civil', $alumno->estado_civil) == $estadoCivil ? 'selected' : '' }}>
                                                    {{ $estadoCivil }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="sexo"><i class="fas fa-venus-mars"></i> Sexo:</label>
                                        <select name="sexo" class="form-control" required>
                                            <option value="HOMBRE" {{ old('sexo', $alumno->sexo) == 'HOMBRE' ? 'selected' : '' }}>Hombre</option>
                                            <option value="MUJER" {{ old('sexo', $alumno->sexo) == 'MUJER' ? 'selected' : '' }}>Mujer</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="fecha_nacimiento"><i class="fas fa-calendar-alt"></i> Fecha de Nacimiento:</label>
                                        <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $alumno->fecha_nacimiento) }}" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="nacionalidad"><i class="fas fa-flag"></i> Nacionalidad:</label>
                                        <input type="text" name="nacionalidad" class="form-control" placeholder="Nacionalidad" value="{{ old('nacionalidad', $alumno->nacionalidad) }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discapacidad"><i class="fas fa-wheelchair"></i> Posee alguna Discapacidad:</label>
                                        <select name="discapacidad" class="form-control" required>
                                            <option value="No">No</option>
                                            <option value="Si">Sí</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="divPorcentajeDiscapacidad" style="display: none;">
                                        <label for="porcentaje_discapacidad"><i class="fas fa-percentage"></i> Porcentaje de discapacidad (en caso de no tener, ingresar 0):</label>
                                        <input type="number" name="porcentaje_discapacidad" class="form-control" min="0" max="100">
                                    </div>

                                    <div class="form-group" id="divCodigoConadis" style="display: none;">
                                        <label for="codigo_conadis"><i class="fas fa-id-card"></i> Código CONADIS:</label>
                                        <input type="text" name="codigo_conadis" class="form-control" placeholder="Código CONADIS">
                                    </div>

                                    <div class="form-group">
                                        <label for="provincia"><i class="fas fa-map-marked-alt"></i> Provincia:</label>
                                        <select name="provincia" id="provincia" class="form-control">
                                            <option value="">Selecciona una provincia</option>
                                            @foreach ($provincias as $provincia)
                                                <option value="{{ $provincia }}" {{ old('provincia', $alumno->provincia) == $provincia ? 'selected' : '' }}>
                                                    {{ $provincia }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="canton"><i class="fas fa-city"></i> Cantón:</label>
                                        <input type="text" name="canton" class="form-control" placeholder="Cantón" value="{{ old('canton', $alumno->canton) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="direccion"><i class="fas fa-home"></i> Dirección:</label>
                                        <input type="text" name="direccion" class="form-control" placeholder="Dirección" value="{{ old('direccion', $alumno->direccion) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="etnia"><i class="fas fa-users"></i> Etnia:</label>
                                        <input type="text" name="etnia" class="form-control" placeholder="Etnia" value="{{ old('etnia', $alumno->etnia) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="tipo_colegio"><i class="fas fa-school"></i> Tipo de Colegio:</label>
                                        <select name="tipo_colegio" id="tipo_colegio" class="form-control">
                                            <option value="">Selecciona el Tipo de Colegio</option>
                                            @foreach ($tipo_colegio as $tp)
                                                <option value="{{ $tp }}" {{ old('tipo_colegio', $alumno->tipo_colegio) == $tp ? 'selected' : '' }}>
                                                    {{ $tp }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="cantidad_miembros_hogar"><i class="fas fa-users"></i> Cantidad de Miembros en el Hogar:</label>
                                        <input type="number" name="cantidad_miembros_hogar" class="form-control" min="0" value="{{ old('cantidad_miembros_hogar', $alumno->cantidad_miembros_hogar) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const discapacidadSelect = document.querySelector('[name="discapacidad"]');
            const divPorcentajeDiscapacidad = document.getElementById('divPorcentajeDiscapacidad');
            const divCodigoConadis = document.getElementById('divCodigoConadis');

            discapacidadSelect.addEventListener('change', function () {
                if (this.value === 'Si') {
                    divPorcentajeDiscapacidad.style.display = 'block';
                    divCodigoConadis.style.display = 'block';
                } else {
                    divPorcentajeDiscapacidad.style.display = 'none';
                    divCodigoConadis.style.display = 'none';
                }
            });
        });

        document.getElementById('imageInput').addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImage = document.getElementById('previewImage');
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@stop
