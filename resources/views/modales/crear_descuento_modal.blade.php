<div class="modal fade" id="crearDescuentoModal" tabindex="-1" role="dialog" aria-labelledby="crearDescuentoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('descuentos.store') }}" method="POST">
            @csrf
            <div class="modal-content">

                <div class="modal-header" style="background-color: #003366; color: white;">
                    <h5 class="modal-title" id="crearDescuentoModalLabel">Crear Descuento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Porcentaje</label>
                        <input type="number" name="porcentaje" class="form-control" required min="0"
                            max="100">
                    </div>

                    <div class="form-group">
                        <label>Requisitos (uno por línea)</label>
                        <textarea name="requisitos" class="form-control requisitos-textarea" rows="5"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Activo</label>
                        <select name="activo" class="form-control">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Crear</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>

            </div>
        </form>
    </div>
</div>
