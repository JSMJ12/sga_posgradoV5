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
        <div class="header">
            <img src="{{ public_path() . '/images/unesum.png' }}" alt="University Logo" class="logo">
            <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="University Seal" class="seal"><br>
            <span class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span><br>
            <span class="institute">INSTITUTO DE POSGRADO</span><br>
            <span class="coordinator">COORDINACIÓN DE LA {{ strtoupper($alumno->maestria->nombre) }}</span>
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
                @foreach ($notas as $nota)
                    <tr>
                        <td>{{ $nota->asignatura->nombre }}</td>
                        <td>
                            {{ $nota->asignatura->horas_duracion ?? $nota->asignatura->credito * 48 }}
                        </td>
                        <td>{{ $nota->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p id="fecha-actual">Jipijapa, {{ $fechaActual }}</p>
        <div class="firmante" style="margin-top: 130px; text-align: center;">
            <div
                style="display: inline-block; border-top: 1px solid black; width: fit-content; padding: 0 10px; margin-bottom: 5px;">
                <b>{{ $nombreCompleto }}</b>
            </div>
            <div>Coordinador del Programa de {{ ucfirst(strtolower($alumno->maestria->nombre)) }}</div>
        </div>
        <div id="qr-code">
            <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="Código QR">
        </div>
    </div>
</body>

</html>
