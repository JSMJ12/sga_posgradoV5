<div class="modal fade" id="addAsignaturaModal{{ $maestria->id }}" tabindex="-1" role="dialog"
    aria-labelledby="addAsignaturaModalLabel{{ $maestria->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="addAsignaturaModalLabel{{ $maestria->id }}">Crear Asignatura</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('asignaturas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="codigo_asignatura">Código de Asignatura:</label>
                        <input type="text" class="form-control" id="codigo_asignatura" name="codigo_asignatura"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="credito">Crédito:</label>
                        <input type="number" step="any" class="form-control" id="credito{{ $maestria->id }}"
                            name="credito">
                    </div>
                    <div class="form-group">
                        <label for="horas_duracion">Horas de Duración:</label>
                        <input type="number" step="any" class="form-control" id="horas_duracion{{ $maestria->id }}"
                            name="horas_duracion">
                    </div>
                    <div class="form-group">
                        <label for="itinerario">Itinerario:</label>
                        <input type="text" class="form-control" id="itinerario" name="itinerario">
                    </div>
                    <div class="form-group">
                        <label for="unidad_curricular">Unidad Curricular:</label>
                        <input type="text" class="form-control" id="unidad_curricular" name="unidad_curricular">
                    </div>

                    <input type="hidden" name="maestria_id" value="{{ $maestria->id }}">
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const creditoInput = document.getElementById('credito{{ $maestria->id }}');
        const horasInput = document.getElementById('horas_duracion{{ $maestria->id }}');

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
