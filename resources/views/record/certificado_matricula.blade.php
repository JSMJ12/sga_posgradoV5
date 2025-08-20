<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado de Matrícula</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        body {
            background-image: url("{{ public_path('images/fondopdf.png') }}");
            background-size: 98% 98%; 
            background-repeat: no-repeat;
            background-position: center center;
            min-height: 100vh;
            width: 100vw;
        }
        .container {
            width: 80%;
            box-sizing: border-box;
            padding-top: 250px; /* Subir el texto */
            margin: 0 auto;
        }
        .title {
            text-align: center;
            font-size: 22pt;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .body-text {
            font-size: 12pt;
            margin-bottom: 10px;
            text-align: justify;
        }
        .body-text.bold {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
        }
        .firmante {
            margin-top: 220px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="title">CERTIFICADO DE MATRÍCULA</div>

        <p class="body-text">
            El Instituto de Posgrado, a través de la Coordinación del Programa de la
            {{ ucfirst(strtolower($alumno->maestria->nombre)) }}, hace constar que:
        </p>
        <br>

        <p class="body-text bold">
            {{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}
        </p>
        <br>
        <p class="body-text">
            Con número de identificación {{ $alumno->dni }}, ha formalizado su matrícula en la
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
