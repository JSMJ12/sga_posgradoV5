<!-- Modal -->
<div class="modal fade" id="verificacionModal" tabindex="-1" aria-labelledby="verificacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="verificacionModalLabel">Verificaciones del Cohorte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="verificacionesContent" class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Docente</th>
                                <th>Asignatura</th>
                                <th>Calificado</th>
                            </tr>
                        </thead>
                        <tbody id="verificacionesTableBody">
                            <!-- Contenido se llena por JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

