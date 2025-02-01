<table style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
    <thead>
        <tr style="background-color: #f2f2f2; text-align: center;">
            <th style="border: 1px solid #000; padding: 8px;">CODIGO_CARRERA</th>
            <th style="border: 1px solid #000; padding: 8px;">TIPO_IDENTIFICACION</th>
            <th style="border: 1px solid #000; padding: 8px;">IDENTIFICACION</th>
            <th style="border: 1px solid #000; padding: 8px;">SEXO</th>
            <th style="border: 1px solid #000; padding: 8px;">EMAIL_INSTITUCIONAL</th>
            <th style="border: 1px solid #000; padding: 8px;">FECHA_INICIO_PRIMER_NIVEL</th>
            <th style="border: 1px solid #000; padding: 8px;">CIUDAD_CARRERA</th>
            <th style="border: 1px solid #000; padding: 8px;">FECHA_GRADUACION</th>
            <th style="border: 1px solid #000; padding: 8px;">TIPO_TRABAJO_GRADUACION</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($alumnos as $alumno)
            <tr style="text-align: center;">
                <td style="border: 1px solid #000; padding: 8px;">{{ $maestria->codigo }}</td>
                <td style="border: 1px solid #000; padding: 8px;">
                    @if (preg_match('/[a-zA-Z]/', $alumno->dni))
                        PASAPORTE
                    @else
                        CÉDULA
                    @endif
                </td>
                <td style="border: 1px solid #000; padding: 8px;">{{ $alumno->dni }}</td>
                <td style="border: 1px solid #000; padding: 8px;">
                    @if ($alumno->sexo === 'M')
                        Masculino
                    @elseif ($alumno->sexo === 'F')
                        Femenino
                    @else
                        No especificado
                    @endif
                </td>
                <td style="border: 1px solid #000; padding: 8px;">{{ $alumno->email_institucional }}</td>
                <td style="border: 1px solid #000; padding: 8px;">{{ $cohorte->fecha_inicio }}</td>
                <td style="border: 1px solid #000; padding: 8px;">JIPIJAPA</td>
                <td style="border: 1px solid #000; padding: 8px;">
                    {{ $alumno->titulaciones->sortBy('fecha_graduacion')->first()->fecha_graduacion ?? 'Sin titulación' }}
                </td>
                <td style="border: 1px solid #000; padding: 8px;">
                    {{ $alumno->tesis->sortBy('created_at')->first()->tipo ?? 'Sin tesis' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
