<div class="modal fade" id="editarAulaModal{{ $aula->id }}" tabindex="-1" aria-labelledby="editarAulaModalLabel{{ $aula->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="editarAulaModalLabel{{$aula->id }}">Editar Aula</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('aulas.update', $aula->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $aula->nombre }}">
                    </div>
                
                    <div class="mb-3">
                        <label for="piso" class="form-label">Piso</label>
                        <input type="text" class="form-control" id="piso" name="piso" value="{{ $aula->piso }}">
                    </div>
                
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $aula->codigo }}">
                    </div>
                
                    <div class="mb-3">
                        <label for="paralelo" class="form-label">Paralelo</label>
                        <input type="text" class="form-control" id="paralelo" name="paralelo" value="{{ $aula->paralelo }}">
                    </div>
                
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>