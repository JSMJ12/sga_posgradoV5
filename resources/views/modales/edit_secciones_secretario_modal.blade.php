<div class="modal fade" id="editarSeccionModal_{{ $secretario->seccion->id }}" tabindex="-1" role="dialog" aria-labelledby="editarSeccionModalLabel_{{ $secretario->seccion->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="editarSeccionModalLabel_{{ $secretario->seccion->id }}">Editar Sección</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('secciones.update', $secretario->seccion->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre">Nombre de la sección:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $secretario->seccion->nombre }}" required>
                    </div>
                    <div class="form-group">
                        <label for="maestrias">Maestrías asociadas:</label>
                        @foreach ($maestrias as $maestria)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="maestria_{{ $secretario->id }}_{{ $maestria->id }}" name="maestrias[]" value="{{ $maestria->id }}" {{ in_array($maestria->id, $secretario->seccion->maestrias->pluck('id')->toArray()) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="maestria_{{ $secretario->id }}_{{ $maestria->id }}">{{ $maestria->nombre }}</label>
                            </div>
                        @endforeach
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