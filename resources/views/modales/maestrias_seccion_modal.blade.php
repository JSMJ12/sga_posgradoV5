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
                <p><strong>Nombre de la sección:</strong> {{ $seccion->nombre }}</p>

                <hr>
                <h6><strong>Maestrías Asociadas:</strong></h6>

                @if($seccion->maestrias->isEmpty())
                    <p class="text-muted">No hay maestrías asociadas a esta sección.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($seccion->maestrias as $maestria)
                                    <tr>
                                        <td>{{ $maestria->codigo }}</td>
                                        <td>{{ $maestria->nombre }}</td>
                                        <td>
                                            @if($maestria->status === 'ACTIVO')
                                                <span class="badge badge-success">ACTIVO</span>
                                            @else
                                                <span class="badge badge-secondary">INACTIVO</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
