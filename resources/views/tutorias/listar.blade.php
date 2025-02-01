@extends('adminlte::page')

@section('title', 'Lista de Tutorías')

@section('content_header')
    <h1 class="text-center">Tutorías para la Tesis: {{ $tesis->tema }}</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-info">
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Tipo</th>
                                <th>Lugar/Link</th>
                                <th>Observaciones</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tesis->tutorias as $tutoria)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($tutoria->fecha)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $tutoria->tipo == 'presencial' ? 'success' : 'info' }}">
                                            {{ ucfirst($tutoria->tipo) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($tutoria->tipo == 'presencial')
                                            <i class="fas fa-map-marker-alt"></i> {{ $tutoria->lugar ?? 'No especificado' }}
                                        @else
                                            <i class="fas fa-link"></i> <a href="{{ $tutoria->link_reunion }}"
                                                target="_blank">{{ $tutoria->link_reunion ?? 'No disponible' }}</a>
                                        @endif
                                    </td>
                                    <td>{{ $tutoria->observaciones ?? 'Sin observaciones' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $tutoria->estado == 'pendiente' ? 'warning' : 'success' }}">
                                            {{ ucfirst($tutoria->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Formulario para eliminar tutoría -->
                                        <form action="{{ route('tutorias.delete', $tutoria->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Estás seguro de eliminar esta tutoría?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>

                                        <!-- Mostrar solo el botón para marcar como realizada si no está realizada aún -->
                                        @if ($tutoria->estado != 'realizada')
                                            <form action="{{ route('tutorias.realizar', $tutoria->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('¿Marcar esta tutoría como realizada?')">
                                                    <i class="fas fa-check"></i> Realizada
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay tutorías asignadas para esta tesis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Estilo adicional para la tabla y card */
        .card {
            background-color: #fff;
            border-radius: 8px;
        }

        .table {
            margin-bottom: 0;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
@stop
