@extends('adminlte::page')

@section('title', 'Gestión de Alumnos')

@section('content_header')
    <h1><i class="fas fa-users"></i> Asignar Descuentos</h1>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            let alumnosTable = $('#alumnos').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('descuentos.alumnos') }}",
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
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
            });

            $('#alumnos').on('click', '.select-descuento', function() {
                let dniAlumno = $(this).data('dni'); // Usando el DNI del alumno
                $.ajax({
                    url: `/descuento/${dniAlumno}`, // Ruta con el DNI
                    method: "GET",
                    success: function(response) {
                        let descuentos = response.programa.descuentos;
                        let modalId = `descuentoModal${dniAlumno}`;
                        let modalHtml = `
                            <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="${modalId}Label">Seleccionar Descuento</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <form action="{{ route('pago.descuento.process') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="dni" value="${dniAlumno}">
                                            <div class="modal-body" style="max-height: 75vh; overflow-y: auto; overflow-x: auto;">
                                                <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                                                    <table class="table table-striped align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Tipo de Descuento</th>
                                                                <th>Arancel Original</th>
                                                                <th>Descuento</th>
                                                                <th>Total con Descuento</th>
                                                                <th>Requisitos</th>
                                                                <th>Seleccionar</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            ${
                                                                Object.entries(descuentos).map(([tipo, d]) => `
                                                                    <tr>
                                                                        <td><strong>${tipo.charAt(0).toUpperCase() + tipo.slice(1)}</strong></td>
                                                                        <td>$${response.programa.arancel}</td>
                                                                        <td>$${d.descuento}</td>
                                                                        <td>$${d.total}</td>
                                                                        <td>
                                                                            <ul class="mb-0">
                                                                                ${d.requisitos.map(req => `<li>${req}</li>`).join('')}
                                                                            </ul>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <input class="form-check-input" type="radio" name="descuento" id="descuento${tipo}" value="${tipo}">
                                                                        </td>
                                                                    </tr>
                                                                `).join('') || '<tr><td colspan="6" class="text-center text-muted">No hay descuentos disponibles.</td></tr>'
                                                            }
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div id="documentoAutenticidad" class="mt-3 d-none">
                                                    <div class="mb-3">
                                                        <label for="documento" class="form-label">Subir Documento de Autenticidad:</label>
                                                        <input type="file" class="form-control" id="documento" name="documento">
                                                    </div>
                                                    <div class="alert alert-info">
                                                        Para aplicar este descuento, debe subir el documento solicitado.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="submit" class="btn btn-primary">Confirmar Selección</button>
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>`;

                        // Agregar modal al DOM y mostrarlo
                        if (!$(`#${modalId}`).length) {
                            $('#dynamicModals').append(modalHtml);
                        }
                        $(`#${modalId}`).modal('show');

                        // Mostrar el campo de autenticidad solo si el descuento lo requiere
                        $(`#${modalId} input[name="descuento"]`).on('change', function() {
                            let selectedDescuento = $(this).val();
                            $('#documentoAutenticidad').toggleClass('d-none',
                                selectedDescuento !== 'mejor_graduado');
                        });
                    }
                });
            });

        });
    </script>
@stop
