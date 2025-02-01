<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Encabezado del modal -->
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="createModalLabel">Crear Sección</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Cuerpo del modal -->
            <div class="modal-body">
                <form method="POST" action="{{ route('secciones.store') }}">
                    @csrf
                    <!-- Campo de nombre -->
                    <div class="form-group mb-4">
                        <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
                        <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required autofocus>
                        @error('nombre')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <!-- Campo de maestrías -->
                    <div class="form-group">
                        <label for="maestrias" class="form-label">{{ __('Maestrías') }}</label>
                        @if($maestrias_noasignadas->isEmpty())
                            <p class="text-danger">Todas las maestrías ya están asignadas a secciones.</p>
                        @else
                            <div class="checkbox-grid">
                                @foreach ($maestrias_noasignadas as $maestria)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input styled-checkbox" type="checkbox" id="maestria_{{ $maestria->id }}" name="maestrias[]" value="{{ $maestria->id }}" {{ in_array($maestria->id, old('maestrias', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="maestria_{{ $maestria->id }}">
                                            {{ $maestria->nombre }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @error('maestrias')
                            <div class="invalid-feedback d-block">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <!-- Botones del formulario -->
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Guardar') }}
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
