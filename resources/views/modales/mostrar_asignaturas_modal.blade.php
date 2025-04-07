<!-- Modal de Asignaturas -->
<div class="modal fade" id="asignaturasModal{{ $maestria->id }}" tabindex="-1" role="dialog" aria-labelledby="asignaturasModalLabel{{ $maestria->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="asignaturasModalLabel{{ $maestria->id }}">Asignaturas de {{ $maestria->nombre }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($maestria->asignaturas->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($maestria->asignaturas as $asignatura)
                                <tr>
                                    <td>{{ $asignatura->nombre }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editAsignaturaModal{{ $asignatura->id }}" title="Editar Asignatura">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No hay asignaturas</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@include('modales.editar_asignatura_modal')