<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
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

        .certificate-title {
            text-align: center;
            font-size: 22pt;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .certificate-details {
            font-size: 10pt;
            text-align: justify;
            margin: 10px 0;
        }

        .student-info {
            font-size: 8pt;
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }

        .student-info th,
        .student-info td {
            border: 1px solid #666;
            padding: 4px;
            text-align: center;
        }

        .student-info th {
            background-color: #ccc;
        }

        #fecha-actual {
            text-align: right;
            margin-top: 20px;
            font-size: 9pt;
        }

        .firma {
            margin-top: 80px;
            text-align: center;
        }

        .firma-nombre {
            border-top: 1px solid black;
            display: inline-block;
            padding: 5px 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .firma-cargo {
            display: inline-block; 
            padding: 0 10px; 
            margin-bottom: 5px; 
            font-size: 10pt; 
            font-weight: bold;
            text-transform: uppercase;
        }

        #qr-code {
            position: absolute;
            bottom: -50px;
            right: 5px;
        }
    </style>
</head>
<body>
    <div class="container" style="font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.6;">
        <!-- Encabezado -->
        <div class="header" style="text-align: center; margin-bottom: 10px;">
            <span class="coordinator" style="font-weight: bold;">COORDINACIÓN DE LA {{ strtoupper($maestria->nombre) }}</span>
            <br>
            <span class="university" style="font-weight: bold;">CERTIFICADO DE CULMINACION DE TESIS</span>
        </div>


        <!-- Fecha -->
        <p id="fecha-actual" style="text-align: right;">Jipijapa, {{ $fechaActual }}</p>

        <!-- Destinatario -->
        <p>
            {{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }} {{ $docente->apellidom }}<br>
            Comisión de Titulación del Programa de la {{ $maestria->nombre }}<br>
            Presente. -
        </p>

        <p style="text-align: justify;">De mi consideración:</p>

        <!-- Cuerpo -->
        <p style="text-align: justify;">
            En atención a la designación efectuada con oficio No. 445-Msc.JCCV-CPMCLC-UNESUM-2021, 
            para ejercer la función de tutor(a) del trabajo de titulación por 
            <b>{{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}</b> 
            con el tema: <b>“{{ $tesis->tema }}”</b>, 
            tengo a bien indicar que este trabajo de titulación cumple con todos los requisitos 
            para la designación de los respectivos Miembros de Tribunal.
        </p>

        <p style="text-align: justify;">
            Seguro de su respuesta favorable a la presente, anticipo mis agradecimientos y me suscribo.
        </p>

        <p>Atentamente,</p>

        <!-- Firmas -->
        <table class="student-info" style="width: 100%; margin-top: 40px; text-align: center;">
            <thead>
                <tr>
                    <th style="width: 50%;">Firma del Estudiante</th>
                    <th style="width: 50%;">Firma Docente Tutor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding-top: 40px;">
                        <b>{{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}</b><br>
                        <span style="font-size: 10pt;">ESTUDIANTE</span>
                    </td>
                    <td style="padding-top: 40px;">
                        <b>{{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }} {{ $docente->apellidom }}</b><br>
                        <span style="font-size: 10pt;">DOCENTE TUTOR</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
