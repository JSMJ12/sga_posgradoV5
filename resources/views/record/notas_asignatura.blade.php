<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notas</title>
    <style>
        /* Márgenes iguales en todas las páginas */
        @page {
            margin: 0px 0px 0px 0px;
        }

        body {
            padding: 115px 40px 120px 60px;
        }

        /* Fondo a página completa en todas las hojas */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("{{ public_path('images/fondo-pdf.jpeg') }}");
            background-size: cover;   /* cubre toda la hoja */
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
            table-layout: fixed;
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
            overflow-wrap: break-word;
        }

        .student-info td:first-child,
        .student-info th:first-child {
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
                <li><strong>Maestría:</strong> {{ $cohorte->maestria->nombre }}</li>
                <li><strong>Asignatura:</strong> {{ $asignatura->nombre }}</li>
                @if ($aula)
                    <li><strong>Aula:</strong> {{ $aula->nombre }}</li>
                @endif
                @if ($paralelo)
                    <li><strong>Paralelo:</strong> {{ $paralelo }}</li>
                @endif
                <li><strong>Periodo:</strong> {{ $periodo_academico->nombre }}</li>
                <li><strong>Cohorte:</strong> {{ $cohorte->nombre }}</li>
                <li><strong>Docente:</strong> {{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }}</li>
            </ul>
        </div>

        <table class="student-info">
            <thead>
                <tr>
                    <th style="width: 20%;">Alumno</th>
                    <th style="width: 10%;">Céd./Pas.</th>
                    <th style="width: 10%;">Actividades</th>
                    <th style="width: 10%;">Prácticas</th>
                    <th style="width: 10%;">Autónomo</th>
                    <th style="width: 10%;">Examen Final</th>
                    <th style="width: 10%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumnosMatriculados->sortBy(fn($a) => $a->apellidop . ' ' . $a->nombre1) as $alumno)
                    <tr>
                        <td>
                            {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}
                            {{ $alumno->apellidop }} {{ $alumno->apellidom }}
                        </td>
                        <td>{{ $alumno->dni }}</td>

                        @php
                            $nota = $alumno->notas
                                ->where('asignatura_id', $asignatura->id)
                                ->where('alumno_id', $alumno->id)
                                ->where('docente_dni', $docente->dni)
                                ->first();
                        @endphp

                        <td>{{ $nota->nota_actividades ?? '--' }}</td>
                        <td>{{ $nota->nota_practicas ?? '--' }}</td>
                        <td>{{ $nota->nota_autonomo ?? '--' }}</td>
                        <td>{{ $nota->examen_final ?? '--' }}</td>
                        <td>{{ $nota->total ?? '--' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="firma-container">
            <div class="firma-box">
                <div class="firma-line"></div>
                <div class="firma-nombre">{{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }}</div>
                <div>{{ $docente->dni }}</div>
            </div>
        </div>

    </div>
</body>
</html>
