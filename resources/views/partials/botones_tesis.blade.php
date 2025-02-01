<div style="display: flex; gap: 10px; align-items: center;">
    @if ($tesis->estado === 'aprobado')
        @if ($tesis->tutor_dni === null)
            <button class="btn btn-outline-success btn-sm" onclick="mostrarModalAsignarTutor({{ $tesis->id }})">
                <i class="fas fa-user-plus"></i> Asignar Tutor
            </button>
        @else
            <button class="btn btn-outline-danger btn-sm" onclick="mostrarModalAsignarTutor({{ $tesis->id }})">
                <i class="fas fa-user-edit"></i> Actualizar Tutor
            </button>
        @endif
    @else
        <button class="btn btn-outline-info btn-sm" onclick="verSolicitud('{{ asset('storage/' . $tesis->solicitud_pdf) }}')">
            <i class="fas fa-eye"></i>
        </button>
        <button class="btn btn-outline-success btn-sm" onclick="aceptarTema({{ $tesis->id }})">
            <i class="fas fa-check"></i>
        </button>
        <button class="btn btn-outline-danger btn-sm" onclick="rechazarTema({{ $tesis->id }})">
            <i class="fas fa-times"></i>
        </button>
    @endif
</div>
