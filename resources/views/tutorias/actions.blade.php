@if ($item->titulaciones?->isNotEmpty())
    <div class="d-flex flex-column align-items-start">
        <span class="badge badge-warning mb-2" style="background-color: #f39c12; color: white; padding: 5px 10px;">
            Alumno Graduado
        </span>
    </div>
@endif

@if ($item->estado === 'titulado' && ($item->titulaciones?->isEmpty() ?? true))
    <div class="d-flex flex-column align-items-start">
        <div class="mb-2">
            <span class="badge badge-warning mb-2" style="background-color: #f39c12; color: white; padding: 5px 10px;">
                Alumno Titulado
            </span>

            <!-- Botón para abrir el modal de titulación -->
            <button type="button" class="btn btn-sm btn-primary ms-2" data-toggle="modal"
                data-target="#modalTitulacion{{ $item->id }}">
                <i class="fas fa-user-graduate"></i> Completar Titulación
            </button>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalTitulacion{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modalTitulacionLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulacionLabel{{ $item->id }}">Registrar Titulación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('titulaciones_alumno.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="tesis_id" value="{{ $item->id }}">
                        <input type="hidden" name="titulado" value="1">

                        <div class="form-group">
                            <label for="tesis_path">Archivo(s) de Tesis (solo PDF)</label>
                            <input type="file" class="form-control" id="tesis_path" name="tesis_path[]" accept="application/pdf" multiple>
                        </div>

                        <div class="form-group">
                            <label for="fecha_graduacion">Fecha de Graduación</label>
                            <input type="date" class="form-control" id="fecha_graduacion" name="fecha_graduacion" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endif

@if ($item->tutorias->count() < 3)
    <div class="d-flex flex-column align-items-start">
        <div class="mb-2">
            <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                data-target="#crearTutoriaModal{{ $item->id }}">
                <i class="fas fa-plus"></i> Asignar Tutoría
            </button>
        </div>
    </div>
@endif

@if ($item->tutorias->count() >= 1 && $item->estado !== 'titulado')
    <div class="d-flex flex-column align-items-start">
        <div class="mb-2">
            <a href="{{ route('tutorias.listar', $item->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-list"></i> Ver Tutorías
            </a>
        </div>
    </div>
@endif

<!-- Modal Asignar Tutoría -->
<div class="modal fade" id="crearTutoriaModal{{ $item->id }}" tabindex="-1"
    aria-labelledby="crearTutoriaModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title">Asignar Nueva Tutoría</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="POST" action="{{ route('tutorias.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="tesis_id" value="{{ $item->id }}">
                    <div class="mb-3">
                        <label for="tipo-tutoria"><i class="fas fa-chalkboard-teacher"></i> Tipo de Tutoría</label>
                        <select class="form-control tipo-select" id="tipo-tutoria{{ $item->id }}" name="tipo">
                            <option value="virtual">Virtual</option>
                            <option value="presencial">Presencial</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label id="dynamic-label{{ $item->id }}" for="dynamic-input{{ $item->id }}" class="form-label">
                            <i class="fas fa-map-marker-alt" id="icon-lugar{{ $item->id }}"></i>
                            <i class="fas fa-link" id="icon-link{{ $item->id }}" style="display: none;"></i>
                            Lugar / Link de la Reunión
                        </label>
                        <input type="text" id="dynamic-input{{ $item->id }}" name="detalle" class="form-control"
                            placeholder="Ingrese el lugar o el link">
                    </div>

                    <div class="mb-3">
                        <label for="fecha"><i class="fas fa-calendar-alt"></i> Fecha y Hora</label>
                        <input type="datetime-local" name="fecha" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones"><i class="fas fa-comments"></i> Observaciones</label>
                        <textarea name="observaciones" class="form-control"
                            placeholder="Ingrese observaciones adicionales"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Asignar Tutoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const items = @json($tesis ?? []);

    items.forEach(item => {
        const selectTipo = document.getElementById('tipo-tutoria' + item.id);
        const dynamicLabel = document.getElementById('dynamic-label' + item.id);
        const dynamicInput = document.getElementById('dynamic-input' + item.id);

        function toggleInput() {
            if (selectTipo.value === 'presencial') {
                dynamicLabel.innerHTML = '<i class="fas fa-map-marker-alt"></i> Lugar';
                dynamicInput.placeholder = 'Ingrese el lugar';
                dynamicInput.type = 'text';
            } else {
                dynamicLabel.innerHTML = '<i class="fas fa-link"></i> Link de Reunión';
                dynamicInput.placeholder = 'Ingrese el link de reunión';
                dynamicInput.type = 'url';
            }
        }

        selectTipo.addEventListener('change', toggleInput);
        toggleInput();
    });
});
</script>
