@extends('adminlte::page')

@section('title', 'Pagos de Matrícula de Postulantes')

@section('content_header')
    <h1>Pagos de Matrícula de Postulantes</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #446f5f;">
                <h3 style="color: white">Historial de Pagos de Matrícula de Postulantes</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="pagosMatriculaTable">
                        <thead style="background-color: #28a745; color: white;">
                            <tr>
                                <th>Imagen</th>
                                <th>DNI</th>
                                <th>Nombre Completo</th>
                                <th>Email Institucional</th>
                                <th>Celular</th>
                                <th>Comprobante</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se insertan dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let table = $('#pagosMatriculaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pagos.matricula') }}",
                columns: [{
                        data: 'foto',
                        name: 'foto',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'dni',
                        name: 'dni',
                    },
                    {
                        data: 'nombre_completo',
                        name: 'nombre_completo',
                        orderable: false
                    },
                    {
                        data: 'correo_electronico',
                        name: 'correo_electronico'
                    },
                    {
                        data: 'celular',
                        name: 'celular'
                    },
                    {
                        data: 'archivo_comprobante',
                        name: 'archivo_comprobante',
                        orderable: false,
                        searchable: false
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

            setInterval(function() {
                table.ajax.reload(null, false);
            }, 40000);
        });

        function confirmarVerificacion(postulanteDni) {
            postulanteDni = String(postulanteDni);
            console.log("DNI del postulante:", postulanteDni); // Verificar si el DNI está correcto

            // Verificar el HTML del formulario
            var formId = `form-verificar-${postulanteDni}`;
            console.log("Formulario ID generado:", formId); // Imprimir el ID del formulario generado

            // Mostrar el cuadro de confirmación
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
                    console.log("El usuario ha confirmado la acción.");
                    var form = document.getElementById(formId);

                    if (form) {
                        console.log("Formulario encontrado:", form);
                        form.submit(); // Enviar el formulario
                    } else {
                        console.log("Formulario no encontrado");
                        Swal.fire('Error', 'No se encontró el formulario para este postulante.', 'error');
                    }
                } else {
                    console.log("El usuario ha cancelado la acción.");
                }
            }).catch((error) => {
                console.error("Hubo un error en la confirmación:", error);
            });
        }
    </script>
@stop
