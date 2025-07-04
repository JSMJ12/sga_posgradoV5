<div class="section-title" style="background: #e3f2fd;">
    <h5 class="mb-0 text-primary"><i class="fas fa-user"></i> Datos Personales</h5>
</div>

<div class="row py-2" style="background: #f8fafc;">
    <div class="col-md-3 form-group text-center">
        <label for="imagen"><i class="fas fa-image"></i> Foto:</label>
        <input type="file" id="imageInput" name="imagen" accept="image/*" class="form-control mb-2">
        <img id="previewImage"
            src="{{ isset($alumno) && $alumno->imagen ? asset('storage/' . $alumno->imagen) : asset('images/default-avatar.jpg') }}"
            alt="Previsualización" class="img-thumbnail" style="width:100px;height:100px;">
        @error('imagen')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="col-md-3 form-group">
        <label for="dni"><i class="fas fa-id-card"></i> Cédula / Pasaporte</label>
        <input type="text" name="dni" class="form-control" value="{{ old('dni', $alumno->dni ?? '') }}"
            {{ isset($alumno->dni) && $alumno->dni !== '' ? 'readonly' : '' }} required>
        @error('dni')
            <div class="alert alert-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 form-group">
        <label for="apellidop"><i class="fas fa-user-tag"></i> Apellido Paterno:</label>
        <input type="text" name="apellidop" class="form-control"
            value="{{ old('apellidop', $alumno->apellidop ?? '') }}" required>
    </div>

    <div class="col-md-3 form-group">
        <label for="apellidom"><i class="fas fa-user-tag"></i> Apellido Materno:</label>
        <input type="text" name="apellidom" class="form-control"
            value="{{ old('apellidom', $alumno->apellidom ?? '') }}" required>
    </div>
</div>

<div class="row py-2" style="background: #f8fafc;">
    <div class="col-md-3 form-group">
        <label for="nombre1"><i class="fas fa-user"></i> Primer Nombre:</label>
        <input type="text" name="nombre1" class="form-control" value="{{ old('nombre1', $alumno->nombre1 ?? '') }}"
            required>
    </div>
    <div class="col-md-3 form-group">
        <label for="nombre2"><i class="fas fa-user"></i> Segundo Nombre:</label>
        <input type="text" name="nombre2" class="form-control" value="{{ old('nombre2', $alumno->nombre2 ?? '') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="email_personal"><i class="fas fa-envelope"></i> Correo Electrónico:</label>
        <input type="email" name="email_personal" class="form-control"
            value="{{ old('email_personal', $alumno->email_personal ?? '') }}" required>
        @error('email_personal')
            <div class="alert alert-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3 form-group">
        <label for="celular"><i class="fas fa-mobile-alt"></i> Celular:</label>
        <input type="text" name="celular" class="form-control" value="{{ old('celular', $alumno->celular ?? '') }}">
    </div>
</div>

<div class="row py-2" style="background: #f8fafc;">
    <div class="col-md-3 form-group">
        <label for="telefono_convencional"><i class="fas fa-phone"></i> Teléfono Convencional:</label>
        <input type="text" name="telefono_convencional" class="form-control"
            value="{{ old('telefono_convencional', $alumno->telefono_convencional ?? '') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="fecha_nacimiento"><i class="fas fa-calendar"></i> Fecha de Nacimiento:</label>
        <input type="date" name="fecha_nacimiento" class="form-control"
            value="{{ old('fecha_nacimiento', $alumno->fecha_nacimiento ?? '') }}" required>
    </div>
    <div class="col-md-2 form-group">
        <label for="edad"><i class="fas fa-hourglass-half"></i> Edad:</label>
        <input type="number" name="edad" class="form-control" min="0"
            value="{{ old('edad', $alumno->edad ?? '') }}">
    </div>
    <div class="col-md-2 form-group">
        <label for="sexo"><i class="fas fa-venus-mars"></i> Sexo:</label>
        <select name="sexo" class="form-control" required>
            <option value="M" {{ old('sexo', $alumno->sexo ?? '') == 'M' ? 'selected' : '' }}>Hombre</option>
            <option value="F" {{ old('sexo', $alumno->sexo ?? '') == 'F' ? 'selected' : '' }}>Mujer</option>
        </select>
    </div>
    <div class="col-md-2 form-group">
        <label for="tipo_sangre"><i class="fas fa-tint"></i> Tipo de Sangre:</label>
        <input type="text" name="tipo_sangre" class="form-control"
            value="{{ old('tipo_sangre', $alumno->tipo_sangre ?? '') }}">
    </div>
</div>

<div class="row py-2" style="background: #f8fafc;">
    <div class="col-md-3 form-group">
        <label for="nacionalidad">Nacionalidad:</label>
        <input type="text" name="nacionalidad" class="form-control"
            value="{{ old('nacionalidad', $alumno->nacionalidad ?? '') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="libreta_militar">Libreta Militar:</label>
        <input type="text" name="libreta_militar" class="form-control"
            value="{{ old('libreta_militar', $alumno->libreta_militar ?? '') }}">
    </div>
    <div class="col-md-2 form-group">
        <label for="discapacidad">¿Discapacidad?</label>
        <select name="discapacidad" class="form-control" required>
            <option value="No"
                {{ old('discapacidad', $alumno->tipo_discapacidad ?? null ? 'Si' : 'No') == 'No' ? 'selected' : '' }}>
                No</option>
            <option value="Si"
                {{ old('discapacidad', $alumno->tipo_discapacidad ?? null ? 'Si' : 'No') == 'Si' ? 'selected' : '' }}>
                Sí</option>
        </select>
    </div>

</div>

<div class="row py-2" style="background: #f8fafc;">
    <div class="col-md-3 form-group" id="divPorcentajeDiscapacidad" style="display: none;">
        <label for="porcentaje_discapacidad">Porcentaje de discapacidad:</label>
        <input type="number" name="porcentaje_discapacidad" class="form-control" min="0" max="100"
            value="{{ old('porcentaje_discapacidad', $alumno->porcentaje_discapacidad ?? '') }}">
    </div>
    <div class="col-md-3 form-group" id="divCodigoConadis" style="display: none;">
        <label for="carnet_discapacidad">Código CONADIS:</label>
        <input type="text" name="carnet_discapacidad" class="form-control"
            value="{{ old('carnet_discapacidad', $alumno->carnet_discapacidad ?? '') }}">
    </div>
    <div class="col-md-3 form-group" id="divTipoDiscapacidad" style="display: none;">
        <label for="tipo_discapacidad">Tipo de Discapacidad:</label>
        <input type="text" name="tipo_discapacidad" class="form-control"
            value="{{ old('tipo_discapacidad', $alumno->tipo_discapacidad ?? '') }}">
    </div>
</div>
