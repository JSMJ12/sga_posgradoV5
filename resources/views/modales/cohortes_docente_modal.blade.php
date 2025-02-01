<!-- Modal Base -->
<div class="modal fade" id="cohortesModal" tabindex="-1" role="dialog" aria-labelledby="cohortesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="cohortesModalLabel">Cohortes del Docente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Contenido dinámico cargado vía AJAX -->
                <div id="cohortesContent">
                    <div class="text-center">
                        <p>Cargando información...</p>
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Script para manejar el contenido dinámico -->
<script>
    $(document).on('click', '.btn-modal-cohortes', function() {
        const docenteDni = $(this).data('dni'); // Obtener el DNI del docente del botón clickeado
        const docenteNombre = $(this).data('nombre'); // Obtener el nombre del docente del botón
        const modal = $('#cohortesModal');
        const modalTitle = modal.find('#cohortesModalLabel');
        const modalBody = modal.find('#cohortesContent');


        // Mostrar spinner de carga
        modalBody.html(`
            <div class="text-center">
                <p>Cargando información...</p>
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        `);

        // Hacer la solicitud AJAX
        $.ajax({
            url: `/docentes/${docenteDni}/cohortes`,
            method: 'GET',
            success: function(response) {
                if (response.error) {
                    modalBody.html(`
                        <div class="alert alert-danger" role="alert">
                            ${response.error}
                        </div>
                    `);
                } else if (response.cohortes.length === 0) {
                    modalBody.html(`
                        <div class="alert alert-warning" role="alert">
                            No se encontraron cohortes para este docente.
                        </div>
                    `);
                } else {
                    // Procesar los cohortes y asignaturas
                    let tableRows = response.cohortes.map(cohorte => {
                        // Obtener la primera asignatura para la fila principal
                        let firstAsignatura = cohorte.asignaturas[0];

                        // Crear la fila principal con rowspan
                        let mainRow = `
        <tr>
            <td rowspan="${cohorte.asignaturas.length}">${cohorte.maestria}</td>
            <td rowspan="${cohorte.asignaturas.length}">${cohorte.nombre}</td>
            <td rowspan="${cohorte.asignaturas.length}">${cohorte.modalidad}</td>
            <td rowspan="${cohorte.asignaturas.length}">${cohorte.aula}</td>
            <td rowspan="${cohorte.asignaturas.length}">${cohorte.paralelo}</td>
            <td>${firstAsignatura.nombre}</td>
            <td>${firstAsignatura.calificado}</td>
            <td>
                <div class="form-check form-check-inline">
                    <input type="radio" id="permiso_editar_si_${cohorte.id}_${firstAsignatura.id}" 
                        name="permiso_editar[${cohorte.id}][${firstAsignatura.id}]" 
                        value="1" class="form-check-input" ${firstAsignatura.editar ? 'checked' : ''}>
                    <label for="permiso_editar_si_${cohorte.id}_${firstAsignatura.id}" class="form-check-label">Sí</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" id="permiso_editar_no_${cohorte.id}_${firstAsignatura.id}" 
                        name="permiso_editar[${cohorte.id}][${firstAsignatura.id}]" 
                        value="0" class="form-check-input" ${!firstAsignatura.editar ? 'checked' : ''}>
                    <label for="permiso_editar_no_${cohorte.id}_${firstAsignatura.id}" class="form-check-label">No</label>
                </div>
            </td>
        </tr>
    `;

                        // Crear filas para el resto de las asignaturas
                        let asignaturasRows = cohorte.asignaturas.slice(1).map(asignatura => `
        <tr>
            <td>${asignatura.nombre}</td>
            <td>${asignatura.calificado}</td>
            <td>
                <div class="form-check form-check-inline">
                    <input type="radio" id="permiso_editar_si_${cohorte.id}_${asignatura.id}" 
                        name="permiso_editar[${cohorte.id}][${asignatura.id}]" 
                        value="1" class="form-check-input" ${asignatura.editar ? 'checked' : ''}>
                    <label for="permiso_editar_si_${cohorte.id}_${asignatura.id}" class="form-check-label">Sí</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" id="permiso_editar_no_${cohorte.id}_${asignatura.id}" 
                        name="permiso_editar[${cohorte.id}][${asignatura.id}]" 
                        value="0" class="form-check-input" ${!asignatura.editar ? 'checked' : ''}>
                    <label for="permiso_editar_no_${cohorte.id}_${asignatura.id}" class="form-check-label">No</label>
                </div>
            </td>
        </tr>
    `).join('');

                        // Unir la fila principal con las filas de asignaturas adicionales
                        return mainRow + asignaturasRows;
                    }).join('');


                    modalBody.html(`
                        <form action="{{ route('guardarCambios') }}" method="POST">
                            @csrf
                            <input type="hidden" name="docente_dni" value="${docenteDni}">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Maestría</th>
                                            <th>Cohorte</th>
                                            <th>Modalidad</th>
                                            <th>Aula</th>
                                            <th>Paralelo</th>
                                            <th>Asignatura</th>
                                            <th>Estado</th>
                                            <th>Permiso de Editar</th>
                                        </tr>
                                    </thead>
                                    <tbody>${tableRows}</tbody>
                                </table>
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                            </div>
                        </form>
                    `);
                }
            },
            error: function() {
                modalBody.html(`
                    <div class="alert alert-danger" role="alert">
                        Ocurrió un error al cargar los cohortes.
                    </div>
                `);
            }
        });

        // Mostrar el modal
        modal.modal('show');
    });
</script>

<!-- Estilos CSS -->
<style>
    .table-sm td,
    .table-sm th {
        padding: 0.2rem;
        font-size: 0.9rem;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .table th {
        background-color: #003366;
        color: white;
    }

    .modal-header {
        background-color: #003366;
        color: white;
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .form-check-inline {
        margin-right: 10px;
    }

    .alert {
        font-size: 1rem;
    }
</style>
