<div class="section-title" style="background: #f3e5f5;">
    <h5 class="mb-0" style="color:#8e24aa;"><i class="fas fa-home"></i> Residencia</h5>
</div>

<div class="row py-2" style="background: #faf7fd;">
    <div class="col-md-3 form-group">
        <label for="pais_residencia">País de Residencia:</label>
        <input type="text" name="pais_residencia" class="form-control"
            value="{{ old('pais_residencia', $postulante->pais_residencia ?? '') }}">
    </div>
    <div class="col-md-2 form-group">
        <label for="anios_residencia">Años de Residencia:</label>
        <input type="text" name="anios_residencia" class="form-control"
            value="{{ old('anios_residencia', $postulante->anios_residencia ?? '') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="provincia">Provincia:</label>
        <select name="provincia" class="form-control">
            <option value="">Selecciona una provincia</option>
            @foreach ($provincias as $provincia)
                <option value="{{ $provincia }}" {{ old('provincia', $postulante->provincia ?? '') == $provincia ? 'selected' : '' }}>
                    {{ $provincia }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label for="canton">Cantón:</label>
        <input type="text" name="canton" class="form-control"
            value="{{ old('canton', $postulante->canton ?? '') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="parroquia">Parroquia:</label>
        <input type="text" name="parroquia" class="form-control"
            value="{{ old('parroquia', $postulante->parroquia ?? '') }}">
    </div>
</div>

<div class="row py-2" style="background: #faf7fd;">
    <div class="col-md-3 form-group">
        <label for="calle_principal">Calle Principal:</label>
        <input type="text" name="calle_principal" class="form-control"
            value="{{ old('calle_principal', $postulante->calle_principal ?? '') }}">
    </div>
    <div class="col-md-2 form-group">
        <label for="numero_direccion">Número:</label>
        <input type="text" name="numero_direccion" class="form-control"
            value="{{ old('numero_direccion', $postulante->numero_direccion ?? '') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="calle_secundaria">Calle Secundaria:</label>
        <input type="text" name="calle_secundaria" class="form-control"
            value="{{ old('calle_secundaria', $postulante->calle_secundaria ?? '') }}">
    </div>
    <div class="col-md-4 form-group">
        <label for="referencia_direccion">Referencia:</label>
        <input type="text" name="referencia_direccion" class="form-control"
            value="{{ old('referencia_direccion', $postulante->referencia_direccion ?? '') }}">
    </div>
</div>

<div class="row py-2" style="background: #faf7fd;">
    <div class="col-md-3 form-group">
        <label for="telefono_domicilio">Teléfono Domicilio:</label>
        <input type="text" name="telefono_domicilio" class="form-control"
            value="{{ old('telefono_domicilio', $postulante->telefono_domicilio ?? '') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="celular_residencia">Celular Residencia:</label>
        <input type="text" name="celular_residencia" class="form-control"
            value="{{ old('celular_residencia', $postulante->celular_residencia ?? '') }}">
    </div>
</div>
