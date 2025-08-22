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
            margin: 0;
            font-family: 'Times New Roman', serif;
            font-size: 16px;
            background-image: url("{{ public_path('images/fondopdf.png') }}");
            background-size: 98% 98%;
            background-position: center center;
            background-repeat: no-repeat;
        }

        .container {
            width: 80%;
            margin: 250px auto 80px auto;
            text-align: justify;
            line-height: 1.8;
        }

        .header {
            text-align: center;
            font-size: 22px;
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
            bottom: 290px; 
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
            display: inline-block; padding: 0 10px; margin-bottom: 5px; font-size: 10pt; text-transform: uppercase; font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
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
            {{ \Carbon\Carbon::parse($cohorte->fecha_inicio)->isoFormat('D [de] MMMM [de] YYYY') }}
            y el
            {{ \Carbon\Carbon::parse($cohorte->fecha_fin)->isoFormat('D [de] MMMM [de] YYYY') }}.
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
