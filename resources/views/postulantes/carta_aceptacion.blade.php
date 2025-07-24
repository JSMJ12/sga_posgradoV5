<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carta de Aceptación</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            background: transparent;
            margin: 0;
            padding: 0;
             background-image: url("{{ public_path('images/fondo-pdf.jpeg') }}");
            background-size: 100% 100%;
            background-position: top left;
            background-repeat: no-repeat;
        }

        .container {
            width: 650px;
            margin: 5cm auto 0 auto;
            padding: 30px 30px 30px 30px;
            background: transparent;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
        }

        .header-flex {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            gap: 20px;
        }

        .logo,
        .seal {
            height: 70px;
            width: auto;
            flex-shrink: 0;
        }

        .header-center {
            flex: 1;
            text-align: center;
        }

        .university-name {
            font-size: 16pt;
            font-weight: bold;
            color: #020a04;
            letter-spacing: 1px;
        }

        .institute {
            font-size: 13pt;
            font-weight: bold;
            color: #22313f;
        }

        .coordinator {
            font-size: 11pt;
            color: #555;
            font-style: italic;
        }

        #fecha-actual {
            text-align: right;
            margin: 20px 0 30px 0;
            font-size: 11pt;
            color: #333;
        }

        .certificate-details {
            margin-bottom: 40px;
        }

        .certificate-details p {
            margin: 10px 0;
            text-align: justify;
        }

        .alert {
            border-radius: 6px;
            padding: 10px 18px;
            margin-bottom: 18px;
            font-size: 11pt;
        }

        .alert-info {
            background: #eaf7ef;
            border-left: 5px solid #218838;
            color: #218838;
        }

        .alert-warning {
            background: #fffbe6;
            border-left: 5px solid #ffc107;
            color: #856404;
        }

        .firma {
            text-align: center;
            margin-top: 60px;
            font-size: 12pt;
        }

        .firma p {
            margin: 0;
            line-height: 1.7;
        }

        @media print {
            .container {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
                padding: 0 30px;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-flex">
            <div class="header-center">
                <div class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</div>
                <div class="institute">INSTITUTO DE POSGRADO</div>
                <div class="coordinator" style="margin-top: 5px;">
                    COORDINACIÓN DE LA
                    @php
                        $nombreMaestria = strtoupper($postulante->maestria->nombre);
                        $palabras = explode(' ', $nombreMaestria);
                        $lineas = [];
                        $lineaActual = '';

                        foreach ($palabras as $palabra) {
                            if (strlen($lineaActual . ' ' . $palabra) <= 40) {
                                $lineaActual .= ($lineaActual ? ' ' : '') . $palabra;
                            } else {
                                $lineas[] = $lineaActual;
                                $lineaActual = $palabra;
                            }
                        }
                        if ($lineaActual) {
                            $lineas[] = $lineaActual;
                        }
                    @endphp

                    @foreach ($lineas as $linea)
                        <div style="text-transform: uppercase;">{{ $linea }}</div>
                    @endforeach
                </div>
            </div>
        </div>

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
            <p>De mi consideración:</p>
            <p>
                Quien suscribe <b>{{ $postulante->nombre1 }} {{ $postulante->nombre2 }} {{ $postulante->apellidop }} {{ $postulante->apellidom }}</b>,
                con cédula de identidad No. <b>{{ $postulante->dni }}</b>, de profesión <b>{{ $postulante->titulo_profesional }}</b>,
                a través de la presente comunico que <b>ACEPTO</b> el cupo al Programa de <b>{{ $postulante->maestria->nombre }}</b>
                - <b>{{ $cohorte_en_inscripcion ? $cohorte_en_inscripcion->nombre : 'N/A' }}</b> a impartirse en el Instituto de
                Posgrado de la Universidad Estatal del Sur de Manabí.
            </p>
            <p>Sin otro particular, reitero mis agradecimientos.</p>
            <p>Atentamente,</p>
        </div>

        <div class="firma">
            <br><br>
            <p>____________________________<br>
            {{ $postulante->nombre1 }} {{ $postulante->nombre2 }} {{ $postulante->apellidop }} {{ $postulante->apellidom }}<br>
            CI: {{ $postulante->dni }}</p>
        </div>
    </div>
</body>
</html>
