<div class="section-title" style="background: #fbe9e7;">
    <h5 class="mb-0" style="color:#d84315;"><i class="fas fa-money-bill-wave"></i> Datos Socioeconómicos</h5>
</div>

<div class="row py-2" style="background: #fff5f3;">
    <div class="col-md-3 form-group">
        <label for="etnia">Etnia:</label>
        <input type="text" name="etnia" class="form-control"
            value="{{ old('etnia', $alumno->etnia ?? '') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="tipo_colegio">Tipo de Colegio:</label>
        <select name="tipo_colegio" class="form-control">
            <option value="">Selecciona el Tipo de Colegio</option>
            @foreach ($tipo_colegio as $tp)
                <option value="{{ $tp }}" {{ old('tipo_colegio', $alumno->tipo_colegio ?? '') == $tp ? 'selected' : '' }}>
                    {{ $tp }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row py-2" style="background: #fff5f3;">
    <div class="col-md-3 form-group">
        <label for="cantidad_miembros_hogar">Miembros en el Hogar:</label>
        <input type="number" name="cantidad_miembros_hogar" class="form-control" min="0"
            value="{{ old('cantidad_miembros_hogar', $alumno->cantidad_miembros_hogar ?? '') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="ingreso_total_hogar">Ingreso total del Hogar:</label>
        <select name="ingreso_total_hogar" class="form-control">
            <option value="">Selecciona el Ingreso total del Hogar</option>
            @foreach ($ingreso_hogar as $tp)
                <option value="{{ $tp }}" {{ old('ingreso_total_hogar', $alumno->ingreso_total_hogar ?? '') == $tp ? 'selected' : '' }}>
                    {{ $tp }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label for="nivel_formacion_padre">Nivel Formación Padre:</label>
        <select name="nivel_formacion_padre" class="form-control">
            <option value="">Selecciona el Nivel Formación Padre</option>
            @foreach ($formacion_padre as $tp)
                <option value="{{ $tp }}" {{ old('nivel_formacion_padre', $alumno->nivel_formacion_padre ?? '') == $tp ? 'selected' : '' }}>
                    {{ $tp }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label for="nivel_formacion_madre">Nivel Formación Madre:</label>
        <select name="nivel_formacion_madre" class="form-control">
            <option value="">Selecciona el Nivel Formación Madre</option>
            @foreach ($formacion_padre as $tp)
                <option value="{{ $tp }}" {{ old('nivel_formacion_madre', $alumno->nivel_formacion_madre ?? '') == $tp ? 'selected' : '' }}>
                    {{ $tp }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row py-2" style="background: #fff5f3;">
    <div class="col-md-4 form-group">
        <label for="origen_recursos_estudios">Origen de los Recursos de Estudios:</label>
        <select name="origen_recursos_estudios" class="form-control">
            <option value="">Selecciona el Origen de los Recursos de Estudios</option>
            @foreach ($origen_recursos as $tp)
                <option value="{{ $tp }}" {{ old('origen_recursos_estudios', $alumno->origen_recursos_estudios ?? '') == $tp ? 'selected' : '' }}>
                    {{ $tp }}
                </option>
            @endforeach
        </select>
    </div>
</div>
