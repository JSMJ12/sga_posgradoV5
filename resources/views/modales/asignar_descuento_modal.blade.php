<!-- Modal para seleccionar descuento -->
<div class="modal fade" id="modalSeleccionarDescuento" tabindex="-1" role="dialog" aria-labelledby="modalSeleccionarDescuentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSeleccionarDescuentoLabel">Seleccionar Descuento</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSeleccionarDescuento" action="{{ route('descuentos.procesar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="dni" id="dniAlumnoInput">
                <div class="modal-body" style="max-height: 75vh; overflow-y: auto; overflow-x: auto;">
                    <div id="contenedorTablaDescuentos" class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                        <!-- Aquí se cargará la tabla de descuentos dinámicamente -->
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
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
