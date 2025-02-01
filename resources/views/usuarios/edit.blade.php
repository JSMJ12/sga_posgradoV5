@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1><i class="fas fa-user-edit"></i> Editar Usuario</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Columna 1 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usu_nombre"><i class="fas fa-user"></i> Nombre:</label>
                            <input type="text" name="usu_nombre" id="usu_nombre" class="form-control" value="{{ old('usu_nombre', $usuario->name) }}">
                        </div>
                        <div class="form-group">
                            <label for="usu_apellido"><i class="fas fa-user-tag"></i> Apellido:</label>
                            <input type="text" name="usu_apellido" id="usu_apellido" class="form-control" value="{{ old('usu_apellido', $usuario->apellido) }}">
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $usuario->email) }}">
                        </div>
                        <div class="form-group">
                            <label for="usu_contrasena"><i class="fas fa-lock"></i> Contraseña:</label>
                            <input type="password" name="usu_contrasena" id="usu_contrasena" class="form-control">
                            <small class="form-text text-muted">Deje en blanco si no desea cambiar la contraseña.</small>
                        </div>
                    </div>

                    <!-- Columna 2 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usu_sexo"><i class="fas fa-venus-mars"></i> Sexo:</label>
                            <select name="usu_sexo" id="usu_sexo" class="form-control">
                                <option value="">Seleccione el sexo</option>
                                <option value="M" {{ $usuario->sexo == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ $usuario->sexo == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="roles"><i class="fas fa-user-shield"></i> Roles:</label>
                            @foreach ($roles as $role)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        id="role_{{ $role->id }}" {{ in_array($role->id, $usuario->roles->pluck('id')->toArray()) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group">
                            <label for="usu_foto"><i class="fas fa-image"></i> Foto:</label>
                            <input type="file" name="usu_foto" id="usu_foto" class="form-control-file" accept="image/*">
                            <div id="preview-container" style="margin-top: 10px;">
                                @if ($usuario->image && file_exists(storage_path('app/public/' . $usuario->image)))
                                    <img id="preview-image" src="{{ asset('storage/' . $usuario->image) }}" alt="Vista previa"
                                        style="max-width: 150px; max-height: 150px; object-fit: cover;" />
                                @else
                                    <img id="preview-image" src="https://ui-avatars.com/api/?name={{ urlencode($usuario->name) }}" alt="Vista previa"
                                        style="max-width: 150px; max-height: 150px; object-fit: cover;" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.getElementById('usu_foto').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewImage = document.getElementById('preview-image');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                previewImage.src = '#';
                previewImage.style.display = 'none';
            }
        });
    </script>
@stop
