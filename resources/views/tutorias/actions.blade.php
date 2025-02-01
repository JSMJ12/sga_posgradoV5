<!-- Botón para abrir modal -->
@if($item->alumno->titulaciones != null)
    <span class="badge badge-warning" style="background-color: #f39c12; color: white; padding: 5px 10px;">
        Alumno Graduado
    </span>
@endif
@if($item->alumno->tesis && $item->alumno->tesis->first()->estado == 'titulado' && $item->alumno->titulaciones == null)
    <span class="badge badge-warning" style="background-color: #f39c12; color: white; padding: 5px 10px;">
        Alumno titulado
    </span>

    <!-- Botón para abrir el modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTitulacion">
        Completar Titulación
    </button>

    <!-- Modal -->
    <div class="modal fade" id="modalTitulacion" tabindex="-1" role="dialog" aria-labelledby="modalTitulacionLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulacionLabel">Registrar Titulación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('titulaciones_alumno.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Campo oculto para el DNI del alumno -->
                        <input type="hidden" name="alumno_dni" value="{{ $item->alumno->dni }}">
    
                        <!-- El campo 'titulado' se establece en 1 por defecto -->
                        <input type="hidden" name="titulado" value="1">
    
                        <!-- Campo para subir archivos (solo PDF y múltiples archivos) -->
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

@if($item->tutorias->count() < 3)
    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#crearTutoriaModal{{ $item->id }}">
        <i class="fas fa-plus"></i> Asignar Tutoría
    </button>
@endif
@if($item->tutorias->count() >= 1 && $item->alumno->tesis->first()->estado !== 'titulado')
    <a href="{{ route('tutorias.listar', $item->id) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-list"></i> Ver Tutorías
    </a>
@endif

<!-- Modal -->
<div class="modal fade" id="crearTutoriaModal{{ $item->id }}" tabindex="-1" aria-labelledby="crearTutoriaModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title">Asignar Nueva Tutoría</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('tutorias.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="tesis_id" value="{{ $item->id }}">

                    <!-- Selección del tipo de tutoría -->
                    <div class="mb-3">
                        <label for="tipo">
                            <i class="fas fa-chalkboard-teacher"></i> Tipo de Tutoría
                        </label>
                        <select class="form-control tipo-select" id="tipo-tutoria" name="tipo">
                            <option value="virtual">Virtual</option>
                            <option value="presencial">Presencial</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label id="dynamic-label" for="dynamic-input" class="form-label">
                            <i class="fas fa-map-marker-alt" id="icon-lugar"></i>
                            <i class="fas fa-link" id="icon-link" style="display: none;"></i> 
                            Lugar / Link de la Reunión
                        </label>
                        <input type="text" id="dynamic-input" name="detalle" class="form-control" placeholder="Ingrese el lugar o el link">
                    </div>

                    <div class="mb-3">
                        <label for="fecha">
                            <i class="fas fa-calendar-alt"></i> Fecha y Hora
                        </label>
                        <input type="datetime-local" name="fecha" class="form-control" required>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label for="observaciones">
                            <i class="fas fa-comments"></i> Observaciones
                        </label>
                        <textarea name="observaciones" class="form-control" placeholder="Ingrese observaciones adicionales"></textarea>
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
    document.addEventListener('DOMContentLoaded', function () {
        const selectTipo = document.getElementById('tipo-tutoria');
        const dynamicLabel = document.getElementById('dynamic-label');
        const dynamicInput = document.getElementById('dynamic-input');

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
        toggleInput(); // Inicializar en la carga
    });
</script>
