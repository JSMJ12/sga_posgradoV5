<div class="modal fade" id="sendMessageModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="sendMessageModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="sendMessageModalLabel{{ $user->id }}">Enviar mensaje a {{ $user->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario de envío de mensaje -->
                <form id="sendMessageForm{{ $user->id }}" action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Campos del formulario -->
                    <div class="form-group">
                        <label for="message">Mensaje</label>
                        <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Adjunto</label>
                        <input type="file" class="form-control-file" id="attachment" name="attachment">
                    </div>
                    <!-- Campo oculto para receiver_id -->
                    <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                    <!-- Fin de campos del formulario -->
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>