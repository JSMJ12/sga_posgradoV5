<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        #fecha-actual {
            font-size: 12pt;
            text-align: right;
            margin-top: 10px;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            transform: translateX(-10px);
        }

        .header {
            text-align: center;
            margin-top: 10px;
        }

        .logo {
            width: 74px;
            height: 89px;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .seal {
            width: 94px;
            height: 143px;
            position: absolute;
            top: -3px;
            right: 10px;
        }

        .student-photo {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border: 1px solid #000;
            float: left;
            margin-right: 15px;
            margin-bottom: 10px;
        }


        .university-name {
            font-size: 14pt;
            font-weight: bold;
        }

        .institute {
            font-size: 10pt;
        }

        .coordinator {
            font-size: 10pt;
        }

        .divider {
            width: 100%;
            height: 2px;
            background-color: #000;
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

        #qr-code {
            position: absolute;
            bottom: 50px;
            right: 5px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300px;
            opacity: 0.08;
            transform: translate(-50%, -50%);
            z-index: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="Marca de agua" class="watermark">
        <div class="header">
            <img src="{{ public_path() . '/images/unesum.png' }}" alt="University Logo" class="logo">
            <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="University Seal" class="seal">
            <br>
            <span class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span><br>
            <span class="institute">INSTITUTO DE POSGRADO</span><br>
            <span class="coordinator">COORDINACIÓN DE LA {{ strtoupper($alumno->maestria->nombre) }}</span>
        </div>
        <div class="divider"></div>
        <p class="certificate-title">CERTIFICADO DE MATRICULA</p>
        <p class="certificate-details">
            @if ($alumno->image)
                <img src="{{ storage_path('app/public/' . $alumno->image) }}" alt="{{ $alumno->image }}"
                    class="student-photo">
            @endif
            <strong>CERTIFICO QUE</strong> el(la) señor(a) {{ strtoupper($alumno->apellidop) }}
            {{ strtoupper($alumno->apellidom) }} {{ strtoupper($alumno->nombre1) }} {{ strtoupper($alumno->nombre2) }},
            con identificación {{ $alumno->dni }}, estudiante de la {{ strtoupper($alumno->maestria->nombre) }}, del
            INSTITUTO DE POSGRADO DE LA UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ, se encuentra legalmente matriculado(a) en
            el
            {{ strtoupper($cohorte->nombre) }}, periodo académico {{ strtoupper($periodo_academico->nombre) }}, con el
            número de matrícula {{ $alumno->registro }}.
        </p>
        <p class="certificate-details">
            El detalle de las asignaturas inscritas es el siguiente:
        </p>

        <table class="student-info" style="width: 100%; font-size: 10pt;">
            <thead>
                <tr>
                    <th>ASIGNATURA</th>
                    <th>CRÉDITOS</th>
                    <th>HORAS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($asignaturas as $asignatura)
                    <tr>
                        <td>{{ $asignatura->nombre }}</td>
                        <td>{{ $asignatura->credito }}</td>
                        <td>{{ $asignatura->horas_duracion ?? $asignatura->credito * 48 }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td style="text-align: right;"><strong>Total</strong></td>
                    <td><strong>{{ $totalCreditos }}</strong></td>
                    <td><strong>{{ $totalHoras }}</strong></td>
                </tr>
            </tbody>
        </table>


        <p id="fecha-actual">Jipijapa, {{ $fechaActual }}</p>
        <table class="student-info">
            <thead>
                <tr>
                    <th>Elaborado por:</th>
                    <th>Revisado y aprobado por:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">
                        <p style="margin: 20px 0;"></p>
                        <b>{{ $secretarios[0]->apellidop }} {{ $secretarios[0]->apellidom }}
                            {{ $secretarios[0]->nombre1 }} {{ $secretarios[0]->nombre2 }}</b><br>
                        <b>SECRETARIO/A ACADÉMICO DE LA MAESTRÍA</b>
                    </td>
                    <td align="center">
                        <p style="margin: 20px 0;"></p>
                        <b>{{ $nombreCompleto }}</b><br>
                        <b>COORDINADOR/A DE LA MAESTRÍA</b>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="qr-code">
            <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="Código QR">
        </div>
    </div>
</body>

</html>
