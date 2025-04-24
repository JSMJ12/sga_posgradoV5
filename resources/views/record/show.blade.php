<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>RECORD ACADÉMICO</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 11pt;
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
        }

        .logo {
            width: 64px;
            position: absolute;
            top: -35px;
            left: 0;
        }

        .seal {
            width: 104px;
            position: absolute;
            top: -35;
            right: 0;
        }

        .university-name {
            font-size: 13pt;
            font-weight: bold;
        }

        .institute,
        .coordinator {
            font-size: 10pt;
        }

        .divider {
            height: 3px;
            background-color: gold;
            margin: 10px 0 20px;
        }

        .certificate-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .student-details {
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .student-info {
            width: 100%;
            font-size: 10pt;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .student-info th,
        .student-info td {
            border: 1px solid #333;
            padding: 5px;
            text-align: center;
        }

        .student-info th {
            background-color: #f0f0f0;
        }

        #fecha-actual {
            text-align: right;
            margin-top: 30px;
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
        <div class="header">
            <img src="{{ public_path() . '/images/unesum.png' }}" alt="Logo" class="logo">
            <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="Sello" class="seal">
            <div class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</div>
            <div class="institute">INSTITUTO DE POSGRADO</div>
            <div class="coordinator">COORDINACIÓN DE LA {{ strtoupper($alumno->maestria->nombre) }}</div>
        </div>

        <div class="divider"></div>

        <p class="certificate-title">RECORD ACADÉMICO</p>

        <div class="student-details">
            Estudiante: {{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }}
            {{ $alumno->nombre2 }}<br>
            Período académico: {{ $cohorte->periodo_academico->nombre }}<br>
            Cohorte: {{ $cohorte->nombre }}<br>
            Modalidad: {{ $cohorte->modalidad }}<br>
            Fecha de inicio:
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_inicio)->translatedFormat('d \d\e F \d\e Y') }}<br>
            Fecha de fin:
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
                @foreach ($notas as $index => $nota)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $nota->asignatura->nombre }}</td>
                        <td>{{ $nota->asignatura->horas_duracion ?? $nota->asignatura->credito * 48 }}</td>
                        <td>{{ $nota->total }}</td>
                        <td>
                            @if (is_null($nota->total))
                                {{-- vacío --}}
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

        <div class="firmante" style="margin-top: 110px; text-align: center;">
            <div
                style="display: inline-block; border-top: 1px solid black; width: fit-content; padding: 0 10px; margin-bottom: 5px;">
                <b>{{ $nombreCompleto }}</b>
            </div>
            <div>Coordinador del Programa de {{ ucfirst(strtolower($alumno->maestria->nombre)) }}</div>
        </div>

        <div id="qr-code">
            <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="QR">
        </div>
    </div>
</body>

</html>
