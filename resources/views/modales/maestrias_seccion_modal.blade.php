<div class="modal fade" id="maestriasModal{{ $seccion->id }}" tabindex="-1" role="dialog"
    aria-labelledby="maestriasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="maestriasModalLabel">Maestrías de {{ $seccion->nombre }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @foreach ($seccion->maestrias as $maestria)
                    {{ $maestria->nombre }}
                    @if (!$loop->last)
                        ,
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
