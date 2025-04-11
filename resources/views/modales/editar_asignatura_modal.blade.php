@foreach ($maestria->asignaturas as $asignatura)
    <div class="modal fade" id="editAsignaturaModal{{ $asignatura->id }}" tabindex="-1" role="dialog"
        aria-labelledby="editAsignaturaModalLabel{{ $asignatura->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #003366; color: white;">
                    <h5 class="modal-title" id="editAsignaturaModalLabel{{ $asignatura->id }}">Editar Asignatura</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('asignaturas.update', $asignatura->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="maestria_id" value="{{ $maestria->id }}">

                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" class="form-control" name="nombre"
                                value="{{ old('nombre', $asignatura->nombre) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="codigo_asignatura">Código de asignatura:</label>
                            <input type="text" class="form-control" name="codigo_asignatura"
                                value="{{ old('codigo_asignatura', $asignatura->codigo_asignatura) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="credito">Crédito:</label>
                            <input type="number" step="any" class="form-control"
                                id="credito_edit{{ $asignatura->id }}" name="credito"
                                value="{{ old('credito', $asignatura->credito) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="horas_duracion">Horas de Duración:</label>
                            <input type="number" step="any" class="form-control"
                                id="horas_edit{{ $asignatura->id }}" name="horas_duracion"
                                value="{{ old('horas_duracion', $asignatura->horas_duracion) }}">
                        </div>
                        <div class="form-group">
                            <label for="itinerario">Itinerario:</label>
                            <input type="text" class="form-control" name="itinerario"
                                value="{{ old('itinerario', $asignatura->itinerario) }}">
                        </div>
                        <div class="form-group">
                            <label for="unidad_curricular">Unidad Curricular:</label>
                            <input type="text" class="form-control" name="unidad_curricular"
                                value="{{ old('unidad_curricular', $asignatura->unidad_curricular) }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary float-left">Guardar cambios</button>
                    </form>
                    <form action="{{ route('asignaturas.destroy', $asignatura->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger float-right" style="margin-left: 5px"
                            onclick="return confirm('¿Estás seguro de que quieres eliminar esta asignatura?')">{{ __('Eliminar') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const creditoInput = document.getElementById('credito_edit{{ $asignatura->id }}');
            const horasInput = document.getElementById('horas_edit{{ $asignatura->id }}');

            let fromCredito = false;
            let fromHoras = false;

            creditoInput.addEventListener('input', function() {
                if (!fromHoras) {
                    fromCredito = true;
                    const credito = parseFloat(creditoInput.value);
                    if (!isNaN(credito)) {
                        horasInput.value = Math.round(credito * 48);
                    } else {
                        horasInput.value = '';
                    }
                    fromCredito = false;
                }
            });

            horasInput.addEventListener('input', function() {
                if (!fromCredito) {
                    fromHoras = true;
                    const horas = parseFloat(horasInput.value);
                    if (!isNaN(horas)) {
                        creditoInput.value = Math.round(horas / 48);
                    } else {
                        creditoInput.value = '';
                    }
                    fromHoras = false;
                }
            });
        });
    </script>
@endforeach
