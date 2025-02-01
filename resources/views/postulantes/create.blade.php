@extends('layouts.app')
<title>Postulación</title>

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="header">
                        <img src="{{ asset('images/logo_unesum_certificado.png') }}" alt="University Logo" class="logo">
                        <img src="{{ asset('images/posgrado-25.png') }}" alt="University Seal" class="seal"><br><span
                            class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span><br>
                        <span class="institute">INSTITUTO DE POSGRADO</span><br>
                    </div>
                    <div class="divider"></div>
                    <div class="card-body">

                        <form action="{{ route('postulaciones.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="maestria_id">Maestría a Postular:</label>
                                        <select class="custom-select" id="maestria_id" name="maestria_id" required
                                            style="width: 100%;">
                                            <option value="" disabled selected>Seleccione una maestría</option>
                                            @foreach ($maestrias as $maestria)
                                                <option value="{{ $maestria->id }}">
                                                    <strong>{{ $maestria->nombre }}</strong><br>
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="imagen">Foto:</label>
                                            <input type="file" id="imageInput" name="imagen" accept="image/*">
                                            @error('imagen')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="dni">Cédula / Pasaporte</label>
                                        <input type="text" name="dni" class="form-control" required>
                                        @error('dni')
                                            @if ($message == 'The dni field is required.')
                                                {{-- Verificar si el error es por omisión --}}
                                                <div class="alert alert-danger">
                                                    <strong>Error:</strong> El campo de cédula o pasaporte es obligatorio.
                                                </div>
                                            @else
                                                {{-- Si no es por omisión, asumir que es por duplicidad --}}
                                                <div class="alert alert-danger">
                                                    <strong>Error:</strong> Ya has realizado una postulación previamente.
                                                </div>
                                            @endif
                                        @enderror
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="apellidop">Apellido Paterno:</label>
                                        <input type="text" name="apellidop" class="form-control" required>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="apellidom">Apellido Materno:</label>
                                        <input type="text" name="apellidom" class="form-control" required>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="nombre1">Primer Nombre:</label>
                                        <input type="text" name="nombre1" class="form-control" required>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="nombre2">Segundo Nombre:</label>
                                        <input type="text" name="nombre2" class="form-control" required>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="correo_electronico">Correo Electrónico:</label>
                                        <input type="email" name="correo_electronico" class="form-control"
                                            placeholder="correo@example.com" required>
                                        @error('correo_electronico')
                                            @if ($message == 'The correo_electronico field is required.')
                                                <div class="alert alert-danger">
                                                    <strong>Error:</strong> El Correo Electrónico es obligatorio.
                                                </div>
                                            @else
                                                <div class="alert alert-danger">
                                                    <strong>Error:</strong> Ya has realizado una postulación previamente con
                                                    este correo electrónico.
                                                </div>
                                            @endif
                                        @enderror
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="celular">Celular:</label>
                                        <input type="text" name="celular" class="form-control"
                                            placeholder="Número de celular">
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="titulo_profesional">Título Profesional:</label>
                                        <input type="text" name="titulo_profesional" class="form-control"
                                            placeholder="Título profesional">
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="universidad_titulo">Universidad en la que obtuvo el título de tercer
                                            nivel:</label>
                                        <input type="text" name="universidad_titulo" class="form-control"
                                            placeholder="Nombre de la universidad">
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="sexo">Sexo:</label>
                                        <select name="sexo" class="form-control" required>
                                            <option value="M">Hombre</option>
                                            <option value="F">Mujer</option>
                                        </select>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                                        <input type="date" name="fecha_nacimiento" class="form-control" required>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="nacionalidad">Nacionalidad:</label>
                                        <input type="text" name="nacionalidad" class="form-control"
                                            placeholder="Nacionalidad">
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discapacidad">Posee alguna Discapacidad:</label>
                                        <select name="discapacidad" class="form-control" required>
                                            <option value="No">No</option>
                                            <option value="Si">Sí</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="divPorcentajeDiscapacidad" style="display: none;">
                                        <label for="porcentaje_discapacidad">Porcentaje de discapacidad (en caso de no
                                            tener, ingresar 0):</label>
                                        <input type="number" name="porcentaje_discapacidad" class="form-control"
                                            min="0" max="100">
                                    </div>

                                    <div class="form-group" id="divCodigoConadis" style="display: none;">
                                        <label for="codigo_conadis">Código CONADIS (en caso de tener carnet del MSP
                                            ingresar número de cédula):</label>
                                        <input type="text" name="codigo_conadis" class="form-control"
                                            placeholder="Código CONADIS">
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="provincia">Provincia:</label>
                                        <select name="provincia" id="provincia" class="form-control">
                                            <option value="">Selecciona una provincia</option>
                                            @foreach ($provincias as $provincia)
                                                <option value="{{ $provincia }}">{{ $provincia }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="etnia">Etnia:</label>
                                        <input type="text" name="etnia" class="form-control" placeholder="Etnia">
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="nacionalidad_indigena">Nacionalidad (en caso que se auto identifique
                                            como Indígena, caso contrario ingresar NO APLICA):</label>
                                        <input type="text" name="nacionalidad_indigena" class="form-control"
                                            placeholder="Nacionalidad Indígena">
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="canton">Cantón:</label>
                                        <input type="text" name="canton" class="form-control" placeholder="Cantón">
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="direccion">Dirección:</label>
                                        <input type="text" name="direccion" class="form-control"
                                            placeholder="Dirección">
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="tipo_colegio">Tipo de Colegio:</label>
                                        <select name="tipo_colegio" id="tipo_colegio" class="form-control">
                                            <option value="">Selecciona el Tipo de Colegio</option>
                                            @foreach ($tipo_colegio as $tp)
                                                <option value="{{ $tp }}">{{ $tp }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="cantidad_miembros_hogar">Cantidad de Miembros en el Hogar:</label>
                                        <input type="number" name="cantidad_miembros_hogar" class="form-control"
                                            min="0">
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="ingreso_total_hogar">Ingreso total del Hogar:</label>
                                        <select name="ingreso_total_hogar" id="ingreso_total_hogar" class="form-control">
                                            <option value="">Selecciona el Ingreso total del Hogar</option>
                                            @foreach ($ingreso_hogar as $tp)
                                                <option value="{{ $tp }}">{{ $tp }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="nivel_formacion_padre">Nivel Formación Padre:</label>
                                        <select name="nivel_formacion_padre" id="nivel_formacion_padre"
                                            class="form-control">
                                            <option value="">Selecciona el Nivel Formación Padre</option>
                                            @foreach ($formacion_padre as $tp)
                                                <option value="{{ $tp }}">{{ $tp }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="nivel_formacion_madre">Nivel Formación Madre:</label>
                                        <select name="nivel_formacion_madre" id="nivel_formacion_madre"
                                            class="form-control">
                                            <option value="">Selecciona el Nivel Formación Madre</option>
                                            @foreach ($formacion_padre as $tp)
                                                <option value="{{ $tp }}">{{ $tp }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="origen_recursos_estudios">Origen de los Recursos de Estudios:</label>
                                        <select name="origen_recursos_estudios" id="origen_recursos_estudios"
                                            class="form-control">
                                            <option value="">Selecciona el Origen de los Recursos de Estudios
                                            </option>
                                            @foreach ($origen_recursos as $tp)
                                                <option value="{{ $tp }}">{{ $tp }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
<style>
    #imagePreview {
        width: 200px;
        height: 200px;
        border: 1px solid #ccc;
        margin-top: 10px;
        background-size: cover;
        background-position: center;
    }



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
        width: 84px;
        height: 93px;
        position: absolute;
        top: 20px;
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

    .custom-select {
        display: block;
        width: 100%;
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .custom-select:disabled {
        background-color: #e9ecef;
    }

    .custom-select::-ms-expand {
        background-color: transparent;
        border: 0;
    }

    .custom-select-wrapper::after {
        content: '\25BC';
        position: absolute;
        top: 50%;
        right: 0.75rem;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .card.body {
        margin: 0;
        font-family: "Nunito", sans-serif;
        font-size: 0.9rem;
        font-weight: 400;
        line-height: 1.6;
        color: #212529;
        text-align: left;
        background-image: url('/images/portada2.png') !important;
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center center;
        background-color: #f8fafc;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const discapacidadSelect = document.querySelector('[name="discapacidad"]');
        const divPorcentajeDiscapacidad = document.getElementById('divPorcentajeDiscapacidad');
        const divCodigoConadis = document.getElementById('divCodigoConadis');
        const divPDFConadis = document.getElementById('divPDFConadis');

        discapacidadSelect.addEventListener('change', function() {
            if (this.value === 'Si') {
                divPorcentajeDiscapacidad.style.display = 'block';
                divCodigoConadis.style.display = 'block';
                divPDFConadis.style.display = 'block';
            } else {
                divPorcentajeDiscapacidad.style.display = 'none';
                divCodigoConadis.style.display = 'none';
                divPDFConadis.style.display = 'none';
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '¡Bienvenido!',
            text: 'Una vez completado el registro, por favor verifica la postulación en el correo electrónico que registraste.',
            icon: 'info',
            confirmButtonText: 'Entendido'
        });
    });
</script>
