@extends('adminlte::page')

@section('title', 'Crear Nota')

@section('content_header')
    <h1>Calificar a {{ $alumno->nombre1 }} {{ $alumno->nombre2 }} {{ $alumno->apellidop }} {{ $alumno->apellidom }}</h1>
@stop

@section('content')
<style>
    .excel-table {
        background-color: #fff; /* Fondo blanco */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Sombra suave */
        max-width: 100%;
        overflow-x: auto; /* Responsivo */
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    th, td {
        padding: 8px;
        border: 1px solid #ddd;
    }

    input[type="number"] {
        width: 100%;
        text-align: center;
    }

    /* Estilos para que se vea como una hoja de cálculo */
    thead {
        background-color: #f8f9fa;
    }

    tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    @media (max-width: 768px) {
        table {
            font-size: 14px;
        }

        th, td {
            padding: 5px;
        }
    }
</style>

<div class="excel-table">
    <form action="{{ route('notas.store') }}" method="POST">
        @csrf

        <input type="hidden" name="alumno_dni" value="{{ $alumno->dni }}">

        <table>
            <thead>
                <tr>
                    <th>Asignatura</th>
                    <th>Nota Actividades</th>
                    <th>Nota Prácticas</th>
                    <th>Nota Autónomo</th>
                    <th>Examen Final</th>
                    <th>Recuperación</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($asignaturas as $asignatura)
                    <tr>
                        <td>{{ $asignatura->nombre }}</td>
                        <input type="hidden" name="asignatura_id[]" value="{{ $asignatura->id }}">
                        <td>
                            <input type="number" step="0.01" class="nota" name="nota_actividades[{{ $asignatura->id }}]" max="3.0" data-asignatura="{{ $asignatura->id }}" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" class="nota" name="nota_practicas[{{ $asignatura->id }}]" max="3.0" data-asignatura="{{ $asignatura->id }}" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" class="nota" name="nota_autonomo[{{ $asignatura->id }}]" max="3.0" data-asignatura="{{ $asignatura->id }}" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" class="nota" name="examen_final[{{ $asignatura->id }}]" max="3.0" data-asignatura="{{ $asignatura->id }}" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" class="nota" name="recuperacion[{{ $asignatura->id }}]" max="3.0" data-asignatura="{{ $asignatura->id }}">
                        </td>
                        <td>
                            <input type="number" step="0.01" class="total" name="total[{{ $asignatura->id }}]" max="10" readonly>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary mt-3">Agregar</button>
    </form>
</div>

<script>
    // Seleccionamos todos los inputs que pertenecen a la clase "nota"
    document.querySelectorAll('.nota').forEach(input => {
        input.addEventListener('input', function() {
            const asignaturaId = this.getAttribute('data-asignatura');
            calculateTotal(asignaturaId);
        });
    });

    function calculateTotal(asignaturaId) {
        // Obtenemos las notas de las diferentes categorías por asignatura
        const actividades = parseFloat(document.querySelector(`input[name="nota_actividades[${asignaturaId}]"]`).value) || 0;
        const practicas = parseFloat(document.querySelector(`input[name="nota_practicas[${asignaturaId}]"]`).value) || 0;
        const autonomo = parseFloat(document.querySelector(`input[name="nota_autonomo[${asignaturaId}]"]`).value) || 0;
        const examen = parseFloat(document.querySelector(`input[name="examen_final[${asignaturaId}]"]`).value) || 0;
        const recuperacion = parseFloat(document.querySelector(`input[name="recuperacion[${asignaturaId}]"]`).value) || 0;

        // Calculamos el total sumando todas las notas
        const total = actividades + practicas + autonomo + examen + recuperacion;

        // Establecemos el valor total en el campo correspondiente
        document.querySelector(`input[name="total[${asignaturaId}]"]`).value = total.toFixed(2);
    }
</script>
@stop
