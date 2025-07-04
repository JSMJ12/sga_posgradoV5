{{-- filepath: resources/views/postulantes/partials/contacto_emergencia_form.blade.php --}}
<div class="section-title bg-success-light p-3 rounded mb-3">
    <h5 class="mb-0 text-success">
        <i class="fas fa-phone-alt me-2"></i> Contacto de Emergencia
    </h5>
</div>

<div class="row g-3 py-3 bg-light rounded align-items-end">
    <div class="col-md-4">
        <label for="contacto_apellidos" class="form-label fw-semibold">Apellidos:</label>
        <input type="text" id="contacto_apellidos" name="contacto_apellidos" class="form-control"
            placeholder="Ingrese apellidos" required
            value="{{ old('contacto_apellidos', $postulante->contacto_apellidos ?? '') }}">
    </div>
    <div class="col-md-3">
        <label for="contacto_nombres" class="form-label fw-semibold">Nombres:</label>
        <input type="text" id="contacto_nombres" name="contacto_nombres" class="form-control"
            placeholder="Ingrese nombres" required
            value="{{ old('contacto_nombres', $postulante->contacto_nombres ?? '') }}">
    </div>
    <div class="col-md-2">
        <label for="contacto_parentesco" class="form-label fw-semibold">Parentesco:</label>
        <input type="text" id="contacto_parentesco" name="contacto_parentesco" class="form-control"
            placeholder="Ej. Padre, Hermano" required
            value="{{ old('contacto_parentesco', $postulante->contacto_parentesco ?? '') }}">
    </div>
    <div class="col-md-3">
        <label for="contacto_telefono" class="form-label fw-semibold">Teléfono:</label>
        <input type="tel" id="contacto_telefono" name="contacto_telefono" class="form-control"
            placeholder="Teléfono convencional"
            value="{{ old('contacto_telefono', $postulante->contacto_telefono ?? '') }}">
    </div>
    <div class="col-md-3">
        <label for="contacto_celular" class="form-label fw-semibold">Celular:</label>
        <input type="tel" id="contacto_celular" name="contacto_celular" class="form-control"
            placeholder="Celular" required
            value="{{ old('contacto_celular', $postulante->contacto_celular ?? '') }}">
    </div>
</div>
