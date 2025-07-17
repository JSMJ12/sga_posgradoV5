@extends('adminlte::page')

@section('title', 'Gestión de Alumnos')

@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Alumnos</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #446f5f;">
                <h3 class="card-title">Listado de Alumnos</h3>
                <div class="card-tools">
                    <a href="{{ route('alumnos.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Agregar nuevo
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="alumnos">
                        <thead style="background-color: #315d50; color: white;">
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
                ajax: "{{ route('alumnos.index') }}",
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
                responsive: true, // Habilitar el diseño responsivo

                columnDefs: [{
                        targets: [1, 6], // Aplica estilo especial a las columnas de foto y acciones
                        className: 'text-center'
                    },
                    {
                        targets: '_all',
                        className: 'align-middle' // Alinear verticalmente las columnas
                    }
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
            });

            // Manejo dinámico de los modales
            $('#alumnos').on('click', '.view-matriculas', function() {
                let alumnoId = $(this).data('id');
                let matriculas = $(this).data('matriculas');

                let modalId = `matriculasModal${alumnoId}`;
                let modalHtml = `
                    <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #003366; color: white;">
                                    <h5 class="modal-title" id="${modalId}Label">Matrículas del Alumno</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true" style="color: white;">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    ${matriculas.length > 0 
                                        ? `<table class="table">
                                                                      <thead>
                                                                          <tr>
                                                                              <th>Asignatura</th>
                                                                              <th>Docente</th>
                                                                              <th>Cohorte</th>
                                                                              <th>Aula</th>
                                                                              <th>Paralelo</th>
                                                                          </tr>
                                                                      </thead>
                                                                      <tbody>
                                                                          ${matriculas.map(m => `
                                                      <tr>
                                                          <td>${m.asignatura}</td>
                                                          <td>${m.docente}</td>
                                                          <td>${m.cohorte}</td>
                                                          <td>${m.aula}</td>
                                                          <td>${m.paralelo}</td>
                                                      </tr>`).join('')}
                                                                      </tbody>
                                                                  </table>` 
                                        : '<div class="alert alert-info">El estudiante no tiene matrículas registradas.</div>'
                                    }
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Agregar modal al DOM si no existe
                if (!$(`#${modalId}`).length) {
                    $('#dynamicModals').append(modalHtml);
                }

                // Mostrar el modal
                $(`#${modalId}`).modal('show');
            });
            setInterval(function() {
                alumnosTable.ajax.reload(null, false);
            }, 30000);
        });
    </script>
@stop
