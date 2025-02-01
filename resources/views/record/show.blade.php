<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        #fecha-actual {
            font-size: 12pt;
            text-align: right;
            margin-top: 10px;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-top: 10px;
        }
        .logo {
            width: 74px;
            height: 89px;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .seal {
            width: 94px;
            height: 143px;
            position: absolute;
            top: -3px;
            right: 10px;
        }
        .university-name {
            font-size: 14pt;
            font-weight: bold;
        }
        .institute {
            font-size: 10pt;
        }
        .coordinator {
            font-size: 10pt;
        }
        .divider {
            width: 100%;
            height: 2px;
            background-color: #000;
            margin: 10px 0;
        }
        .certificate-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
        .certificate-details {
            font-size: 12pt;
            text-align: justify;
            margin: 10px 0;
        }
        .student-info {
            font-size: 10pt;
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        .student-info th, .student-info td {
            border: 1px solid #666;
            padding: 5px;
        }
        .student-info th {
            background-color: #ccc;
        }
        .footer {
            font-size: 10pt;
            text-align: right;
            margin-top: 10px;
        }
        #qr-code {
            position: absolute;
            bottom: 50px;
            right: 5px;
        }
    </style>
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
        <p class="certificate-title">CERTIFICA</p>
        <p class="certificate-details">Que de acuerdo a los registros que reposan en la Secretaría Académica de
             la Coordinación de la {{ $alumno->maestria->nombre }} de la Universidad Estatal del Sur de Manabí, se desarrolló al {{$numeroRomano}} PROGRAMA 
             DE {{ $alumno->maestria->nombre }}, inició sus actividades 
             académicas el {{ \Carbon\Carbon::parse($periodo_academico->fecha_inicio)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') }} y culminó 
             el {{ \Carbon\Carbon::parse($periodo_academico->fecha_fin)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') }}, con una modalidad {{ $cohorte->modalidad }}, con un total de {{ $totalCreditos }} horas, según plan curricular.</p>
        <p class="certificate-details">En los archivos de esta maestría consta: {{ $alumno->apellidop }} {{ $alumno->apellidom }} {{ $alumno->nombre1 }} {{ $alumno->nombre2 }}, con número de Matrícula Nº  {{ $alumno->registro }}, quien aprobó todos los módulos contemplados en este programa, de acuerdo al siguiente detalle:</p>
        <table class="student-info" style="width: 80%; margin: 0 auto; font-size: 10pt;">
            <col width="60%">
            <col width="40%">
            <col width="40%">
            <thead class="thead-dark">
                <tr>
                    <th>ASIGNATURA</th>
                    <th>TOTAL HORAS</th>
                    <th>PROMEDIO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notas as $nota)
                    <tr>
                        <td>{{ $nota->asignatura->nombre }}</td>
                        <td>{{ $nota->asignatura->credito }}</td>
                        <td>{{ $nota->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <p id="fecha-actual">Jipijapa, {{ $fechaActual }}</p>
        <table class="student-info">
            <col width="50%">
            <col width="25%">
            <col width="25%">
            <thead class="thead-white">
                <tr>
                    <th>Elaborado por:</th>
                    <th>Revisado y aprobado por:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <!-- Espacio para la firma -->
                        <p style="margin: 20px 0;"></p>
                        <p style="margin: 0;" align="center">
                            <font size="2" face="Times New Roman, serif"><b>{{ $secretarios[0]->apellidop }} {{ $secretarios[0]->apellidom }} {{ $secretarios[0]->nombre1 }} {{ $secretarios[0]->nombre2 }}</b></font>
                        </p>
                        <p style="margin: 0;" align="center">
                            <font size="2" face="Times New Roman, serif"><b>SECRETARIA ACADÉMICA DE LA MAESTRÍA</b></font>
                        </p>
                    </td>
                    <td>
                        <!-- Espacio para la firma -->
                        <p style="margin: 20px 0;"></p>
                        <p style="margin: 0;" align="center">
                            <font size="2" face="Times New Roman, serif"><b>{{ $nombreCompleto}}</b></font>
                        </p>
                        <p style="margin: 0;" align="center">
                            <font size="2" face="Times New Roman, serif"><b>COORDINADOR/A DE LA MAESTRÍA</b></font>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="qr-code">
            <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="Código QR">
        </div>
    </div>
</body>
</html>