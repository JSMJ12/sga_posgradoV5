<div class="modal fade" id="modalSilabo_{{ $asignatura['id'] }}" tabindex="-1" aria-labelledby="modalLabelSilabo_{{ $asignatura['id'] }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="modalLabelSilabo_{{ $asignatura['id'] }}">Subir Sílabo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('updateSilabo') }}" method="POST" class="form-silabo" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="asignatura_id" value="{{ $asignatura['id'] }}">
                    <div class="mb-3">
                        <label for="silabo_{{ $asignatura['id'] }}" class="form-label">Subir Sílabo</label>
                        <input type="file" id="silabo_{{ $asignatura['id'] }}" name="silabo" class="form-control">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
