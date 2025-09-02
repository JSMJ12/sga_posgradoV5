<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notas</title>
    <style>
        /* MÃ¡rgenes iguales en todas las pÃ¡ginas */
        @page {
            margin: 0px 0px 0px 0px;
        }

        body {
            padding: 115px 40px 120px 60px;
        }

        /* Fondo a pÃ¡gina completa en todas las hojas */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("{{ public_path('images/fondo-pdf.jpeg') }}");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            z-index: -1;
        }

        .container { 
            width: 90%; 
            text-align: justify; 
            line-height: 1.5; 
        }

        .info-header {
            text-align: left;
            font-size: 12pt;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .info-header strong {
            margin-right: 10px;
        }

        .student-info {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            table-layout: auto;   /* ahora se ajusta al contenido */
            word-wrap: break-word;
        }

        .student-info thead {
            background-color: #f0f0f0;
        }

        .student-info th,
        .student-info td {
            border: 1px solid #333;
            padding: 5px;
            text-align: center;
        }

        .student-info th:first-child,
        .student-info td:first-child {
            text-align: left;
        }

        .firma-container {
            margin-top: 60px;
            display: flex;
            justify-content: flex-end;
        }

        .firma-box {
            width: 40%;
            text-align: center;
        }

        .firma-line {
            border-top: 1px solid #000;
            margin: 30px 0 5px 0;
        }

        .firma-nombre {
            font-weight: bold;
            font-size: 11pt;
        }
    </style>

</head>

<body>
    <div class="container">

        <div class="info-header">
            <ul style="list-style: none; padding: 0; margin: 0; font-size: 11pt; line-height: 1.6;">
                <li><strong>MaestrÃ­a:</strong> {{ $cohorte->maestria->nombre }}</li>
                <li><strong>Asignatura:</strong> {{ $asignatura->nombre }}</li>
                @if ($aula)
                    <li><strong>Aula:</strong> {{ $aula->nombre }}</li>
                @endif
                @if ($paralelo)
                    <li><strong>Paralelo:</strong> {{ $paralelo }}</li>
                @endif
                <li><strong>Periodo:</strong> {{ $periodo_academico->nombre }}</li>
                <li><strong>Cohorte:</strong> {{ $cohorte->nombre }}</li>
		<li><strong>Modalidad:</strong> {{ \Illuminate\Support\Str::ucfirst($cohorte->modalidad) }}</li>
                <li><strong>Docente:</strong> {{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }}</li>
            </ul>
        </div>

        <table class="student-info">
            <thead>
                <tr>
                    <th>Ced./Pas.</th>                  
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th>Acti. Ap</th>
                    <th>Pract.</th>
                    <th>Autón.</th>
                    <th>Ex. Final</th>
                    <th>% Recup.</th>
                    <th>Recup.</th>
                    <th>Total</th>
                    <th>Final</th>
                    <th>Obs.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumnosMatriculados->sortBy(fn($a) => $a->apellidop . ' ' . $a->nombre1) as $alumno)
                    <tr>
                        <td>{{ $alumno->dni }}</td>
			            <td>{{ $alumno->apellidop }} {{ $alumno->apellidom }}</td>
                        <td>{{ $alumno->nombre1 }} {{ $alumno->nombre2 }}</td>
                        @php
                            $nota = $alumno->notas
                                ->where('asignatura_id', $asignatura->id)
                                ->where('alumno_id', $alumno->id)
                                ->where('docente_dni', $docente->dni)
                                ->first();

                            $actividades = $nota->nota_actividades ?? 0;
                            $practicas = $nota->nota_practicas ?? 0;
                            $autonomo = $nota->nota_autonomo ?? 0;
                            $examen_final = $nota->examen_final ?? 0;
                            $recuperacion = $nota->recuperacion ?? null;
                            $porcentaje_recuperacion = $nota->porcentaje_recuperacion ?? null;

                            $total = $actividades + $practicas + $autonomo + $examen_final;

                            $campos = [
                                'actividades' => $actividades,
                                'practicas' => $practicas,
                                'autonomo' => $autonomo,
                                'examen_final' => $examen_final,
                            ];

                            $calificacion_final = $total;
                            if ($recuperacion !== null && $recuperacion > 0) {
                                $minKey = array_keys($campos, min($campos))[0];
                                if ($recuperacion > $campos[$minKey]) {
                                    $campos[$minKey] = $recuperacion;
                                }
                                $calificacion_final = array_sum($campos);
                            }

                            $observacion = $calificacion_final >= 7 ? 'Aprobado' : 'Reprobado';
                        @endphp

                        <td>{{ $actividades }}</td>
                        <td>{{ $practicas }}</td>
                        <td>{{ $autonomo }}</td>
                        <td>{{ $examen_final }}</td>
                        <td>{{ $recuperacion * 10 ?? '--' }}</td>
                        <td>{{ $recuperacion ?? '--' }}</td>
                        <td>{{ $total }}</td>
                        <td>{{ $calificacion_final }}</td>
                        <td>{{ $observacion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Glosario -->
        <div style="margin-top: 30px; font-size: 7pt;">
            <ul style="list-style: none; padding-left: 0;">
                <li><strong>Acti. Ap:</strong> Actividades de aprendizaje en el aula.</li>
                <li><strong>Práct.:</strong> Trabajos prácticos, experimentales y pruebas escritas.</li>
                <li><strong>Autón.:</strong> Actividades de aprendizaje autónomo.</li>
                <li><strong>Ex. Final:</strong> Examen final.</li>
            </ul>
        </div>

        <div class="firma-container" style="margin-top: 80px; width: 100%;">
            <table style="width: 100%; border: none; text-align: center; font-size: 11pt;">
                <tr>
                    <!-- Firma Docente -->
                    <td style="width: 50%; padding: 10px;">
                        <div style="border-top: 1px solid black; display: inline-block; padding: 0 10px; font-size: 12pt;">
                            <b>{{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }} {{ $docente->apellidom }}</b>
                        </div>
                        <br>
                        <div style="display: inline-block; padding: 0 10px; font-size: 10pt; text-transform: uppercase; font-weight: bold;">
                            C.I. {{ $docente->dni }}<br>
                            FIRMA DEL DOCENTE
                        </div>
                    </td>

                    <!-- Firma Coordinador -->
                    <td style="width: 50%; padding: 10px;">
                        <div style="border-top: 1px solid black; display: inline-block; padding: 0 10px; font-size: 12pt;">
                            <b>{{ $nombreCompleto }}</b>
                        </div>
                        <br>
                        <div style="display: inline-block; padding: 0 10px; font-size: 10pt; text-transform: uppercase; font-weight: bold;">
                            COORDINADOR DEL PROGRAMA DE {{ strtoupper($maestria->nombre) }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>