@extends('adminlte::page')

@section('title', 'Proceso de Titulación')

@section('content_header')
    <h1 class="text-center text-success">Solicitud de Aprobación de Tema</h1>
@stop

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Listado de Cohortes</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Nombre</th>
                            <th>Periodo Académico</th>
                            <th>Aula</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($cohortes->isNotEmpty())
                            @foreach ($cohortes as $cohorte)
                                <tr>
                                    <td>{{ $cohorte->nombre }}</td>
                                    <td>{{ $cohorte->periodo_academico->nombre }}</td>
                                    <td>
                                        @if ($cohorte->aula && $cohorte->aula->paralelo)
                                            {{ $cohorte->aula->nombre }} - {{ $cohorte->aula->paralelo }}
                                        @else
                                            Sin aula ni paralelo
                                        @endif
                                    </td>
                                    <td>{{ $cohorte->fecha_inicio }}</td>
                                    <td>{{ $cohorte->fecha_fin }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#examenComplexivoModal" data-cohorte-id="{{ $cohorte->id }}"
                                            data-cohorte-nombre="{{ $cohorte->nombre }}">
                                            Examen Complexivo
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-muted text-center">No hay cohortes registradas aún.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @include('modales.asignar_examen_complexivo_modal')
    </div>
@stop


@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Asignar el ID del cohorte y el nombre del cohorte al modal de examen
        var examenModal = document.getElementById('examenComplexivoModal');
        examenModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var cohorteId = button.getAttribute('data-cohorte-id'); // Extrae el ID del cohorte
            var cohorteNombre = button.getAttribute('data-cohorte-nombre'); // Extrae el nombre del cohorte

            // Asigna el ID del cohorte al campo oculto
            var modalCoheteId = examenModal.querySelector('#cohorteId');
            modalCoheteId.value = cohorteId;

            // Asigna el nombre del cohorte al encabezado del modal
            var modalCohorteNombre = examenModal.querySelector('#cohorteNombre');
            modalCohorteNombre.textContent = cohorteNombre;
        });
    </script>
@stop
