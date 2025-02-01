<table>
    <thead>
        <tr>
            <th>CODIGO_CARRERA</th>
            <th>TIPO_IDENTIFICACION</th>
            <th>IDENTIFICACION</th>
            <th>SEXO</th>
            <th>DISCAPACIDAD</th>
            <th>PORCENTAJE_DISCAPACIDAD</th>
            <th>NUMERO_CONADIS</th>
            <th>ETNIA</th>
            <th>NACIONALIDAD</th>
            <th>EMAIL_INSTITUCIONAL</th>
            <th>FECHA_INICIO_PRIMER_NIVEL</th>
            <th>FECHA_INGRESO_CONVALIDACION</th>
            <th>PAIS_RESIDENCIA</th>
            <th>PROVINCIA_RESIDENCIA</th>
            <th>CANTON_RESIDENCIA</th>
            <th>TIPO_COLEGIO</th>
            <th>POLITICA_CUOTA</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($alumnos as $alumno)
            <tr>
                <td>{{ $maestria->codigo }}</td>
                <td>{{ $alumno->ciudad }}</td>
                <td>
                    @if (preg_match('/[a-zA-Z]/', $alumno->dni))
                        PASAPORTE
                    @else
                        CÉDULA
                    @endif
                </td>
                <td>{{ $alumno->dni }}</td>
                <td>
                    @if ($alumno->sexo === 'M')
                        Masculino
                    @elseif ($alumno->sexo === 'F')
                        Femenino
                    @else
                        No especificado
                    @endif
                </td>
                <td>
                    {{ !is_null($alumno->porcentaje_discapacidad) || $alumno->porcentaje_discapacidad != 0 ? 'Sí' : 'No' }}
                </td>
                <td>{{ $alumno->porcentaje_discapacidad ?? 0 }}</td>
                <td>{{ $alumno->carnet_discapacidad ?? 'Ninguna' }}</td>
                <td>{{ $alumno->etnia ?? 'Ninguna' }}</td>
                <td>{{ $alumno->nacionalidad }}</td>
                <td>{{ $alumno->email_institucional }}</td>
                <td>{{ $cohorte->fecha_inicio }}</td>
                <td></td>
                <td></td>
                <td>{{ $alumno->provincia }}</td>
                <td>{{ $alumno->canton }}</td>
                <td>{{ $alumno->tipo_colegio }}</td>
                <td>{{ $alumno->descuento }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
