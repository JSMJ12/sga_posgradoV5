<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 10pt;
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
            background-image: url("{{ public_path('images/fondopdf.png') }}");
            background-size: 100% 100%;
            background-position: top left;
            background-repeat: no-repeat;
        }

        .container {
            width: 85%;
            margin: 190px auto 80px auto; /* 🔽 Bajado 15px (antes era 160px) */
            padding-left: 0;
            text-align: justify;
            line-height: 1.8;
        }


        .program-title {
            font-size: 9pt;
            margin-top: 5px;
        }


        .certificate-title {
            font-size: 10.5pt;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }

        .certificate-details {
            font-size: 10pt;
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
            font-size: 9pt;
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        .student-info th,
        .student-info td {
            border: 1px solid #666;
            padding: 4px;
        }

        .student-info th {
            background-color: #ccc;
        }

        .footer {
            font-size: 9pt;
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
        <table class="student-info" style="width: 80%; margin: 0 auto; font-size: 8pt;">
            <thead class="thead-dark">
                <tr>
                    <th style="font-size: 8pt;">ASIGNATURA</th>
                    <th style="font-size: 8pt;">TOTAL HORAS</th>
                    <th style="font-size: 8pt;">PROMEDIO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notasCompletas  as $nota)
                    <tr>
                        <td style="font-size: 8pt;">{{ $nota->asignatura->nombre }}</td>
                        <td style="font-size: 8pt;">
                            {{ $nota->asignatura->horas_duracion ?? $nota->asignatura->credito * 48 }}
                        </td>
                        <td style="font-size: 8pt;">{{ $nota->total ?? '--' }}</td>
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
