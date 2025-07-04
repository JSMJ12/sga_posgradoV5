<!-- Modal Bootstrap 4 -->
<div class="modal fade" id="verificacionModal" tabindex="-1" role="dialog" aria-labelledby="verificacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #001f3f;">
                <h5 class="modal-title" id="verificacionModalLabel">Verificaciones del Cohorte</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="verificacionesContent" class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>Docente</th>
                                <th>Asignatura</th>
                                <th>Calificado</th>
                                <th>Notas Alumnos</th>
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
