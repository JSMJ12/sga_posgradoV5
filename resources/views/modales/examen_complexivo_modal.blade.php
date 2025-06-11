<!-- Modal Examen Complexivo -->
<div class="modal fade" id="examenComplexivoModal" tabindex="-1" role="dialog" aria-labelledby="examenComplexivoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="examenComplexivoModalLabel"><i class="fas fa-book"></i> Alumnos con Examen
                    Complexivo</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalExamenComplexivoContent">
                <!-- Se cargará vía AJAX -->
            </div>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.btn-examen-complexivo', function() {
        var cohorteId = $(this).data('id');
        var modal = $('#examenComplexivoModal');

        modal.find('#modalExamenComplexivoContent').html(`
        <div class="text-center">
            <div class="spinner-border text-warning" role="status"></div>
            <p class="mt-2">Cargando alumnos con examen complexivo...</p>
        </div>
    `);

        $.get(`/cohortes/${cohorteId}/examen-complexivo`, function(data) {
            let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Alumno</th>
                            <th>Lugar</th>
                            <th>Fecha y Hora</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

            if (data.length === 0) {
                html += `
                <tr>
                    <td colspan="4" class="text-center text-muted">No hay alumnos con examen complexivo en esta cohorte.</td>
                </tr>
            `;
            } else {
                data.forEach(item => {
                    html += `
                    <tr>
                        <td>${item.alumno}</td>
                        <td>${item.lugar ?? 'No asignado'}</td>
                        <td>${item.fecha_hora ?? 'Sin definir'}</td>
                        <td>${item.nota ?? 'No asignada aún'}</td>
                    </tr>
                `;
                });
            }

            html += `</tbody></table></div>`;
            modal.find('#modalExamenComplexivoContent').html(html);
        }).fail(function() {
            modal.find('#modalExamenComplexivoContent').html(
                '<p class="text-danger">Error al cargar la información.</p>'
            );
        });
    });
</script>
