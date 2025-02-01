@extends('adminlte::page')

@section('title', 'Proceso de Titulación')

@section('content_header')
    <h1 class="text-center text-success">Solicitud de Aprobación de Tema</h1>
@stop

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Listado de Solicitudes</h5>
            </div>
            <div class="card-body">
                <table id="solicitudesTable" class="table table-hover align-middle">
                    <thead class="table-success text-center">
                        <tr>
                            <th>Foto</th>
                            <th>Cédula / Pasaporte</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Tema</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Tipo</th> <!-- Nueva columna -->
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de asignación de tutor -->
    <div class="modal fade" id="asignarTutorModal" tabindex="-1" aria-labelledby="asignarTutorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="asignarTutorModalLabel">Asignar Tutor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="asignarTutorForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="docente" class="form-label">Seleccionar Tutor</label>
                            <select class="form-select" id="docente" name="dni">
                                @foreach ($docentes as $docente)
                                    <option value="{{ $docente->dni }}">{{ $docente->nombre1 }} {{ $docente->nombre2 }}
                                        {{ $docente->apellidop }} {{ $docente->apellidom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="tesisId" name="tesisId">
                        <button type="submit" class="btn btn-success">Asignar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#solicitudesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('tesis.index') }}",
                columns: [{
                        data: 'alumno_image',
                        render: function(data) {
                            return data ?
                                `<img src="${data}" alt="Foto" class="rounded-circle" width="50" height="50">` :
                                '<span class="text-muted">Sin foto</span>';
                        },
                        className: 'text-center'
                    },
                    {
                        data: 'alumno.dni',
                        className: 'text-center'
                    },
                    {
                        data: 'nombre_completo'
                    },
                    {
                        data: 'alumno.email_institucional'
                    },
                    {
                        data: 'alumno.celular'
                    },
                    {
                        data: 'tema'
                    },
                    {
                        data: 'descripcion'
                    },
                    {
                        data: 'estado',
                        className: 'text-center'
                    },
                    {
                        data: 'tipo',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (data) {
                                return data.replace(/\w\S*/g, function(word) {
                                    return word.charAt(0).toUpperCase() + word.slice(1)
                                        .toLowerCase();
                                });
                            }
                            return '';
                        }
                    },
                    {
                        data: 'acciones',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (row.tipo === 'examen complexivo') {
                                return '';
                            }
                            return data;
                        }
                    }
                ],
                responsive: true,
                colReorder: true,
                keys: true,
                autoFill: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                }
            });
        });
    </script>
    <script>
        function mostrarModalAsignarTutor(id) {
            $('#tesisId').val(id);
            $('#asignarTutorForm').attr('action', '/tesis/asignar-tutor/' + id);
            $('#asignarTutorModal').modal('show');
        }

        function verSolicitud(url) {
            window.open(url, '_blank');
        }

        function aceptarTema(id) {
            if (confirm('¿Estás seguro de que quieres aceptar este tema?')) {
                $.ajax({
                    url: '/tesis/aceptar/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.success);
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('Ocurrió un error al aceptar el tema.');
                    }
                });
            }
        }

        function rechazarTema(id) {
            if (confirm('¿Estás seguro de que quieres rechazar este tema?')) {
                $.ajax({
                    url: '/tesis/rechazar/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.success);
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('Ocurrió un error al rechazar el tema.');
                    }
                });
            }
        }
    </script>
@stop
