<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>RECORD ACADÉMICO</title>
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
            font-size: 16pt;
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

        table.firmas {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
            font-size: 8pt;
        }

        table.firmas td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            vertical-align: top;
        }

        hr.firma-linea {
            margin: 8px 20px;
            border: none;
            border-top: 1px solid #000;
        }

        span.nombre-firma {
            text-transform: uppercase;
            font-size: 6pt;
        }

        span.cargo-firma {
            font-size: 7pt;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <p class="certificate-title">RECORD ACADÉMICO</p>

        <div class="student-details">
            <strong>Estudiante:</strong> {{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}<br>
            <strong>Cédula/Pasaporte:</strong> {{ $alumno->dni }}<br>
            <strong>Período académico:</strong> {{ $cohorte->periodo_academico->nombre }}<br>
            <strong>Cohorte:</strong> {{ $cohorte->nombre }}<br>
            <strong>Modalidad:</strong> {{ $cohorte->modalidad }}<br>
            <strong>Fecha de inicio:</strong> {{ \Carbon\Carbon::parse($cohorte->fecha_inicio)->translatedFormat('d \d\e F \d\e Y') }}<br>
            <strong>Fecha de fin:</strong> {{ \Carbon\Carbon::parse($cohorte->fecha_fin)->translatedFormat('d \d\e F \d\e Y') }}
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
                                --
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

        <p>
            <strong>Total de horas:</strong> {{ $totalHoras }}<br>
            <strong>Total de asignaturas aprobadas:</strong> {{ $cantidadAsignaturas }}<br>
            <strong>Promedio general:</strong> {{ $promedio ? number_format($promedio, 2) : 'N/A' }}
        </p>

        <p id="fecha-actual">Jipijapa, {{ $fechaActual }}</p>

        <table class="firmas">
            <tr>
                <td>
                    <b>Generado por:</b><br><br>
                    <hr class="firma-linea">
                    @if (isset($secretario))
                        <span class="nombre-firma">{{ $secretario->full_name }}</span><br>
                    @else
                        <span class="nombre-firma">--</span><br>
                    @endif
                    <span class="cargo-firma">SECRETARIA ACADÉMICA</span>
                </td>
                <td>
                    <b>Revisado por:</b><br><br>
                    <hr class="firma-linea">
                    <span class="nombre-firma">{{ strtoupper($nombreCompleto) }}</span><br>
                    <span class="cargo-firma">COORDINADOR(A) DEL PROGRAMA</span>
                </td>
                <td>
                    <b>Aprobado por:</b><br><br>
                    <hr class="firma-linea">
                    @if (isset($directorDocente))
                        <span class="nombre-firma">{{ $directorDocente->full_name }}</span><br>
                    @else
                        <span class="nombre-firma">Ing. Leopoldo Venegas Loor, PhD</span><br>
                    @endif
                    <span class="cargo-firma">DIRECTOR DEL INSTITUTO DE POSGRADO</span>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
