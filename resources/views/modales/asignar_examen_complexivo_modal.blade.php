<div class="modal fade" id="examenComplexivoModal" tabindex="-1" aria-labelledby="examenComplexivoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="examenComplexivoModalLabel">
                    <i class="fas fa-clipboard-check me-2"></i> Asignar Examen Complexivo - <span
                        id="cohorteNombre"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="examenComplexivoForm" method="POST" action="{{ route('examen_complexivo.store') }}">
                    @csrf
                    <input type="hidden" id="cohorteId" name="cohorte_id">

                    <div class="mb-3">
                        <label for="lugar" class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i> Lugar del Examen
                        </label>
                        <input type="text" class="form-control" id="lugar" name="lugar" required>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_hora" class="form-label">
                            <i class="fas fa-calendar-check me-2"></i> Fecha y Hora del Examen
                        </label>
                        <input type="datetime-local" class="form-control" id="fecha_hora" name="fecha_hora" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> Guardar
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            <i class="fas fa-times-circle me-2"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
