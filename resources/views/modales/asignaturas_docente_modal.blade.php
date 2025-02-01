<div class="modal fade" id="asignaturasModal" tabindex="-1" role="dialog" aria-labelledby="asignaturasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="asignaturasModalLabel">Asignaturas del Docente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-asignaturas-content">
                    <!-- Aquí se cargará el contenido dinámico -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Delegado de eventos para manejar botones dinámicos
        $(document).on('click', '.btn-modal-asignatura', function() {
            const docenteDni = $(this).data('id'); // DNI del docente
            const modal = $('#asignaturasModal');
            const modalTitle = modal.find('#asignaturasModalLabel');
            const modalContent = modal.find('#modal-asignaturas-content');

            // Cambiar el título mientras carga
            modalTitle.text('Cargando asignaturas del docente...');

            // Mostrar spinner de carga
            modalContent.html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>
    `);

            // Hacer la solicitud AJAX
            $.ajax({
                url: `/docentes/${docenteDni}/asignaturas`,
                method: 'GET',
                success: function(response) {
                    if (response.length === 0) {
                        modalContent.html(`
                    <div class="alert alert-info" role="alert">
                        No hay asignaturas asignadas a este docente.
                    </div>
                `);
                    } else {
                        let tableRows = response.map(asignatura => `
                    <tr>
                        <td>${asignatura.nombre}</td>
                        <td>${asignatura.codigo_asignatura}</td>
                        <td>${asignatura.credito}</td>
                        
                        <td>
                            <form action="/docentes/${docenteDni}/asignaturas/${asignatura.id}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                `).join('');

                        modalContent.html(`
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Asignatura</th>
                                    <th>Código</th>
                                    <th>Créditos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                `);
                    }

                    // Actualizar el título del modal
                    modalTitle.text('Asignaturas del Docente');
                },
                error: function() {
                    modalContent.html(`
                <div class="alert alert-danger" role="alert">
                    Ocurrió un error al cargar las asignaturas.
                </div>
            `);
                }
            });

            // Mostrar el modal
            modal.modal('show');
        });
    });
</script>
