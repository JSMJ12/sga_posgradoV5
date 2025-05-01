<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @page {
            margin: 2.54cm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            position: relative;
            margin-top: -60px;
        }

        .logo {
            width: 64px;
            position: absolute;
            top: -25px;
            left: 0;
        }

        .seal {
            width: 104px;
            position: absolute;
            top: -25;
            right: 0;
        }

        .university-name {
            font-size: 14pt;
            font-weight: bold;
        }

        .institute {
            font-size: 10pt;
        }

        .program-title {
            font-size: 10pt;
            margin-top: 5px;
        }

        .divider {
            width: 100%;
            height: 2px;
            background-color: goldenrod;
            margin: 10px 0;
        }

        .certificate-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }

        .certificate-details {
            font-size: 12pt;
            text-align: justify;
            margin: 10px 0;
        }

        .firmante {
            margin-top: 40px;
            text-align: center;
        }

        .firmante b {
            display: block;
            margin-bottom: 2px;
        }

        .student-info {
            font-size: 10pt;
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        .student-info th,
        .student-info td {
            border: 1px solid #666;
            padding: 5px;
        }

        .student-info th {
            background-color: #ccc;
        }

        .footer {
            font-size: 10pt;
            text-align: right;
            margin-top: 10px;
        }

        #qr-code {
            position: absolute;
            bottom: -50px;
            right: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="{{ public_path() . '/images/unesum.png' }}" alt="University Logo" class="logo">
        <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="University Seal" class="seal"><br>
        <div class="header">
            <span class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span><br>
            <span class="institute">INSTITUTO DE POSGRADO</span><br>
            <div class="coordinator" style="margin-top: 5px; font-size: 11pt;">
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
        <p class="certificate-title">CERTIFICA</p>
        <p class="certificate-details">Que de acuerdo a los registros que reposan en la Secretaría Académica de
            la Coordinación de la {{ $alumno->maestria->nombre }} de la Universidad Estatal del Sur de Manabí, se
            desarrolló al {{ $numeroRomano }} PROGRAMA
            DE LA {{ strtoupper($alumno->maestria->nombre) }}, inició sus actividades
            académicas el
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_inicio)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') }}
            y culminó
            el
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_fin)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') }},
            con una modalidad {{ $cohorte->modalidad }}, con un total de {{ $totalHoras }} horas, según plan
            curricular.</p>
        <p class="certificate-details">En los archivos de esta maestría consta: {{ $alumno->apellidop }}
            {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}, con número de Matrícula Nº
            {{ $alumno->registro }}, quien aprobó todos los módulos contemplados en este programa, de acuerdo al
            siguiente detalle:</p>
        <table class="student-info" style="width: 80%; margin: 0 auto; font-size: 10pt;">
            <col width="60%">
            <col width="40%">
            <col width="40%">
            <thead class="thead-dark">
                <tr>
                    <th>ASIGNATURA</th>
                    <th>TOTAL HORAS</th>
                    <th>PROMEDIO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notasCompletas  as $nota)
                    <tr>
                        <td>{{ $nota->asignatura->nombre }}</td>
                        <td>
                            {{ $nota->asignatura->horas_duracion ?? $nota->asignatura->credito * 48 }}
                        </td>
                        <td>{{ $nota->total ?? '--' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p id="fecha-actual">Jipijapa, {{ $fechaActual }}</p>
        <div style="margin-top: 100px; text-align: center;">
            <div
                style="border-top: 1px solid black; display: inline-block; padding: 5px 20px; font-weight: bold; text-transform: uppercase;">
                {{ strtoupper($nombreCompleto) }}
            </div>
            <div style="margin-top: 5px; font-weight: normal; font-size: 9pt; text-transform: uppercase;">
                COORDINADOR DEL PROGRAMA DE {{ strtoupper($alumno->maestria->nombre) }}
            </div>
        </div>
    </div>
</body>

</html>
