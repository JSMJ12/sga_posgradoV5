<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path() . '/images/unesum.png' }}" alt="University Logo" class="logo">
            <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="University Seal" class="seal">
            <br>
            <span class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span><br>
            <span class="institute">INSTITUTO DE POSGRADO</span><br>
            <span class="coordinator">COORDINACIÓN DE LA {{ strtoupper($alumno->maestria->nombre) }}</span>
        </div>

        <div class="divider"></div>
        
        <p id="fecha-actual">Jipijapa, {{ $fechaActual }}</p>

        <p>
            {{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }} {{ $docente->apellidom }}<br>
            Comisión de Titulación del Programa de la {{ $alumno->maestria->nombre }}<br>
            Presente. -
        </p>

        <p>
            De mi consideración:
        </p>

        <p> En atención a la designación efectuada con oficio No. 445-Msc.JCCV-CPMCLC-UNESUM-2021, para ejercer la función de tutor(a) del trabajo de titulación por {{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }} con el tema "<b>{{ $alumno->tesis->first()->tema }}</b>", tengo a bien indicar que este trabajo de titulación cumple con todos los requisitos para la designación de los respectivos Miembros de Tribunal.</p>

        <p>
            Seguro de su respuesta favorable a la presente, anticipo mis agradecimientos y suscribo.
        </p>

        <p>
            Atentamente,
        </p>

        <table class="student-info">
            <col width="50%">
            <col width="50%">
            <thead class="thead-white">
                <tr>
                    <th>Firma del Estudiante</th>
                    <th>Firma Docente Tutor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <!-- Espacio para la firma -->
                        <p style="margin: 20px 0;"></p>
                        <p style="margin: 0;" align="center">
                            <font size="2" face="Times New Roman, serif"><b>{{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}</b></font>
                        </p>
                        <p style="margin: 0;" align="center">
                            <font size="2" face="Times New Roman, serif"><b>ESTUDIANTE</b></font>
                        </p>
                    </td>
                    <td>
                        <!-- Espacio para la firma -->
                        <p style="margin: 20px 0;"></p>
                        <p style="margin: 0;" align="center">
                            <font size="2" face="Times New Roman, serif"><b>{{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }} {{ $docente->apellidom }}</b></font>
                        </p>
                        <p style="margin: 0;" align="center">
                            <font size="2" face="Times New Roman, serif"><b>DOCENTE TUTOR</b></font>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
