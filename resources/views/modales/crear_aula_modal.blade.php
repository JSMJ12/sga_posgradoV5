<!-- Modal Crear Aula -->
<div class="modal fade" id="crearAulaModal" tabindex="-1" aria-labelledby="crearAulaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="crearAulaModalLabel">Crear Aula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('aulas.store') }}" method="POST">
                    @csrf
        
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
            
                    <div class="mb-3">
                        <label for="piso" class="form-label">Piso</label>
                        <input type="text" class="form-control" id="piso" name="piso" required>
                    </div>
            
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" required>
                    </div>
            
                    <div class="mb-3">
                        <label for="paralelos_id" class="form-label">Paralelo</label>
                        <input type="text" class="form-control" id="paralelo" name="paralelo" required>
                    </div>
            
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>