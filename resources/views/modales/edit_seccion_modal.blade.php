<div class="modal fade" id="editModal{{ $seccion->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $seccion->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Encabezado del modal -->
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="editModalLabel{{ $seccion->id }}">Editar Sección</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Cuerpo del modal -->
            <div class="modal-body">
                <form method="POST" action="{{ route('secciones.update', $seccion) }}">
                    @csrf
                    @method('PUT')

                    <!-- Campo de nombre -->
                    <div class="form-group mb-4">
                        <label for="nombre_{{ $seccion->id }}" class="form-label">{{ __('Nombre') }}</label>
                        <input id="nombre_{{ $seccion->id }}" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre', $seccion->nombre) }}" required autofocus>
                        @error('nombre')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <!-- Campo de maestrías -->
                    <div class="form-group">
                        <label for="maestrias_{{ $seccion->id }}" class="form-label">{{ __('Maestrías') }}</label>
                        <div class="checkbox-grid">
                            @foreach ($maestrias as $maestria)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input styled-checkbox" id="maestria_{{ $seccion->id }}_{{ $maestria->id }}" type="checkbox" name="maestrias[]" value="{{ $maestria->id }}" {{ $seccion->maestrias->contains($maestria->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maestria_{{ $seccion->id }}_{{ $maestria->id }}">
                                        {{ $maestria->nombre }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('maestrias')
                            <div class="invalid-feedback d-block">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <!-- Botones del formulario -->
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Actualizar') }}
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Cancelar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
