<div class="modal fade" id="mostrarSeccionModal_{{ $secretario->seccion->id }}" tabindex="-1" role="dialog" aria-labelledby="mostrarSeccionModalLabel_{{ $secretario->seccion->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="mostrarSeccionModalLabel_{{ $secretario->seccion->id }}">Información de la Sección</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre de la sección:</strong> {{ $secretario->seccion->nombre }}</p>
                <p><strong>Maestrías asociadas:</strong></p>
                <ul>
                    @foreach ($secretario->seccion->maestrias as $maestria)
                        <li>{{ $maestria->nombre }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>