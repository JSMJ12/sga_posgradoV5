<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte EPSU</title>
    <style>
        @page {
            margin: 2.54cm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 13pt;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
            max-width: 900px;
            margin: auto;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-left,
        .logo-right {
            width: 70px;
        }

        .header-center {
            text-align: center;
            flex-grow: 1;
            padding: 0 10px;
        }

        .header-center .line1 {
            font-size: 13pt;
            font-weight: bold;
        }

        .header-center .line2 {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 3px;
        }

        .header-center .line3 {
            font-size: 10pt;
            margin-top: 2px;
        }

        .divider {
            width: 100%;
            height: 2px;
            background-color: goldenrod;
            margin: 10px 0 20px;
        }

        .info {
            font-size: 10pt;
            margin-bottom: 12px;
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
    </style>
</head>

<body>
    <div class="container">
        
        <div class="info">
            <strong>Maestría:</strong> {{ $maestria_nombre }}<br>
            <strong>Cohorte:</strong> {{ $cohorte->nombre ?? 'Cohorte ' . $cohorte->id }}
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
    </div>
</body>

</html>
