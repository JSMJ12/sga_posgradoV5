<div class="modal fade" id="editarDescuentoModal{{ $descuento->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="editarDescuentoModalLabel{{ $descuento->id }}">Editar Descuento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('descuentos.update', $descuento) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $descuento->nombre }}" required>
                    </div>

                    <div class="form-group">
                        <label>Porcentaje</label>
                        <input type="number" name="porcentaje" class="form-control" value="{{ $descuento->porcentaje }}" required min="0" max="100">
                    </div>

                    <div class="form-group">
                        <label>Requisitos (uno por línea)</label>
                        <textarea name="requisitos" class="form-control requisitos-textarea" rows="5">{{ $descuento->requisitos ? implode("\n", json_decode($descuento->requisitos, true)) : '' }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Activo</label>
                        <select name="activo" class="form-control">
                            <option value="1" {{ $descuento->activo ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ !$descuento->activo ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>

            </form>

        </div>
    </div>
</div>
