@extends('adminlte::page')
@section('title', 'Pagos')

@section('content_header')
    <h1>Pagos Realizados y Pendientes</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #3007b8;">
                <h3 style="color: white">Historial de Pagos</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="pagosTable">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>Imagen</th>
                                <th>Cédula/Pasaporte</th>
                                <th>Email Institucional</th>
                                <th>Celular</th>
                                <th>Monto</th>
                                <th>Fecha de Pago</th>
                                <th>Comprobante</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#pagosTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pagos.index') }}",
                columns: [{
                        data: 'alumno.image',
                        name: 'alumno.image',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<img src="/storage/${data}" alt="Imagen del Alumno" class="img-thumbnail" style="width: 50px; height: 50px;">`;
                        }
                    },
                    {
                        data: 'dni',
                        name: 'dni'
                    },
                    {
                        data: 'alumno.email_institucional',
                        name: 'alumno.email_institucional'
                    },
                    {
                        data: 'alumno.celular',
                        name: 'alumno.celular'
                    },
                    {
                        data: 'monto',
                        name: 'monto'
                    },
                    {
                        data: 'fecha_pago',
                        name: 'fecha_pago'
                    },
                    {
                        data: 'archivo_comprobante',
                        name: 'archivo_comprobante',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                            <a href="/storage/${data}" target="_blank" class="btn btn-info btn-sm" title="Ver Comprobante">
                                <i class="fas fa-file-alt"></i>
                            </a>`;
                        }
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                }
            });
        });

        // Función para confirmar la verificación del pago
        function confirmarVerificacion(pagoId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Deseas marcar este pago como aprobado?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`form-verificar-${pagoId}`).submit();
                }
            });
        }
    </script>
@stop
