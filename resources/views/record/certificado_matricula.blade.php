<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado de Matrícula</title>
    <style>
        html, body {
            background-image: url('{{ public_path("images/fondopdf.png") }}');
        }

        .container {
            width: 100%;
            box-sizing: border-box;
            padding-top: 190px;
            margin: 0;
        }
    </style>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>

<body>
    <div class="container">
        <div class="title">CERTIFICADO DE MATRÍCULA</div>

        <p class="body-text">
            El Instituto de Posgrado, a través de la Coordinación del Programa de la
            {{ ucfirst(strtolower($alumno->maestria->nombre)) }}, hace constar que:
        </p>

        <p class="body-text" style="text-align: center; font-weight: bold; margin-top: 20px;">
            {{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}
        </p>

        <p class="body-text">
            con número de identificación {{ $alumno->dni }}, ha formalizado su matrícula en la
            {{ ucfirst(strtolower($alumno->maestria->nombre)) }}, correspondiente a la {{ $cohorte->nombre }},
            en el Período Académico {{ $cohorte->periodo_academico->nombre }}, con fecha de inicio el
            {{ \Carbon\Carbon::parse($cohorte->fecha_inicio)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
            y fecha de culminación prevista para el
            {{ \Carbon\Carbon::parse($cohorte->fecha_fin)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}.
        </p>

        <p class="body-text" id="fecha-actual">
            Dado en Jipijapa, {{ $fechaActual }}.
        </p>

        <div class="firmante">
            <div style="display: inline-block; border-top: 1px solid black; padding: 0 10px; margin-bottom: 5px;">
                <b>{{ $nombreCompleto }}</b>
            </div>
            <div style="margin-top: 5px; font-weight: normal; font-size: 9pt; text-transform: uppercase;">
                COORDINADOR DEL PROGRAMA DE {{ strtoupper($alumno->maestria->nombre)}}
            </div>
        </div>
    </div>
</body>

</html>
