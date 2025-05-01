<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos Cohorte {{ $cohorte }}</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .header {
            text-align: center;
            position: relative;
            margin-bottom: 20px;
        }

        .logo {
            position: absolute;
            top: 0;
            left: 0;
            width: 74px;
        }

        .seal {
            position: absolute;
            top: 0;
            right: 0;
            width: 94px;
        }

        .university-name {
            font-size: 14pt;
            font-weight: bold;
        }

        .institute, .coordinator {
            font-size: 10pt;
        }

        .divider {
            height: 2px;
            background-color: #000;
            margin: 10px 0;
        }

        .titulo {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
        }

        .subtitulo {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #e6e6e6;
        }

        .chart {
            text-align: center;
            margin-top: 30px;
        }

        .chart img {
            width: 100%;
            max-width: 600px;
        }

        .footer {
            text-align: right;
            font-size: 10pt;
            margin-top: 40px;
            color: #444;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path() . '/images/unesum.png' }}" alt="Logo UNESUM" class="logo">
            <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="Sello" class="seal">
            <p class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</p>
            <p class="institute">INSTITUTO DE POSGRADO</p>
            <p class="coordinator">COORDINACIÓN DE LA {{ strtoupper($maestria) }}</p>
        </div>

        <div class="divider"></div>

        <h1 class="titulo">Reporte de Pagos (Aranceles) - {{ $cohorte }}</h1>
        <h2 class="subtitulo">{{ $maestria }} (Código: {{ $codigoMaestria }})</h2>

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

        <div class="chart">
            <h3>Distribución de Pagos por Alumno</h3>
            <img src="{{ public_path('/storage/chart1_' . $cohorte . '_' . $codigoMaestria . '_' . $maestria . '.png') }}" alt="Gráfico 1">
        </div>

        <div class="chart">
            <h3>Comparación: Total Pagado vs Deuda</h3>
            <img src="{{ public_path('/storage/chart2_' . $cohorte . '_' . $codigoMaestria . '_' . $maestria . '.png') }}" alt="Gráfico 2">
        </div>

        <div class="footer">
            Fecha de emisión: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
    </div>
</body>

</html>
