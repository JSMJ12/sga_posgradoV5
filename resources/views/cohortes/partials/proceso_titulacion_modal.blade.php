<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Alumno</th>
                <th>Estado Tesis</th>
                <th>Tutorías</th>
                <th>Tutorías Completadas</th>
                <th>Tutor</th>
                <th>Fecha de Graduación</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($alumnosProcesados as $item)
                <tr>
                    <td>{{ $item['alumno']->getFullNameAttribute() }}</td>
                    <td>
                        @php
                            $estado = $item['estado_tesis'];
                            $badgeClass = match($estado) {
                                'aprobado' => 'success',
                                'pendiente' => 'warning',
                                'rechazado' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($estado) }}</span>
                    </td>
                    <td>
                        @if($item['tiene_tesis_aprobada'])
                            <ul class="mb-0 pl-3">
                                @foreach($item['tutorias'] as $tutoria)
                                    <li>
                                        {{ $tutoria['fecha'] ?? 'Sin fecha' }} - 
                                        <span class="badge badge-{{ $tutoria['estado'] === 'realizada' ? 'success' : 'warning' }}">
                                            {{ ucfirst($tutoria['estado']) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>{{ $item['tutorias_completadas'] }} / 3</td>
                    <td>{{ $item['tutor'] ?? 'Sin tutor asignado' }}</td>
                    <td>{{ $item['graduado'] ?? 'No graduado' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No hay alumnos registrados en esta cohorte.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
