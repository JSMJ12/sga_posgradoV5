<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
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

        .certificate-title {
            text-align: center;
            font-size: 22pt;
            font-weight: bold;
            margin-bottom: 20px;
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
            display: inline-block; 
            padding: 0 10px; 
            margin-bottom: 5px; 
            font-size: 10pt; 
            font-weight: bold;
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
            la Coordinación de la {{ $maestria->nombre }} de la Universidad Estatal del Sur de Manabí, se
            desarrolló al {{ $numeroRomano }} PROGRAMA
            DE LA {{ strtoupper($maestria->nombre) }}, inició sus actividades académicas el
            {{ \Carbon\Carbon::parse($cohorte->fecha_inicio)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') }}
            y culminó el
            {{ \Carbon\Carbon::parse($cohorte->fecha_fin)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') }},
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
                COORDINADOR DEL PROGRAMA DE {{ strtoupper($maestria->nombre) }}
            </div>
        </div>

    </div>
</body>

</html>
