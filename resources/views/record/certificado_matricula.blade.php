<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado de Matrícula</title>
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
        <br>

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
            <div style="display: inline-block; border-top: 1px solid black; padding: 0 10px; margin-bottom: 5px; font-size: 12pt;">
                <b>{{ $nombreCompleto }}</b>
            </div>
            <div style="display: inline-block; padding: 0 10px; margin-bottom: 5px; font-size: 10pt; text-transform: uppercase; font-weight: bold;">
                COORDINADOR DEL PROGRAMA DE {{ strtoupper($alumno->maestria->nombre)}}
            </div>
        </div>
    </div>
</body>

</html>
