<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado de Matrícula</title>
    <style>
        @page {
            margin: 2.54cm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo {
            width: 64px;
            position: absolute;
            top: -25px;
            left: 0;
        }

        .seal {
            width: 104px;
            position: absolute;
            top: -25;
            right: 0;
        }

        .university-name {
            font-size: 14pt;
            font-weight: bold;
        }

        .institute {
            font-size: 10pt;
        }

        .program-title {
            font-size: 10pt;
            margin-top: 5px;
        }

        .divider {
            height: 3px;
            background-color: gold;
            margin: 10px 0 20px;
        }

        .title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .body-text {
            text-align: justify;
            line-height: 1.6;
        }

        .footer {
            text-align: left;
            margin-top: 60px;
        }

        #fecha-actual {
            margin-top: 30px;
        }

        .firmante {
            margin-top: 40px;
            text-align: center;
        }

        .firmante b {
            display: block;
            margin-bottom: 2px;
        }

        .qr {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 90px;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="{{ public_path() . '/images/unesum.png' }}" class="logo">
        <img src="{{ public_path() . '/images/posgrado-25.png' }}" class="seal">

        <div class="header">
            <div class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</div>
            <div class="institute">INSTITUTO DE POSGRADO</div>
            <div class="program-title">COORDINACIÓN DE LA {{ strtoupper($alumno->maestria->nombre) }}</div>
        </div>

        <div class="divider"></div>

        <div class="title">INSTITUTO DE POSGRADO <br> CERTIFICADO DE MATRÍCULA</div>

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

        <div class="firmante" style="margin-top: 130px; text-align: center;">
            <div style="display: inline-block; border-top: 1px solid black; width: fit-content; padding: 0 10px; margin-bottom: 5px;">
                <b>{{ $nombreCompleto }}</b>
            </div>
            <div>Coordinador del Programa de {{ ucfirst(strtolower($alumno->maestria->nombre)) }}</div>
        </div>              

        <img class="qr" src="data:image/png;base64,{{ base64_encode($qrCode) }}">
    </div>
</body>

</html>
