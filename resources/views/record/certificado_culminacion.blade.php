<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado de Culminación</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        html, body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', serif;
            font-size: 16px;
            background-image: url("{{ public_path('images/fondopdf.png') }}");
            background-size: 100% 100%;
            background-position: top left;
            background-repeat: no-repeat;
        }

        .container {
            width: 85%;
            margin: 190px auto 80px auto; /* 🔽 Bajado 15px (antes era 160px) */
            padding-left: 0;
            text-align: justify;
            line-height: 1.8;
        }

        .header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .nombre {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin: 25px 0;
        }

        .footer {
            position: absolute;
            bottom: 170px; /* 🔼 Subido 20px (antes era 80px) */
            width: 100%;
            text-align: center;
        }

        .firma-block {
            margin-top: 60px;
            text-align: center;
        }

        .firma-linea {
            border-top: 1px solid black;
            display: inline-block;
            padding: 5px 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .cargo {
            margin-top: 5px;
            font-weight: normal;
            font-size: 9pt;
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            INSTITUTO DE POSGRADO <br>
            CERTIFICADO DE CULMINACIÓN DEL PROGRAMA DE MAESTRÍA
        </div>

        <p>
            El Instituto de Posgrado, a través de la Coordinación del Programa de Maestría en
            {{ $alumno->maestria->nombre }}, hace constar que:
        </p>

        <div class="nombre">
            {{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}
        </div>

        <p>
            con cédula de identidad {{ $alumno->dni }}, ha cumplido con todos los requisitos académicos y
            administrativos del programa de Maestría en {{ $alumno->maestria->nombre }}, correspondiente a la cohorte
            {{ $cohorte->nombre }}, cursado en el Período Académico {{ $cohorte->periodo_academico->nombre }},
            desarrollado entre el
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_inicio)->isoFormat('D [de] MMMM [de] YYYY') }}
            y el
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_fin)->isoFormat('D [de] MMMM [de] YYYY') }}.
        </p>

        <p>
            Dado en Jipijapa, {{ $fechaActual }}.
        </p>
    </div>

    <div class="footer">
        <div class="firma-block">
            <div class="firma-linea">
                {{ strtoupper($nombreCompleto) }}
            </div>
            <div class="cargo">
                COORDINADOR DEL PROGRAMA DE {{ strtoupper($alumno->maestria->nombre) }}
            </div>
        </div>
    </div>

</body>
</html>
