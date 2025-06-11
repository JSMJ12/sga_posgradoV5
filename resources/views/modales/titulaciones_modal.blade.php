<!-- Modal -->
<div class="modal fade" id="procesoTitulacionModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel">Proceso de Titulación</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalProcesoTitulacionContent">
                <!-- Aquí se cargará el contenido con AJAX -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Cargando datos...</p>
                </div>
            </div>
        </div>
    </div>

</div>
@include('modales.enviar_mensaje_modal')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '.btn-proceso-titulacion', function() {
        var cohorteId = $(this).data('id');
        var modal = $('#procesoTitulacionModal');

        modal.find('#modalProcesoTitulacionContent').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Cargando datos...</p>
            </div>
        `);

        $.get(`/cohortes/${cohorteId}/proceso-titulacion`, function(data) {
            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Alumno</th>
                                <th>Estado Tesis</th>
                                <th>Tutorías</th>
                                <th>Tutorías Completadas</th>
                                <th>Tutor</th>
                                <th>Fecha de Graduación</th>
                                <th>Mensajería</th>

                            </tr>
                        </thead>
                        <tbody>
            `;

            if (data.length === 0) {
                html += `
                    <tr>
                        <td colspan="6" class="text-center text-muted">No hay alumnos registrados en esta cohorte.</td>
                    </tr>
                `;
            } else {
                data.forEach(item => {
                    const alumno = item.alumno.full_name ?? 'Desconocido';

                    const estado = item.estado_tesis;
                    const badgeClass = {
                        aprobado: 'success',
                        pendiente: 'warning',
                        rechazado: 'danger'
                    } [estado] || 'secondary';

                    let tutoriasHtml = `<span class="text-muted">N/A</span>`;
                    if (item.tiene_tesis) {
                        tutoriasHtml = '<ul class="mb-0 pl-3">';
                        item.tutorias.forEach(t => {
                            let estadoTutoria = t.estado === 'realizada' ? 'success' :
                                'warning';
                            tutoriasHtml += `
                                <li>${t.fecha ?? 'Sin fecha'} - 
                                    <span class="badge badge-${estadoTutoria}">${capitalize(t.estado)}</span>
                                </li>
                            `;
                        });
                        tutoriasHtml += '</ul>';
                    }
                    const tutorId = item.tutor_user_id;
                    const tutorNombre = item.tutor ?? 'Sin tutor';
                    const btnMensajeria = tutorId ?
                        `<button type="button"
                            class="btn btn-outline-info btn-sm btn-message"
                            data-id="${tutorId}"
                            data-nombre="${tutorNombre}"
                            data-toggle="modal"
                            data-target="#sendMessageModal"
                            title="Enviar mensaje">
                            <i class="fas fa-envelope"></i>
                    </button>` :
                        `<span class="text-muted">No disponible</span>`;
                    html += `
                        <tr>
                            <td>${alumno}</td>
                            <td><span class="badge badge-${badgeClass}">${capitalize(estado)}</span></td>
                            <td>${tutoriasHtml}</td>
                            <td>${item.tutorias_completadas} / 3</td>
                            <td>${item.tutor ?? 'Sin tutor asignado'}</td>
                            <td>${item.graduado ?? 'No graduado'}</td>
                            <td class="text-center">${btnMensajeria}</td>
                        </tr>
                    `;
                });
            }

            html += `</tbody></table></div>`;
            modal.find('#modalProcesoTitulacionContent').html(html);
            modal.modal('show');
        }).fail(function() {
            modal.find('#modalProcesoTitulacionContent').html(
                '<p class="text-danger">Error al cargar la información.</p>');
        });
    });

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
</script>
