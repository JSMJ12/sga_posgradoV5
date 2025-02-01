@extends('adminlte::page')
@section('title', 'Editar Cohorte')
@section('content_header')
    <h1>Editar Cohorte</h1>
@stop
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header text-center bg-primary text-white">Información de Cohorte</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cohortes.update', $cohorte->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Nombre -->
                            <div class="form-group col-md-6">
                                <label for="nombre"><i class="fas fa-file-signature"></i> Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $cohorte->nombre) }}" required>
                            </div>

                            <!-- Maestría -->
                            <div class="form-group col-md-6">
                                <label for="maestria_id"><i class="fas fa-graduation-cap"></i> Maestría:</label>
                                <select id="maestria_id" class="form-control" name="maestria_id" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    @foreach($maestrias as $maestria)
                                        <option value="{{ $maestria->id }}" {{ old('maestria_id', $cohorte->maestria_id) == $maestria->id ? 'selected' : '' }}>{{ $maestria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Periodo Académico -->
                            <div class="form-group col-md-6">
                                <label for="periodo_academico_id"><i class="fas fa-calendar-alt"></i> Periodo Académico:</label>
                                <select id="periodo_academico_id" class="form-control" name="periodo_academico_id" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    @foreach($periodos_academicos as $periodo_academico)
                                        <option value="{{ $periodo_academico->id }}" {{ old('periodo_academico_id', $cohorte->periodo_academico_id) == $periodo_academico->id ? 'selected' : '' }}>{{ $periodo_academico->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Aforo -->
                            <div class="form-group col-md-6">
                                <label for="aforo"><i class="fas fa-users"></i> Aforo:</label>
                                <input id="aforo" type="number" class="form-control" name="aforo" value="{{ old('aforo', $cohorte->aforo) }}" required>
                            </div>

                            <!-- Modalidad -->
                            <div class="form-group col-md-6">
                                <label for="modalidad"><i class="fas fa-chalkboard"></i> Modalidad:</label>
                                <select id="modalidad" class="form-control" name="modalidad" required>
                                    <option value="">--Seleccione--</option>
                                    <option value="presencial" {{ old('modalidad', $cohorte->modalidad) == 'presencial' ? 'selected' : '' }}>Presencial</option>
                                    <option value="hibrida" {{ old('modalidad', $cohorte->modalidad) == 'hibrida' ? 'selected' : '' }}>Híbrida</option>
                                    <option value="virtual" {{ old('modalidad', $cohorte->modalidad) == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                </select>
                            </div>

                            <!-- Aula -->
                            <div class="form-group col-md-6" id="aula_id">
                                <label for="aula_id"><i class="fas fa-door-open"></i> Aula:</label>
                                <select class="form-control" name="aula_id">
                                    <option value="">--Seleccione--</option>
                                    @foreach($aulas as $aula)
                                        <option value="{{ $aula->id }}" {{ old('aula_id', $cohorte->aula_id) == $aula->id ? 'selected' : '' }}>{{ $aula->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Fecha de Inicio -->
                            <div class="form-group col-md-6">
                                <label for="fecha_inicio"><i class="fas fa-calendar-day"></i> Fecha de Inicio:</label>
                                <input id="fecha_inicio" type="date" class="form-control" name="fecha_inicio" value="{{ old('fecha_inicio', $cohorte->fecha_inicio) }}">
                            </div>

                            <!-- Fecha de Fin -->
                            <div class="form-group col-md-6">
                                <label for="fecha_fin"><i class="fas fa-calendar-check"></i> Fecha de Fin:</label>
                                <input id="fecha_fin" type="date" class="form-control" name="fecha_fin" value="{{ old('fecha_fin', $cohorte->fecha_fin) }}">
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalidadSelect = document.getElementById("modalidad");
        const aulaSelect = document.getElementById("aula_id");

        // Ocultar el campo de selección de aulas si la modalidad es virtual
        if (modalidadSelect.value === "virtual") {
            aulaSelect.style.display = "none";
        }

        modalidadSelect.addEventListener("change", function() {
            if (modalidadSelect.value === "virtual") {
                aulaSelect.style.display = "none";
                aulaSelect.selectedIndex = 0; // Deseleccionar cualquier valor
            } else {
                aulaSelect.style.display = "block"; // Mostrar campo si no es virtual
            }
        });
    });
</script>
@stop
