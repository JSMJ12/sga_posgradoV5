<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte EPSU</title>
    <style>
        @page {
            margin: 1cm; /* Margen reducido en todos los lados */
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 13pt;
            margin: 0;
            padding: 0;
            position: relative;
            min-height: 100vh;
        }

        .container {
            padding: 5px;
            max-width: 1000px;
            margin: 0 auto 0 auto; /* Más arriba y abajo */
        }

        .header {
            position: relative;
            text-align: center;
            margin-bottom: 10px;
            min-height: 100px;
        }

        .logo {
            position: absolute;
            top: 0;
            left: 0;
            width: 70px;
        }

        .seal {
            position: absolute;
            top: -15px;
            right: 0;
            width: 120px;
        }

        .university-name {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 10px;
        }

        .institute,
        .coordinator {
            font-size: 10pt;
            margin: 0;
        }

        .divider {
            width: 100%;
            height: 2px;
            background-color: #000;
            margin: 10px 0 20px 0;
        }

        .info {
            font-size: 10pt;
            margin-bottom: 12px;
            text-align: center;
        }

        table.excel-style {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        table.excel-style th,
        table.excel-style td {
            border: 1px solid #999;
            padding: 4px 6px;
            background-color: #fff;
            text-align: center;
        }

        table.excel-style th {
            background-color: #f0f0f0;
        }

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
            <img src="{{ public_path() . '/images/unesum.png' }}" alt="Logo UNESUM" class="logo">
            <img src="{{ public_path() . '/images/posgrado-25.png' }}" alt="Sello" class="seal">
            <p class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</p>
            <p class="institute">INSTITUTO DE POSGRADO</p>
            <p class="coordinator">COORDINACIÓN DE LA {{ strtoupper($maestria_nombre) }}</p>
        </div>
        <div class="divider"></div>

        <div class="info" style="text-align: center;">
            <h3 class="titulo">Reporte de Pagos (Aranceles / Matriculas / Inscriciones) - {{ $cohorte->nombre }}</h3>
            <h4 class="subtitulo">{{  $maestria_nombre }} (Código: {{ $codigoMaestria }})</h4>
        </div>

        <div style="overflow-x: auto;">
            <table class="excel-style">
                <thead>
                    <tr>
                        <th rowspan="2">Nombres</th>
                        <th rowspan="2">Apellidos</th>
                        <th rowspan="2">Email Institucional</th>
                        <th rowspan="2">Celular</th>
                        <th colspan="2">Descuento</th>
                        <th colspan="2">Arancel</th>
                        <th colspan="2">Matrícula</th>
                        <th colspan="2">Inscripción</th>
                    </tr>
                    <tr>
                        <th>Nombre</th>
                        <th>%</th>
                        <th>Pagado ($)</th>
                        <th>Adeudado ($)</th>
                        <th>Pagado ($)</th>
                        <th>Adeudado ($)</th>
                        <th>Pagado ($)</th>
                        <th>Adeudado ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $item)
                        <tr>
                            <td>{{ $item['alumno']->nombre1 }} {{ $item['alumno']->nombre2 }}</td>
                            <td>{{ $item['alumno']->apellidop }} {{ $item['alumno']->apellidom }}</td>
                            <td>{{ $item['alumno']->email_institucional }}</td>
                            <td>{{ $item['alumno']->celular }}</td>
                            <td>{{ $item['descuento']->nombre ?? '-' }}</td>
                            <td>{{ $item['descuento']->porcentaje ?? '0' }}%</td>
                            <td>{{ number_format($item['pagado']['arancel'], 2) }}</td>
                            <td>
                                @if ($item['adeudado']['arancel'] < 0)
                                    {{ number_format(abs($item['adeudado']['arancel']), 2) }}
                                    <small>(Devolución)</small>
                                @else
                                    {{ number_format($item['adeudado']['arancel'], 2) }}
                                @endif
                            </td>
                            <td>{{ number_format($item['pagado']['matricula'], 2) }}</td>
                            <td>
                                @if ($item['adeudado']['matricula'] < 0)
                                    {{ number_format(abs($item['adeudado']['matricula']), 2) }}
                                    <small>(Devolución)</small>
                                @else
                                    {{ number_format($item['adeudado']['matricula'], 2) }}
                                @endif
                            </td>
                            <td>{{ number_format($item['pagado']['inscripcion'], 2) }}</td>
                            <td>
                                @if ($item['adeudado']['inscripcion'] < 0)
                                    {{ number_format(abs($item['adeudado']['inscripcion']), 2) }}
                                    <small>(Devolución)</small>
                                @else
                                    {{ number_format($item['adeudado']['inscripcion'], 2) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px; text-align:left; font-size: 11pt;">
            <strong>Total recaudado:</strong><br>
            <strong>Arancel:</strong> ${{ number_format($total_recaudado['arancel'],2) }}<br>
            <strong>Matrícula:</strong> ${{ number_format($total_recaudado['matricula'],2) }}<br>
            <strong>Inscripción:</strong> ${{ number_format($total_recaudado['inscripcion'],2) }}<br>
            <br>
            <strong>Total deuda:</strong><br>
            <strong>Arancel:</strong> ${{ number_format($total_deuda['arancel'],2) }}<br>
            <strong>Matrícula:</strong> ${{ number_format($total_deuda['matricula'],2) }}<br>
            <strong>Inscripción:</strong> ${{ number_format($total_deuda['inscripcion'],2) }}
        </div>
    </div>
    <footer>
        Reporte generado por SGA POSGRADO UNESUM el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </footer>
</body>

</html>
