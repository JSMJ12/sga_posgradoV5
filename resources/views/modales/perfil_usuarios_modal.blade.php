
<div class="modal fade" id="cambiarPerfilModal" tabindex="-1" role="dialog" aria-labelledby="cambiarPerfilLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded">
            <div class="modal-header text-white"
                style="background: linear-gradient(90deg, #003366, #0055aa); border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                <h5 class="modal-title" id="cambiarPerfilLabel"><i class="fas fa-user-edit mr-2"></i>Editar Perfil</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="POST" action="{{ route('usuario.actualizarPerfil') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body px-4">

                    <!-- Imagen de Perfil -->
                    <div class="form-group text-center mb-4">
                        <label class="font-weight-bold">Imagen de Perfil</label>
                        <div class="mb-3">
                            <img id="previewImage" src="{{ asset('storage/' . Auth::user()->image) }}"
                                alt="Imagen actual"
                                style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; box-shadow: 0 0 10px rgba(0,0,0,0.2);">
                        </div>
                        <input type="file" name="image" class="form-control-file" accept="image/*"
                            onchange="previewImage(event)">
                    </div>

                    <!-- Mostrar opción para cambiar contraseña -->
                    <div class="form-group text-center mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="togglePasswordFields()">
                            <i class="fas fa-key mr-1"></i>¿Cambiar contraseña?
                        </button>
                    </div>

                    <!-- Campos de contraseña -->
                    <div id="passwordFields" style="display: none;">
                        <div class="form-group mb-3">
                            <label for="password" class="font-weight-bold">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-control" minlength="8"
                                placeholder="Mínimo 8 caracteres">
                        </div>
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="font-weight-bold">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8"
                                placeholder="Repite la contraseña">
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 justify-content-between px-4 pb-4">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('previewImage').src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    function togglePasswordFields() {
        const fields = document.getElementById('passwordFields');
        fields.style.display = fields.style.display === 'none' ? 'block' : 'none';
    }
</script>
