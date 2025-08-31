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
                                <th>% Recup.</th>
                                <th>Recuperación</th>
                                <th>Total</th>
                                <th>Final</th>
                                <th>Obs.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notasData as $asignatura => $nota)
                                @php
                                    // Notas base convertidas a float
                                    $actividades = isset($nota['nota_actividades']) ? (float)$nota['nota_actividades'] : null;
                                    $practicas = isset($nota['nota_practicas']) ? (float)$nota['nota_practicas'] : null;
                                    $autonomo = isset($nota['nota_autonomo']) ? (float)$nota['nota_autonomo'] : null;
                                    $examen_final = isset($nota['examen_final']) ? (float)$nota['examen_final'] : null;
                                    $recuperacion = isset($nota['recuperacion']) ? (float)$nota['recuperacion'] : null;

                                    $porcentaje_recuperacion = $recuperacion !== null ? ($recuperacion * 10) . '%' : '--';

                                    // Total sin recuperación
                                    $total = ($actividades ?? 0) + ($practicas ?? 0) + ($autonomo ?? 0) + ($examen_final ?? 0);

                                    // Calcular nota final con recuperación
                                    $calificacion_final = $total;
                                    if ($recuperacion !== null && $recuperacion > 0) {
                                        $campos = [
                                            'actividades' => $actividades ?? 0,
                                            'practicas' => $practicas ?? 0,
                                            'autonomo' => $autonomo ?? 0,
                                            'examen_final' => $examen_final ?? 0,
                                        ];
                                        $minKey = array_keys($campos, min($campos))[0];
                                        if ($recuperacion > $campos[$minKey]) {
                                            $campos[$minKey] = $recuperacion;
                                        }
                                        $calificacion_final = array_sum($campos);
                                    }

                                    // Si no hay ninguna nota registrada → Observación = "--"
                                    $tieneNotas = $actividades !== null || $practicas !== null || $autonomo !== null || $examen_final !== null || $recuperacion !== null;
                                    $observacion = $tieneNotas ? ($calificacion_final >= 7 ? 'Aprobado' : 'Reprobado') : '--';
                                @endphp

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
                                    <td>{{ $actividades !== null ? number_format($actividades, 2) : '--' }}</td>
                                    <td>{{ $practicas !== null ? number_format($practicas, 2) : '--' }}</td>
                                    <td>{{ $autonomo !== null ? number_format($autonomo, 2) : '--' }}</td>
                                    <td>{{ $examen_final !== null ? number_format($examen_final, 2) : '--' }}</td>
                                    <td>{{ $porcentaje_recuperacion }}</td>
                                    <td>{{ $recuperacion !== null ? number_format($recuperacion, 2) : '--' }}</td>
                                    <td>{{ $tieneNotas ? number_format($total, 2) : '--' }}</td>
                                    <td>{{ $tieneNotas ? number_format($calificacion_final, 2) : '--' }}</td>
                                    <td>{{ $observacion }}</td>
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
