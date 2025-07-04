<div class="section-title" style="background: #e3f6fd;">
    <h5 class="mb-0" style="color:#0288d1;"><i class="fas fa-briefcase"></i> Información Laboral</h5>
</div>

<div class="row py-2" style="background: #f0fbff;">
    <div class="col-md-4 form-group">
        <label for="lugar_trabajo">Nombre de la institución o empresa donde trabaja:</label>
        <input type="text" name="lugar_trabajo" class="form-control" 
               placeholder="Ej. Ministerio de Salud" 
               value="{{ old('lugar_trabajo', $postulante->lugar_trabajo ?? '') }}">
    </div>
    <div class="col-md-4 form-group">
        <label for="funcion_laboral">Cargo o función que desempeña:</label>
        <input type="text" name="funcion_laboral" class="form-control" 
               placeholder="Ej. Analista de sistemas" 
               value="{{ old('funcion_laboral', $postulante->funcion_laboral ?? '') }}">
    </div>
    <div class="col-md-4 form-group">
        <label for="ciudad_trabajo">Ciudad donde trabaja:</label>
        <input type="text" name="ciudad_trabajo" class="form-control" 
               placeholder="Ej. Portoviejo" 
               value="{{ old('ciudad_trabajo', $postulante->ciudad_trabajo ?? '') }}">
    </div>
</div>

<div class="row py-2" style="background: #f0fbff;">
    <div class="col-md-6 form-group">
        <label for="direccion_trabajo">Dirección del lugar de trabajo:</label>
        <input type="text" name="direccion_trabajo" class="form-control" 
               placeholder="Ej. Av. Universitaria y Calle A" 
               value="{{ old('direccion_trabajo', $postulante->direccion_trabajo ?? '') }}">
    </div>
    <div class="col-md-3 form-group">
        <label for="telefono_trabajo">Teléfono de contacto laboral:</label>
        <input type="text" name="telefono_trabajo" class="form-control" 
               placeholder="Ej. 05 2600 123" 
               value="{{ old('telefono_trabajo', $postulante->telefono_trabajo ?? '') }}">
    </div>
</div>
