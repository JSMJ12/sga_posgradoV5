@extends('adminlte::page')

@section('title', 'Notas de Alumnos')

@section('content_header')
    <h1>Notas de Alumnos</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Notas</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablaNotas">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>Docente</th>
                                <th>Asignatura</th>
                                <th>Actividades de Aprendizaje</th>
                                <th>Prácticas de Aplicación</th>
                                <th>Aprendizaje Autónomo</th>
                                <th>Examen Final</th>
                                <th>Recuperación</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notasData as $asignatura => $nota)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <img src="{{ asset('storage/' . $nota['docente_image']) }}"
                                                 alt="Imagen del Docente"
                                                 style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                                            <span>{{ $nota['docente_nombre'] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $asignatura }}</td>
                                    <td>{{ $nota['nota_actividades'] }}</td>
                                    <td>{{ $nota['nota_practicas'] }}</td>
                                    <td>{{ $nota['nota_autonomo'] }}</td>
                                    <td>{{ $nota['examen_final'] }}</td>
                                    <td>{{ $nota['recuperacion'] }}</td>
                                    <td>{{ $nota['total'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#tablaNotas').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
                }
            });
        });
    </script>
@stop
