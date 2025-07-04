<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>RECORD ACADÉMICO</title>
    <style>
        @page {
            margin: 2.54cm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 9pt;
            margin: 40px;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .header {
            text-align: center;
            position: relative;
            margin-bottom: 10px;
            margin-top: -60px;
        }

        .logo {
            width: 60px;
            position: absolute;
            top: -30px;
            left: 0;
        }

        .seal {
            width: 100px;
            position: absolute;
            top: -35;
            right: 0;
        }

        .university-name {
            font-size: 11pt;
            font-weight: bold;
        }

        .institute,
        .coordinator {
            font-size: 9pt;
        }

        .divider {
            width: 100%;
            height: 2px;
            background-color: goldenrod;
            margin: 10px 0;
        }

        .certificate-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .student-details {
            margin-bottom: 15px;
            line-height: 1.4;
            font-size: 9pt;
        }

        .student-info {
            width: 100%;
            font-size: 8.5pt;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .student-info th,
        .student-info td {
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
        }

        .student-info th {
            background-color: #f0f0f0;
        }

        #fecha-actual {
            text-align: right;
            margin-top: 30px;
            font-size: 9pt;
        }

        .firmante {
            margin-top: 40px;
            text-align: center;
        }

        .firmante b {
            display: block;
            margin-bottom: 2px;
        }

        #qr-code {
            position: absolute;
            bottom: 40px;
            right: 40px;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="{{ public_path() . '/images/unesum.png' }}" alt="Logo" class="logo">
        <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="Sello" class="seal">
        <div class="header" style="text-align: center;">
            <div class="university-name" style="font-weight: bold;">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</div>
            <div class="institute" style="font-weight: bold;">INSTITUTO DE POSGRADO</div>

            <div class="coordinator" style="margin-top: 5px;">
                COORDINACIÓN DE LA
                @php
                    $nombreMaestria = strtoupper($alumno->maestria->nombre);
                    $palabras = explode(' ', $nombreMaestria);
                    $lineas = [];
                    $lineaActual = '';

                    foreach ($palabras as $palabra) {
                        if (strlen($lineaActual . ' ' . $palabra) <= 40) {
                            $lineaActual .= ($lineaActual ? ' ' : '') . $palabra;
                        } else {
                            $lineas[] = $lineaActual;
                            $lineaActual = $palabra;
                        }
                    }
                    if ($lineaActual) {
                        $lineas[] = $lineaActual;
                    }
                @endphp

                @foreach ($lineas as $linea)
                    <div style="text-transform: uppercase;">{{ $linea }}</div>
                @endforeach
            </div>
        </div>


        <div class="divider"></div>

        <p class="certificate-title">RECORD ACADÉMICO</p>

        <div class="student-details">
            <strong>Estudiante:</strong> {{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }}
            {{ $alumno->nombre2 }}<br>
            <strong>Cédula/Pasaporte:</strong> {{ $alumno->dni }}<br>
            <strong>Período académico:</strong> {{ $cohorte->periodo_academico->nombre }}<br>
            <strong>Cohorte:</strong> {{ $cohorte->nombre }}<br>
            <strong>Modalidad:</strong> {{ $cohorte->modalidad }}<br>
            <strong>Fecha de inicio:</strong>
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_inicio)->translatedFormat('d \d\e F \d\e Y') }}<br>
            <strong>Fecha de fin:</strong>
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_fin)->translatedFormat('d \d\e F \d\e Y') }}

        </div>

        <table class="student-info">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>ASIGNATURA</th>
                    <th>TOTAL HORAS</th>
                    <th>PROMEDIO</th>
                    <th>ESTADO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notasCompletas as $index => $nota)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $nota->asignatura->nombre }}</td>
                        <td>{{ $nota->asignatura->horas_duracion ?? $nota->asignatura->credito * 48 }}</td>
                        <td>{{ $nota->total ?? '--' }}</td>
                        <td>
                            @if (is_null($nota->total))
                                {{ '--' }}
                            @elseif ($nota->total >= 7)
                                APROBADO
                            @else
                                REPROBADO
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top: 10px;">
            <strong>Total de horas:</strong> {{ $totalHoras }}<br>
            <strong>Total de asignaturas aprobadas:</strong> {{ $cantidadAsignaturas }}<br>
            <strong>Promedio general:</strong> {{ $promedio ? number_format($promedio, 2) : 'N/A' }}
        </p>

        <p id="fecha-actual">Jipijapa, {{ $fechaActual }}</p>

        <table style="width:100%; margin-top: 30px; border-collapse: collapse; font-size:8pt;">
            <tr>
                <!-- ELABORADO POR -->
                <td style="border:1px solid #000; padding:10px; width:33%; vertical-align:top; text-align:center;">
                    <b style="font-size:8pt;">Elaborado por:</b><br><br>
                    <hr style="margin: 8px 20px; border: none; border-top: 1px solid #000;">
                    @if (isset($secretario))
                        <span style="text-transform:uppercase; font-size:6pt;">{{ $secretario->full_name }}</span><br>
                    @else
                        <span style="font-size:6pt;">--</span><br>
                    @endif
                    <span style="font-size:7pt; font-weight:bold;">SECRETARIA ACADÉMICA</span>
                </td>

                <!-- REVISADO POR -->
                <td style="border:1px solid #000; padding:10px; width:33%; vertical-align:top; text-align:center;">
                    <b style="font-size:8pt;">Revisado por:</b><br><br>
                    <hr style="margin: 8px 20px; border: none; border-top: 1px solid #000;">
                    <span style="text-transform:uppercase; font-size:6pt;">{{ strtoupper($nombreCompleto) }}</span><br>
                    <span style="font-size:7pt; font-weight:bold;">COORDINADOR(A) DEL PROGRAMA</span>
                </td>

                <!-- APROBADO POR -->
                <td style="border:1px solid #000; padding:10px; width:34%; vertical-align:top; text-align:center;">
                    <b style="font-size:8pt;">Aprobado por:</b><br><br>
                    <hr style="margin: 8px 20px; border: none; border-top: 1px solid #000;">
                    @if (isset($directorDocente))
                        <span
                            style="text-transform:uppercase; font-size:6pt;">{{ $directorDocente->full_name }}</span><br>
                    @else
                        <span style="font-size:6pt;">Ing. Leopoldo Venegas Loor, PhD</span><br>
                    @endif
                    <span style="font-size:7pt; font-weight:bold;">DIRECTOR DEL INSTITUTO DE POSGRADO</span>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>
