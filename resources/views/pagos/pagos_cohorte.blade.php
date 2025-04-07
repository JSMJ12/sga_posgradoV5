<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos Cohorte {{ $cohorte }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 100%;
            text-align: center;
            margin: 20px 0;
        }

        .chart-img {
            width: 100%;
            max-width: 600px;
        }

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
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
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

        .footer {
            font-size: 10pt;
            text-align: right;
            margin-top: 10px;
        }

        #qr-code {
            position: absolute;
            bottom: 50px;
            right: 5px;
        }
    </style>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .titulo {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }

        .subtitulo {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .total {
            font-weight: bold;
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
            <span class="coordinator">COORDINACIÓN DE LA {{ strtoupper($maestria) }}</span>
        </div>
        <div class="divider"></div>

        <h1 class="titulo">Reporte de Pagos - Cohorte {{ $cohorte }}</h1>
        <h2 class="subtitulo">Maestría: {{ $maestria }} (Código: {{ $codigoMaestria }})</h2>


        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>DNI</th>
                    <th>Nombre del Alumno</th>
                    <th>Correo Institucional</th>
                    <th>Celular</th>
                    <th>Cantidad de Pagos</th>
                    <th>Monto Pagado</th>
                    <th>Deuda Pendiente</th>
                </tr>
            </thead>
            <tbody>
                @php $contador = 1; @endphp
                @foreach ($pagos as $dni => $datos)
                    <tr>
                        <td>{{ $contador++ }}</td>
                        <td>{{ $dni }}</td>
                        <td>{{ $datos['alumno']->nombre1 }} {{ $datos['alumno']->nombre2 }}
                            {{ $datos['alumno']->apellidop }} {{ $datos['alumno']->apellidom }}</td>
                        <td>{{ $datos['alumno']->email_institucional }}</td>
                        <td>{{ $datos['alumno']->celular }}</td>
                        <td>{{ $datos['cantidad_pagos'] }}</td>
                        <td>${{ number_format($datos['monto_pagado'], 2) }}</td>
                        <td>${{ number_format($datos['deuda_pendiente'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <h3>Gráficos de Pagos</h3>
    <p>Distribución de pagos por alumno:</p>
    <img src="{{ public_path('/storage/chart1_' . $cohorte . '_' . $codigoMaestria . '_' . $maestria . '.png') }}"
        style="width: 100%; max-width: 600px;">

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    <p>Comparación de total deuda vs total pagado:</p>
    <img src="{{ public_path('/storage/chart2_' . $cohorte . '_' . $codigoMaestria . '_' . $maestria . '.png') }}"
        style="width: 100%; max-width: 600px;">
</body>

</html>
