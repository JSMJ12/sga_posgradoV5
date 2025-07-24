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

        html, body {
            font-family: "Times New Roman", serif;
            font-size: 10pt;
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
            background-image: url("{{ public_path('images/fondo-pdf.jpeg') }}");
            background-size: 98% 98%;
            background-position: top left;
            background-repeat: no-repeat;
        }

        .container {
            width: 80%;
            margin: 120px auto 100px auto; /* margen superior e inferior */
            text-align: justify;
            line-height: 1.8;
        }

        .certificate-title {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }

        .certificate-details {
            font-size: 10pt;
            text-align: justify;
            margin: 10px 0;
        }

        .student-info {
            font-size: 8pt;
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }

        .student-info th,
        .student-info td {
            border: 1px solid #666;
            padding: 4px;
            text-align: center;
        }

        .student-info th {
            background-color: #ccc;
        }

        #fecha-actual {
            text-align: right;
            margin-top: 20px;
            font-size: 9pt;
        }

        .firma {
            margin-top: 80px;
            text-align: center;
        }

        .firma-nombre {
            border-top: 1px solid black;
            display: inline-block;
            padding: 5px 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .firma-cargo {
            margin-top: 5px;
            font-weight: normal;
            font-size: 9pt;
            text-transform: uppercase;
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

        <p class="certificate-details">
            Que de acuerdo a los registros que reposan en la Secretaría Académica de
            la Coordinación de la {{ $alumno->maestria->nombre }} de la Universidad Estatal del Sur de Manabí, se
            desarrolló al {{ $numeroRomano }} PROGRAMA
            DE LA {{ strtoupper($alumno->maestria->nombre) }}, inició sus actividades académicas el
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_inicio)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') }}
            y culminó el
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_fin)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') }},
            con una modalidad {{ $cohorte->modalidad }}, con un total de {{ $totalHoras }} horas, según plan curricular.
        </p>

        <p class="certificate-details">
            En los archivos de esta maestría consta: {{ $alumno->apellidop }}
            {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}, con número de Matrícula Nº
            {{ $alumno->registro }}, quien aprobó todos los módulos contemplados en este programa, de acuerdo al
            siguiente detalle:
        </p>

        <table class="student-info">
            <thead>
                <tr>
                    <th>ASIGNATURA</th>
                    <th>TOTAL HORAS</th>
                    <th>PROMEDIO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notasCompletas as $nota)
                    <tr>
                        <td>{{ $nota->asignatura->nombre }}</td>
                        <td>{{ $nota->asignatura->horas_duracion ?? $nota->asignatura->credito * 48 }}</td>
                        <td>{{ $nota->total ?? '--' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p id="fecha-actual">Jipijapa, {{ $fechaActual }}</p>

        <div class="firma">
            <div class="firma-nombre">
                {{ strtoupper($nombreCompleto) }}
            </div>
            <div class="firma-cargo">
                COORDINADOR DEL PROGRAMA DE {{ strtoupper($alumno->maestria->nombre) }}
            </div>
        </div>

    </div>
</body>

</html>
