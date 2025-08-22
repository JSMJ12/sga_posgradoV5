<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado de Culminación</title>
    <style>
        @page {
            margin: 0px;
        }

        body {
            padding: 115px 20px 120px 80px;
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
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            z-index: -1;
        }

        .container {
            width: 90%;
            text-align: justify;
            line-height: 1.5;
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
            margin-top: 120px; /* Súbela o bájala ajustando este valor */
            width: 100%;
            text-align: center;
        }

        .firma-block {
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
            display: block;
            margin-top: 5px;
            font-size: 10pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        /* Footer dinámico para Secretario/a EPSU */
        footer {
            position: fixed;
            right: 0;
            bottom: 0;
            width: 420px;
            text-align: right;
            font-size: 10pt;
            color: #fff;
            background: #14532d;
            padding: 10px 30px 10px 10px;
            border-top-left-radius: 12px;
            box-shadow: -2px -2px 8px rgba(20, 83, 45, 0.08);
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

    <!-- Firma centrada -->
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

    <!-- Footer solo si el usuario es Secretario/a EPSU -->
    @role('Secretario/a EPSU')
        <footer>
            Reporte generado por SGA POSGRADO UNESUM el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </footer>
    @endrole

</body>
</html>
