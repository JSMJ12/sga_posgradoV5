@extends('adminlte::page')

@section('title', 'Gestión de Alumnos')

@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Alumnos - Examen Complexivo</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Alumnos</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="alumnos">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>Cédula / Pasaporte</th>
                                <th>Foto</th>
                                <th>Nombre Completo</th>
                                <th>Maestría</th>
                                <th>Email Institucional</th>
                                <th>Sexo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- El contenido se cargará dinámicamente mediante DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="dynamicModals"></div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let alumnosTable = $('#alumnos').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('examen-complexivo.calificar') }}",
                columns: [{
                        data: 'dni',
                        name: 'dni'
                    },
                    {
                        data: 'foto',
                        name: 'foto',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nombre_completo',
                        name: 'nombre_completo'
                    },
                    {
                        data: 'maestria_nombre',
                        name: 'maestria.nombre'
                    },
                    {
                        data: 'email_institucional',
                        name: 'email_institucional'
                    },
                    {
                        data: 'sexo',
                        name: 'sexo'
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: true,
                columnDefs: [{
                        targets: [1, 6],
                        className: 'text-center'
                    },
                    {
                        targets: '_all',
                        className: 'align-middle'
                    }
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
            });


            $('#alumnos').on('click', '.btn-primary', function() {
                let alumnoId = $(this).data('dni');
                let alumnoNombre = $(this).data('nombre');

                let modalId = `calificarModal${alumnoId}`;
                let modalHtml = `
                    <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #003366; color: white;">
                                    <h5 class="modal-title" id="${modalId}Label">Calificar Examen - ${alumnoNombre}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true" style="color: white;">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="unifiedForm${alumnoId}" method="POST" action="{{ route('examen-complexivo.actualizarNotaYFechaGraduacion') }}">
                                        @csrf
                                        <input type="hidden" name="alumno_dni" value="${alumnoId}">
                                        <div class="form-group">
                                            <label for="nota">Nota:</label>
                                            <input type="number" name="nota" class="form-control" required min="0" max="10" step="0.01">
                                        </div>
                                        <div class="form-group">
                                            <label for="fecha_graduacion">Fecha de Graduación:</label>
                                            <input type="date" name="fecha_graduacion" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-success">Guardar Nota y Registrar Fecha</button>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                if (!$(`#${modalId}`).length) {
                    $('#dynamicModals').append(modalHtml);
                }

                $(`#${modalId}`).modal('show');
            });

        });
    </script>
@stop
