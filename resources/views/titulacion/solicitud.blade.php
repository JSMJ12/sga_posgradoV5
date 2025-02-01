<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Solicitud de Tesis</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path() . '/images/unesum.png' }}" alt="University Logo" class="logo">
            <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="University Seal" class="seal"><br>
            <span class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span><br>
            <span class="institute">INSTITUTO DE POSGRADO</span><br>
            <span class="coordinator">COORDINACIÓN DE LA {{ $alumno->maestria->nombre }}</span>
        </div>
        <div class="divider"></div>
        <h3><strong>Solicitud de Aprobación de Tesis y Asignacion de Tutor</strong></h3>
        <div id="fecha-actual">
            Jipijapa, {{ \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </div>
        <div class="certificate-details">
            <p>Coordinador del Programa de Maestría: {{ $nombreCompleto }}</p>
            <p>
                Yo, {{ $alumno->nombre1 }} {{ $alumno->nombre2 }} {{ $alumno->apellidop }} estudiante(s) de la {{ $alumno->maestria->nombre }} me dirijo a usted con el debido respeto y, por su intermedio, a la Comisión Académica del programa de maestría, para solicitar la aprobación del tema de trabajo de titulación titulado: <strong>"Tema de Tesis"</strong>.
            </p> 
            <p>Asimismo, informo que la modalidad elegida para el proceso de titulación es <strong>"Modalidad de Tesis"</strong>.</p>
            <p>Por lo expuesto, solicito se designe el tutor correspondiente para el desarrollo de dicho trabajo.</p>
            <p>Atentamente,</p>
        </div>
        <div class="firma">
            <br>
            <br>
            <br>
            <p>____________________________<br>{{ $alumno->nombre1 }} {{ $alumno->nombre2 }} {{ $alumno->apellidop }}
                {{ $alumno->apellidom }}<br>CI: {{ $alumno->dni }}</p>
        </div>


    </div>
</body>

</html>
