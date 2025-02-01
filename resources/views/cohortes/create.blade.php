@extends('adminlte::page')
@section('title', 'Crear Cohorte')
@section('content_header')
    <h1>Crear Cohorte</h1>
@stop
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header text-center bg-success text-white">Información de Cohorte</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cohortes.store') }}">
                        @csrf
                        <div class="row">
                            <!-- Nombre -->
                            <div class="form-group col-md-6">
                                <label for="nombre"><i class="fas fa-file-signature"></i> Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required>
                            </div>

                            <!-- Maestría -->
                            <div class="form-group col-md-6">
                                <label for="maestria_id"><i class="fas fa-graduation-cap"></i> Maestría:</label>
                                <select id="maestria_id" class="form-control" name="maestria_id" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    @foreach($maestrias as $maestria)
                                        <option value="{{ $maestria->id }}">{{ $maestria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Periodo Académico -->
                            <div class="form-group col-md-6">
                                <label for="periodo_academico_id"><i class="fas fa-calendar-alt"></i> Periodo Académico:</label>
                                <select id="periodo_academico_id" class="form-control" name="periodo_academico_id" required>
                                    <option value="" selected disabled>-- Seleccione una opción --</option>
                                    @foreach($periodos_academicos as $periodo_academico)
                                        <option value="{{ $periodo_academico->id }}">{{ $periodo_academico->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Aforo -->
                            <div class="form-group col-md-6">
                                <label for="aforo"><i class="fas fa-users"></i> Aforo:</label>
                                <input id="aforo" type="number" class="form-control" name="aforo" value="{{ old('aforo') }}" required>
                            </div>

                            <!-- Modalidad -->
                            <div class="form-group col-md-6">
                                <label for="modalidad"><i class="fas fa-chalkboard"></i> Modalidad:</label>
                                <select id="modalidad" class="form-control" name="modalidad" required>
                                    <option value="">--Seleccione--</option>
                                    <option value="presencial">Presencial</option>
                                    <option value="hibrida">Híbrida</option>
                                    <option value="virtual">Virtual</option>
                                </select>
                            </div>

                            <!-- Aula -->
                            <div class="form-group col-md-6" id="aula_id">
                                <label for="aula_id"><i class="fas fa-door-open"></i> Aula:</label>
                                <select class="form-control" name="aula_id">
                                    <option value="">--Seleccione--</option>
                                    @foreach($aulas as $aula)
                                        <option value="{{ $aula->id }}">{{ $aula->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Fecha de Inicio -->
                            <div class="form-group col-md-6">
                                <label for="fecha_inicio"><i class="fas fa-calendar-day"></i> Fecha de Inicio:</label>
                                <input id="fecha_inicio" type="date" class="form-control" name="fecha_inicio">
                            </div>

                            <!-- Fecha de Fin -->
                            <div class="form-group col-md-6">
                                <label for="fecha_fin"><i class="fas fa-calendar-check"></i> Fecha de Fin:</label>
                                <input id="fecha_fin" type="date" class="form-control" name="fecha_fin">
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Crear</button>
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

        // Ocultar el campo de selección de aulas al cargar la página
        aulaSelect.style.display = "none";

        modalidadSelect.addEventListener("change", function() {
            if (modalidadSelect.value === "virtual") {
                // Si la modalidad es virtual, ocultar el campo de selección de aulas
                aulaSelect.style.display = "none";
                aulaSelect.selectedIndex = 0; // Deseleccionar cualquier valor
            } else {
                aulaSelect.style.display = "block"; // Mostrar campo si no es virtual
            }
        });
    });
</script>
@stop
