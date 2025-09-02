<div style="display: flex; gap: 10px; align-items: center;">
    <a href="{{ route('certificar.alumno', $item->id) }}" class="btn btn-success" target="_blank">
        <i class="fas fa-download"></i> Certificado
    </a>

    <form action="{{ route('titulacion_alumno.store') }}" method="POST" id="titularForm_{{ $item->id }}">
        @csrf
        <input type="hidden" name="tesis_id" value="{{ $item->id }}">
        <input type="hidden" name="titulado" value="1">
        <button type="button" class="btn btn-danger btn-sm" id="titularBtn_{{ $item->id }}"
                onclick="confirmTitularAlumno('{{ $item->id }}')">
            <i class="fas fa-user-graduate"></i> Titular Alumno
        </button>
    </form>
</div>
