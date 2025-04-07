<div class="modal fade" id="createMaestriaModal" tabindex="-1" role="dialog" aria-labelledby="createMaestriaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="createMaestriaModalLabel">Crear Maestria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('maestrias.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="maestria-codigo">Codigo:</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" required>
                    </div>
                    <div class="form-group">
                        <label for="maestria-nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="coordinador">Coordinador:</label>
                        <select class="form-control" id="coordinador" name="coordinador" required>
                            <option value="">Selecciona un coordinador</option>
                            @foreach($docentes as $docente)
                                <option value="{{ $docente->dni }}">{{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }} {{ $docente->apellidom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="matricula">Precio de Matrícula:</label>
                        <input type="number" step="0.01" class="form-control" id="matricula" name="matricula" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="arancel">Arancel Anual:</label>
                        <input type="number" step="0.01" class="form-control" id="arancel" name="arancel" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="inscripcion">Precio de Inscripción:</label>
                        <input type="number" step="0.01" class="form-control" id="inscripcion" name="inscripcion" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </form>
            </div>
        </div>
    </div>
</div>
