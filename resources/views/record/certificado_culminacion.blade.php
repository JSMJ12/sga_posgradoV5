<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado de Culminación</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 16px;
            text-align: justify;
            margin: 60px;
            margin-top: 240px;
        }

        .header-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 10px;
            width: 100%;
            z-index: 999;
        }

        .barra-verde {
            position: absolute;
            top: 0;
            left: 0;
            width: 85%;
            height: 10px;
            background-color: green;
        }

        .barra-roja {
            position: absolute;
            top: 0;
            left: calc(85% + 5px);
            right: 0;
            height: 10px;
            background-color: red;
        }

        .header {
            text-align: center;
        }

        .logo {
            position: absolute;
            top: 0;
            left: -13;
            width: 360px;
        }

        .titulo {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-top: 40px;

        }

        .nombre {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin-top: 15px;
        }

        .contenido {
            margin-top: 20px;
            line-height: 1.8;
        }

        .footer {
            position: absolute;
            bottom: 80px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .firma {
            margin-top: 50px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 700px;
            opacity: 0.08;
            transform: translate(-50%, -50%);
            z-index: 0;
        }
    </style>

</head>

<body>

    <div class="header-bar">
        <div class="barra-verde"></div>
        <div class="barra-roja"></div>
    </div>

    <img src="{{ public_path() . '/images/posgrado_logo.png' }}" alt="Marca de agua" class="watermark">

    <img class="logo" src="{{ public_path() . '/images/posgrado-20.png' }}" alt="Logo UNESUM">

    <div class="header">
        <h2>INSTITUTO DE POSGRADO</h2>
        <p><strong>CERTIFICADO DE CULMINACIÓN DEL PROGRAMA DE MAESTRÍA</strong></p>
    </div>

    <div class="contenido">
        <p>El Instituto de Posgrado, a través de la Coordinación del Programa de Maestría en
            {{ $alumno->maestria->nombre }}, hace constar que:</p>

        <div class="nombre">{{ $alumno->apellidop }}
            {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}</div>

        <p>
            con cédula de identidad {{ $alumno->dni }}, ha cumplido con todos los requisitos académicos y
            administrativos del programa de Maestría en {{ $alumno->maestria->nombre }} correspondiente a la cohorte
            {{ $cohorte->nombre }}, cursado en el Período Académico {{ $cohorte->periodo_academico->nombre }},
            desarrollado
            entre el
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_inicio)->isoFormat('D [de] MMMM [de] YYYY') }}
            y el
            {{ \Carbon\Carbon::parse($cohorte->periodo_academico->fecha_fin)->isoFormat('D [de] MMMM [de] YYYY') }}.
        </p>

        <p>Dado en Jipijapa,
            {{ $fechaActual }}.</p>
    </div>

    <div class="footer">
        <div style="margin-top: 100px; text-align: center;">
            <div
                style="border-top: 1px solid black; display: inline-block; padding: 5px 20px; font-weight: bold; text-transform: uppercase;">
                {{ strtoupper($nombreCompleto) }}
            </div>
            <div style="margin-top: 5px; font-weight: normal; font-size: 9pt; text-transform: uppercase;">
                COORDINADOR DEL PROGRAMA DE {{ strtoupper($alumno->maestria->nombre) }}
            </div>
        </div>
    </div>

</body>

</html>
