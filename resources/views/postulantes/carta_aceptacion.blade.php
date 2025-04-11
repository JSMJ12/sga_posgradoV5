<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carta de Aceptación</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path() . '/images/unesum.png' }}" alt="University Logo" class="logo">
            <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="University Seal" class="seal"><br>
            <span class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span><br>
            <span class="institute">INSTITUTO DE POSGRADO</span><br>
            <span class="coordinator">COORDINACIÓN DE LA {{ $postulante->maestria->nombre }}</span>
        </div>
        <div class="divider"></div>

        @if ($postulante->maestria->cohorte)
            @if ($cohorte_en_inscripcion)
                <div class="alert alert-info mt-3">
                    <strong>Inscripciones abiertas:</strong><br>
                    Cohorte: <strong>{{ $cohorte_en_inscripcion->nombre }}</strong><br>
                    Fecha de inicio: {{ \Carbon\Carbon::parse($cohorte_en_inscripcion->fecha_inicio)->format('d/m/Y') }}
                </div>
            @else
                <div class="alert alert-warning mt-3">
                    No hay cohortes con inscripciones abiertas actualmente.
                </div>
            @endif
        @endif

        <div id="fecha-actual">
            Jipijapa, {{ \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </div>

        <div class="certificate-details">
            <p>Señores<br>Instituto de Posgrado UNESUM<br>Presente.-</p>
            <p>De mi consideración</p>
            <p>Quien suscribe {{ $postulante->nombre1 }} {{ $postulante->nombre2 }} {{ $postulante->apellidop }}
                {{ $postulante->apellidom }} con cédula de identidad No. {{ $postulante->dni }} de profesión
                {{ $postulante->titulo_profesional }} a través de la presente comunico que ACEPTO el cupo al Programa
                de {{ $postulante->maestria->nombre }} - 
                {{ $cohorte_en_inscripcion ? $cohorte_en_inscripcion->nombre : 'N/A' }} a impartirse en el Instituto de
                Posgrado de la Universidad Estatal del Sur de Manabí.</p>
            <p>Sin otro particular reitero mis agradecimientos.</p>
            <p>Atentamente,</p>
        </div>

        <div class="firma">
            <br><br><br>
            <p>____________________________<br>{{ $postulante->nombre1 }} {{ $postulante->nombre2 }}
                {{ $postulante->apellidop }} {{ $postulante->apellidom }}<br>CI: {{ $postulante->dni }}</p>
        </div>

    </div>
</body>

</html>
