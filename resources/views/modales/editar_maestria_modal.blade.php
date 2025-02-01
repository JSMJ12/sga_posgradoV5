<div class="modal fade" id="editMaestriaModal{{ $maestria->id }}" tabindex="-1" role="dialog" aria-labelledby="editMaestriaModalLabel{{ $maestria->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="editMaestriaModalLabel{{ $maestria->id }}">Editar Maestria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('maestrias.update', $maestria) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="maestria-codigo">Codigo:</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $maestria->codigo }}" required>
                    </div>
                    <div class="form-group">
                        <label for="maestria-nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $maestria->nombre }}" required>
                    </div>
                    <div class="form-group">
                        <label for="coordinador">Coordinador:</label>
                        <select class="form-control" id="coordinador" name="coordinador" required>
                            <option value="">Selecciona un coordinador</option>
                            @foreach($docentes as $docente)
                                <option value="{{ $docente->dni }}" {{ $maestria->coordinador == $docente->dni ? 'selected' : '' }}>
                                    {{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }} {{ $docente->apellidom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="matricula">Precio de Matrícula:</label>
                        <input type="number" step="0.01" class="form-control" id="matricula" name="matricula" value="{{ $maestria->matricula }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="arancel">Arancel Anual:</label>
                        <input type="number" step="0.01" class="form-control" id="arancel" name="arancel" value="{{ $maestria->arancel }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="inscripcion">Precio de Inscripción:</label>
                        <input type="number" step="0.01" class="form-control" id="inscripcion" name="inscripcion" value="{{ $maestria->inscripcion }}" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>
