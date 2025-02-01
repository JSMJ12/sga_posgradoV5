@extends('adminlte::page')

@section('title', 'Secretarios')

@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Secretarios</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 class="card-title">Listado de Secretarios</h3>
                <div class="card-tools">
                    <a href="{{ route('secretarios.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Agregar nuevo
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="secretarios">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>Cédula / Pasaporte</th>
                                <th>Foto</th>
                                <th>Nombres</th>
                                <th>Email</th>
                                <th>Sección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#secretarios').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('secretarios.index') }}",
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
                        data: null,
                        render: function(data) {
                            return `${data.apellidop} ${data.apellidom} <br> ${data.nombre1} ${data.nombre2}`;
                        },
                        name: 'nombres'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'seccion.nombre',
                        name: 'seccion.nombre',
                        render: function(data, type, row) {
                            return `<button type="button" class="btn btn-info mostrar-seccion" data-id="${row.seccion.id}" title="Mostrar Sección">
                                    <i class="fas fa-eye"></i>
                                </button>`;
                        }
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                colReorder: true,
                keys: true,
                autoFill: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                }
            });

            // Cargar modal dinámicamente al hacer clic en el botón
            $(document).on('click', '.mostrar-seccion', function() {
                var seccionId = $(this).data('id');
                // Llamada AJAX para cargar datos de la sección
                $.ajax({
                    url: "/secciones/" +
                    seccionId, // Asume que tienes una ruta para obtener los detalles de la sección
                    method: "GET",
                    success: function(data) {
                        // Crear el modal
                        var modalHtml = `
                        <div class="modal fade" id="mostrarSeccionModal_${data.id}" tabindex="-1" role="dialog" aria-labelledby="mostrarSeccionModalLabel_${data.id}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #003366; color: white;">
                                        <h5 class="modal-title" id="mostrarSeccionModalLabel_${data.id}">Información de la Sección</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" style="color: white;">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Nombre de la sección:</strong> ${data.nombre}</p>
                                        <p><strong>Maestrías asociadas:</strong></p>
                                        <ul>
                                            ${data.maestrias.map(maestria => `<li>${maestria.nombre}</li>`).join('')}
                                        </ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                        // Append the modal HTML to the body and show it
                        $('body').append(modalHtml);
                        $('#mostrarSeccionModal_' + data.id).modal('show');
                    }
                });
            });
        });
    </script>

@stop
