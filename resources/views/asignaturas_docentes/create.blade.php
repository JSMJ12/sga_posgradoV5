@extends('adminlte::page')
@section('title', 'Asignatura-Docentes')
@section('content_header')
    <h1>Asignacion de asignaturas</h1>
@stop
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="background-color: #28a745; color: white;">
                    <div class="row">
                        <div class="col-12 text-center mb-4" >
                            <img src="{{ asset('storage/' . $docente->image) }}" alt="Imagen de {{ $docente->name }}" style="max-width: 150px; border-radius: 0.25rem;">
                            <h1 class="h3">{{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }} {{ $docente->apellidom }}</h1>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('asignaturas_docentes.store') }}">
                        @csrf
                        <input type="hidden" name="docente_dni" value="{{ $docente->dni }}">
                        <div class="form-group">
                            <label for="maestria_id">Maestría:</label>
                            <select class="form-control" id="maestria_id" name="maestria_id">
                                <option value="">Seleccione una opción</option>
                                @foreach ($maestrias as $maestria)
                                    <option value="{{ $maestria->id }}">{{ $maestria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Asignaturas:</label><br>
                            <div id="asignaturas-container">
                                @foreach ($asignaturas as $asignatura)
                                    <div class="form-check asignatura-container maestria-{{ $asignatura->maestria_id }}">
                                        <input class="form-check-input" type="checkbox" id="asignatura_{{ $asignatura->id }}" name="asignaturas[]" value="{{ $asignatura->id }}">
                                        <label class="form-check-label" for="asignatura_{{ $asignatura->id }}">{{ $asignatura->nombre }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
    $(function() {
        // Al cambiar la maestría, filtrar las asignaturas
        $('#maestria_id').on('change', function() {
            var maestriaId = $(this).val();

            // Ocultar todas las asignaturas
            $('#asignaturas-container .asignatura-container').hide();

            // Mostrar solo las asignaturas de la maestría seleccionada
            $('#asignaturas-container .maestria-' + maestriaId).show();
        });
    });
</script>
@endsection