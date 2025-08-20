<!-- Modal Editar Sección -->
<div class="modal fade" id="editModal{{ $seccion->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $seccion->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> <!-- modal centrado y ancho -->
        <div class="modal-content shadow-lg rounded-lg">
            <!-- Encabezado -->
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="editModalLabel{{ $seccion->id }}">
                    <i class="fas fa-edit"></i> Editar Sección
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">
                <form method="POST" action="{{ route('secciones.update', $seccion) }}">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div class="form-group mb-4">
                        <label for="nombre_{{ $seccion->id }}" class="form-label">
                            <i class="fas fa-tag"></i> {{ __('Nombre') }}
                        </label>
                        <input id="nombre_{{ $seccion->id }}" type="text" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               name="nombre" 
                               value="{{ old('nombre', $seccion->nombre) }}" required autofocus>
                        @error('nombre')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <!-- Maestrías -->
                    <div class="form-group">
                        <label for="maestrias_{{ $seccion->id }}" class="form-label">
                            <i class="fas fa-graduation-cap"></i> {{ __('Maestrías') }}
                        </label>
                        <div class="row">
                            @foreach ($maestrias as $maestria)
                                <div class="col-md-6 col-lg-4 mb-2"> <!-- responsivo -->
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               id="maestria_{{ $seccion->id }}_{{ $maestria->id }}" 
                                               type="checkbox" 
                                               name="maestrias[]" 
                                               value="{{ $maestria->id }}" 
                                               {{ $seccion->maestrias->contains($maestria->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="maestria_{{ $seccion->id }}_{{ $maestria->id }}">
                                            {{ $maestria->nombre }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('maestrias')
                            <div class="invalid-feedback d-block">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="form-group mt-4 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Actualizar') }}
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> {{ __('Cancelar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
