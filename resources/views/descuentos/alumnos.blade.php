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
                            <!-- DataTables carga los alumnos -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @include('modales.asignar_descuento_modal')
    @include('modales.botones_alumnos_modal')
@stop

@section('js')
    <script>
        $(document).ready(function() {

            // Inicializar DataTable
            let alumnosTable = $('#alumnos').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('descuentos.alumnos') }}",
                columns: [
                    { data: 'dni', name: 'dni' },
                    { data: 'foto', name: 'foto', orderable: false, searchable: false },
                    { data: 'nombre_completo', name: 'nombre_completo' },
                    { data: 'maestria_nombre', name: 'maestria.nombre' },
                    { data: 'email_institucional', name: 'email_institucional' },
                    { data: 'sexo', name: 'sexo' },
                    { data: 'descuento_nombre', name: 'descuento_nombre', orderable: false, searchable: false },
                    { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
                ],
                language: { url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json" }
            });

            // Abrir modal para seleccionar descuento
            $('#alumnos').on('click', '.select-descuento', function() {
                let dniAlumno = $(this).data('dni');

                // Spinner mientras carga
                $('#contenedorTablaDescuentos').html('<div class="spinner-border text-primary"></div>');
                $('#formSeleccionarDescuento')[0].reset();
                $('#dniAlumnoInput').val(dniAlumno);
                $('#maestriaSelect').empty();
                $('#documentoAutenticidad').addClass('d-none');
                $('#modalSeleccionarDescuento').modal('show');

                // Obtener maestrías pendientes y descuentos
                $.ajax({
                    url: `/descuentos/alumno/${dniAlumno}`,
                    method: 'GET',
                    success: function(response) {
                        const descuentos = response.descuentos;
                        const maestriasPendientes = response.maestrias;

                        // Si no hay maestrías pendientes
                        if (maestriasPendientes.length === 0) {
                            $('#contenedorTablaDescuentos').html('<div class="alert alert-info text-center">Todas las maestrías ya tienen descuento aplicado.</div>');
                            return;
                        }

                        // Llenar select con maestrías pendientes
                        maestriasPendientes.forEach(m => {
                            $('#maestriaSelect').append(`<option value="${m.id}" data-arancel="${m.arancel}">${m.nombre}</option>`);
                        });

                        // Función para renderizar tabla de descuentos
                        function renderTabla(arancel) {
                            let html = `<table class="table table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <th>Tipo</th>
                                        <th>%</th>
                                        <th>Arancel</th>
                                        <th>Descuento</th>
                                        <th>Total</th>
                                        <th>Requisitos</th>
                                        <th>Seleccionar</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                            if (Object.keys(descuentos).length > 0) {
                                html += Object.entries(descuentos).map(([tipo, d]) => {
                                    let descuentoMonto = (d.porcentaje / 100) * arancel;
                                    let total = arancel - descuentoMonto;
                                    return `<tr class="text-center">
                                        <td>${tipo.charAt(0).toUpperCase() + tipo.slice(1)}</td>
                                        <td>${d.porcentaje}%</td>
                                        <td>$${arancel.toFixed(2)}</td>
                                        <td>$${descuentoMonto.toFixed(2)}</td>
                                        <td>$${total.toFixed(2)}</td>
                                        <td class="text-left"><ul>${d.requisitos.map(req => `<li>${req}</li>`).join('')}</ul></td>
                                        <td><input type="radio" name="descuento_id" value="${d.id}" data-tipo="${tipo}"></td>
                                    </tr>`;
                                }).join('');
                            } else {
                                html += `<tr><td colspan="7" class="text-center text-muted">No hay descuentos disponibles.</td></tr>`;
                            }

                            html += '</tbody></table>';
                            $('#contenedorTablaDescuentos').html(html);

                            // Mostrar campo de documento si el tipo requiere
                            $('input[name="descuento_id"]').off('change').on('change', function() {
                                $('#documentoAutenticidad').toggle($(this).data('tipo') === 'mejor_graduado');
                            });
                        }

                        // Render inicial con primera maestría
                        let arancelInicial = parseFloat($('#maestriaSelect option:selected').data('arancel')) || 0;
                        renderTabla(arancelInicial);

                        // Re-render al cambiar maestría
                        $('#maestriaSelect').off('change').on('change', function() {
                            let arancel = parseFloat($(this).find(':selected').data('arancel')) || 0;
                            renderTabla(arancel);
                        });

                    },
                    error: function() {
                        $('#contenedorTablaDescuentos').html('<div class="alert alert-danger">Error al cargar descuentos</div>');
                    }
                });
            });

            // Validar antes de enviar
            $('#formSeleccionarDescuento').on('submit', function(e) {
                if (!$('input[name="descuento_id"]:checked').val()) {
                    e.preventDefault();
                    alert('Por favor selecciona un descuento antes de confirmar.');
                }
            });

            // Limpiar modal al cerrar
            $('#modalSeleccionarDescuento').on('hidden.bs.modal', function() {
                $('#contenedorTablaDescuentos').empty();
                $('#documentoAutenticidad').addClass('d-none');
                $('#formSeleccionarDescuento')[0].reset();
                $('#maestriaSelect').empty();
            });

            // Modal de reportes
            $(document).on('click', '.open-reportes', function() {
                let dni = $(this).data('dni');
                let nombre = $(this).data('nombre');
                let maestrias = JSON.parse($(this).data('maestrias'));

                $('#modalAlumnoNombre').text(nombre);
                let tbody = $('#tablaReportes');
                tbody.empty();

                maestrias.forEach(m => {
                    tbody.append(`
                        <tr>
                            <td>${m.nombre}</td>
                            <td>
                                <a href="/certificado_culminacion/${dni}/${m.id}" target="_blank" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-pdf"></i> Culminación
                                </a>
                            </td>
                        </tr>
                    `);
                });

                new bootstrap.Modal(document.getElementById("modalReportes")).show();
            });

        });
    </script>
@stop


