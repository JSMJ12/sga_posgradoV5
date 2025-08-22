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
                                <th>Descuento Aplicado</th>
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
    @include('modales.asignar_descuento_modal')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar la tabla de alumnos
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
                        data: 'descuento_nombre',
                        name: 'descuento_nombre',
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
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });

            // Al hacer clic en el botón de seleccionar descuento
            $('#alumnos').on('click', '.select-descuento', function() {
                let dniAlumno = $(this).data('dni');

                // Mostrar spinner de carga
                $('#contenedorTablaDescuentos').html(`
                <div class="d-flex justify-content-center my-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            `);

                // Resetear formulario y ocultar documento de autenticidad
                $('#documentoAutenticidad').addClass('d-none');
                $('#formSeleccionarDescuento')[0].reset();
                $('#dniAlumnoInput').val(dniAlumno);

                // Abrir modal
                $('#modalSeleccionarDescuento').modal('show');

                // Cargar descuentos vía AJAX
                $.ajax({
                    url: `/descuentos/alumno/${dniAlumno}`,
                    method: "GET",
                    success: function(response) {
                        let descuentos = response.programa.descuentos;
                        let arancel = response.programa.arancel;

                        let htmlTabla = `
                        <table class="table table-striped align-middle">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>Tipo de Descuento</th>
                                    <th>Porcentaje</th>
                                    <th>Arancel Original</th>
                                    <th>Descuento</th>
                                    <th>Total con Descuento</th>
                                    <th>Requisitos</th>
                                    <th>Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody>`;

                        if (Object.keys(descuentos).length > 0) {
                            htmlTabla += Object.entries(descuentos).map(([tipo, d]) => `
                            <tr class="text-center">
                                <td><strong>${tipo.charAt(0).toUpperCase() + tipo.slice(1)}</strong></td>
                                <td>${d.porcentaje}%</td>
                                <td>$${parseFloat(arancel).toFixed(2)}</td>
                                <td>$${parseFloat(d.descuento).toFixed(2)}</td>
                                <td>$${parseFloat(d.total).toFixed(2)}</td>
                                <td class="text-left">
                                    <ul class="mb-0">
                                        ${d.requisitos.map(req => `<li>${req}</li>`).join('')}
                                    </ul>
                                </td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="radio" name="descuento_id" value="${d.id}" data-tipo="${tipo}">
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                        } else {
                            htmlTabla += `
                            <tr>
                                <td colspan="7" class="text-center text-muted">No hay descuentos disponibles.</td>
                            </tr>`;
                        }

                        htmlTabla += `</tbody></table>`;

                        $('#contenedorTablaDescuentos').html(htmlTabla);

                        // Detectar selección de descuento
                        $('input[name="descuento_id"]').on('change', function() {
                            let tipoSeleccionado = $(this).data('tipo');
                            if (tipoSeleccionado === 'mejor_graduado') {
                                $('#documentoAutenticidad').removeClass('d-none');
                            } else {
                                $('#documentoAutenticidad').addClass('d-none');
                            }
                        });
                    },
                    error: function() {
                        $('#contenedorTablaDescuentos').html(`
                        <div class="alert alert-danger text-center">
                            Error al cargar los descuentos. Intenta nuevamente.
                        </div>
                    `);
                    }
                });
            });

            // Validar selección antes de enviar el formulario
            $('#formSeleccionarDescuento').on('submit', function(e) {
                if (!$('input[name="descuento_id"]:checked').val()) {
                    e.preventDefault();
                    alert('Por favor selecciona un descuento antes de confirmar.');
                }
            });

            // Limpiar el modal cuando se cierra
            $('#modalSeleccionarDescuento').on('hidden.bs.modal', function() {
                $('#contenedorTablaDescuentos').empty();
                $('#documentoAutenticidad').addClass('d-none');
                $('#formSeleccionarDescuento')[0].reset();
            });
        });
    </script>

@stop
