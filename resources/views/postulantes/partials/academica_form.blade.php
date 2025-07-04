<div class="section-title" style="background: #fff3e0;">
    <h5 class="mb-0" style="color:#ef6c00;"><i class="fas fa-book-open"></i> Información Académica</h5>
</div>

<div class="row py-2" style="background: #fff8f0;">
    <div class="col-md-4 form-group">
        <label for="especialidad_bachillerato">Especialidad del bachillerato cursado:</label>
        <input type="text" name="especialidad_bachillerato" class="form-control"
            value="{{ old('especialidad_bachillerato', $postulante->especialidad_bachillerato ?? '') }}"
            placeholder="Ej. Ciencias, Técnico en informática">
    </div>
    <div class="col-md-4 form-group">
        <label for="colegio_bachillerato">Nombre del colegio donde obtuvo el bachillerato:</label>
        <input type="text" name="colegio_bachillerato" class="form-control"
            value="{{ old('colegio_bachillerato', $postulante->colegio_bachillerato ?? '') }}"
            placeholder="Ej. Colegio Nacional Portoviejo">
    </div>
    <div class="col-md-4 form-group">
        <label for="ciudad_bachillerato">Ciudad del colegio:</label>
        <input type="text" name="ciudad_bachillerato" class="form-control"
            value="{{ old('ciudad_bachillerato', $postulante->ciudad_bachillerato ?? '') }}"
            placeholder="Ej. Portoviejo">
    </div>
</div>

<div class="row py-2" style="background: #fff8f0;">
    <div class="col-md-4 form-group">
        <label for="titulo_profesional">Título profesional obtenido (tercer nivel):</label>
        <input type="text" name="titulo_profesional" class="form-control"
            value="{{ old('titulo_profesional', $postulante->titulo_profesional ?? '') }}"
            placeholder="Ej. Ingeniero en Sistemas">
    </div>
    <div class="col-md-4 form-group">
        <label for="especialidad_mencion">Especialidad o mención del título:</label>
        <input type="text" name="especialidad_mencion" class="form-control"
            value="{{ old('especialidad_mencion', $postulante->especialidad_mencion ?? '') }}"
            placeholder="Ej. Redes, Desarrollo de software">
    </div>
    <div class="col-md-4 form-group">
        <label for="universidad_titulo">Nombre de la universidad donde estudió:</label>
        <input type="text" name="universidad_titulo" class="form-control"
            value="{{ old('universidad_titulo', $postulante->universidad_titulo ?? '') }}"
            placeholder="Ej. Universidad Estatal del Sur de Manabí">
    </div>
</div>

<div class="row py-2" style="background: #fff8f0;">
    <div class="col-md-4 form-group">
        <label for="ciudad_universidad">Ciudad de la universidad:</label>
        <input type="text" name="ciudad_universidad" class="form-control"
            value="{{ old('ciudad_universidad', $postulante->ciudad_universidad ?? '') }}"
            placeholder="Ej. Manta">
    </div>
    <div class="col-md-4 form-group">
        <label for="pais_universidad">País de la universidad:</label>
        <input type="text" name="pais_universidad" class="form-control"
            value="{{ old('pais_universidad', $postulante->pais_universidad ?? '') }}"
            placeholder="Ej. Ecuador">
    </div>
    <div class="col-md-4 form-group">
        <label for="registro_senescyt">Número de registro SENESCYT:</label>
        <input type="text" name="registro_senescyt" class="form-control"
            value="{{ old('registro_senescyt', $postulante->registro_senescyt ?? '') }}"
            placeholder="Ej. 1234-RR-5678">
    </div>
</div>

<div class="row py-2" style="background: #fff8f0;">
    <div class="col-md-4 form-group">
        <label for="titulo_posgrado">Título de posgrado obtenido:</label>
        <input type="text" name="titulo_posgrado" class="form-control"
            value="{{ old('titulo_posgrado', $postulante->titulo_posgrado ?? '') }}"
            placeholder="Ej. Magíster en Educación">
    </div>
    <div class="col-md-4 form-group">
        <label for="denominacion_posgrado">Denominación o área del posgrado:</label>
        <input type="text" name="denominacion_posgrado" class="form-control"
            value="{{ old('denominacion_posgrado', $postulante->denominacion_posgrado ?? '') }}"
            placeholder="Ej. Innovación Educativa, Gestión Pública">
    </div>
    <div class="col-md-4 form-group">
        <label for="universidad_posgrado">Nombre de la universidad de posgrado:</label>
        <input type="text" name="universidad_posgrado" class="form-control"
            value="{{ old('universidad_posgrado', $postulante->universidad_posgrado ?? '') }}"
            placeholder="Ej. Universidad Central del Ecuador">
    </div>
</div>

<div class="row py-2" style="background: #fff8f0;">
    <div class="col-md-4 form-group">
        <label for="ciudad_posgrado">Ciudad donde realizó el posgrado:</label>
        <input type="text" name="ciudad_posgrado" class="form-control"
            value="{{ old('ciudad_posgrado', $postulante->ciudad_posgrado ?? '') }}"
            placeholder="Ej. Quito">
    </div>
    <div class="col-md-4 form-group">
        <label for="pais_posgrado">País del posgrado:</label>
        <input type="text" name="pais_posgrado" class="form-control"
            value="{{ old('pais_posgrado', $postulante->pais_posgrado ?? '') }}"
            placeholder="Ej. Ecuador">
    </div>
</div>
