@extends('adminlte::page')
@section('title', 'Calificar y Agregar Alumnos')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('calificaciones.store') }}" method="POST">
                @csrf
                <input type="hidden" name="docente_dni" value="{{ $docente_dni }}">
                <input type="hidden" name="asignatura_id" value="{{ $asignatura_id }}">
                <input type="hidden" name="cohorte_id" value="{{ $cohorte_id }}">

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Nota Actividades </th>
                                <th>Nota Prácticas </th>
                                <th>Nota Autónomo </th>
                                <th>Examen Final </th>
                                <th>Recuperación</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($alumnos) > 0)
                                @foreach ($alumnos as $alumno)
                                    @php
                                        $nota = $notas->get($alumno->dni);
                                    @endphp
                                    <tr>
                                        <td>{{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}</td>
                                        <input type="hidden" name="alumno_dni[]" value="{{ $alumno->dni }}">

                                        <!-- Actividades (máx 3) -->
                                        <td>
                                            <input class="form-control nota-input" type="number" step="0.01"
                                                name="nota_actividades[{{ $alumno->dni }}]"
                                                value="{{ $nota->nota_actividades ?? '' }}" max="3"
                                                oninput="calcularTotal(this)">
                                        </td>

                                        <!-- Prácticas (máx 3) -->
                                        <td>
                                            <input class="form-control nota-input" type="number" step="0.01"
                                                name="nota_practicas[{{ $alumno->dni }}]"
                                                value="{{ $nota->nota_practicas ?? '' }}" max="3"
                                                oninput="calcularTotal(this)">
                                        </td>

                                        <!-- Autónomo (máx 3) -->
                                        <td>
                                            <input class="form-control nota-input" type="number" step="0.01"
                                                name="nota_autonomo[{{ $alumno->dni }}]"
                                                value="{{ $nota->nota_autonomo ?? '' }}" max="3"
                                                oninput="calcularTotal(this)">
                                        </td>

                                        <!-- Examen Final (máx 4) -->
                                        <td>
                                            <input class="form-control nota-input" type="number" step="0.01"
                                                name="examen_final[{{ $alumno->dni }}]"
                                                value="{{ $nota->examen_final ?? '' }}" max="4"
                                                oninput="calcularTotal(this)">
                                        </td>

                                        <!-- Recuperación (independiente) -->
                                        <td>
                                            <input class="form-control" type="number" step="0.01"
                                                name="recuperacion[{{ $alumno->dni }}]"
                                                value="{{ $nota->recuperacion ?? '' }}" max="4">
                                        </td>

                                        <!-- Total ponderado -->
                                        <td>
                                            <input class="form-control total-input" type="number" step="0.01"
                                                name="total[{{ $alumno->dni }}]" value="{{ $nota->total ?? '' }}"
                                                readonly>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Notas</button>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table th,
        .table td {
            text-align: center;
        }
    </style>
@stop

@section('js')
    <script>
        function calcularTotal(input) {
            var fila = input.closest('tr');

            var notaActividades = parseFloat(fila.querySelector('input[name^="nota_actividades"]').value) || 0;
            var notaPracticas = parseFloat(fila.querySelector('input[name^="nota_practicas"]').value) || 0;
            var notaAutonomo = parseFloat(fila.querySelector('input[name^="nota_autonomo"]').value) || 0;
            var notaExamenFinal = parseFloat(fila.querySelector('input[name^="examen_final"]').value) || 0;

            // Total ponderado según porcentaje
            var total = (notaActividades) + (notaPracticas) + (notaAutonomo) + (notaExamenFinal);

            var totalInput = fila.querySelector('.total-input');
            totalInput.value = total.toFixed(2);

            // Color rojo si supera 10
            if (total > 10) {
                totalInput.style.color = 'red';
                totalInput.style.fontWeight = 'bold';
            } else {
                totalInput.style.color = 'black';
                totalInput.style.fontWeight = 'normal';
            }
        }

        // Ejecutar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.nota-input').forEach(function(input) {
                calcularTotal(input);
            });
        });
    </script>
@stop
