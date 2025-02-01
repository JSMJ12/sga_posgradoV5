<div class="modal fade" id="editPeriodoModal{{ $periodo_academico->id }}" tabindex="-1" role="dialog" aria-labelledby="editPeriodoModalLabel{{ $periodo_academico->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="editPeriodoModalLabel{{ $periodo_academico->id }}">Editar Periodo Academico</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST"
                    action="{{ route('periodos_academicos.update', $periodo_academico->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3 row">
                        <label for="nombre"
                            class="col-md-4 col-form-label text-md-end">{{ __('Nombre') }}</label>
                        <div class="col-md-8">
                            <input id="nombre" type="text"
                                class="form-control @error('nombre') is-invalid @enderror"
                                name="nombre" value="{{ $periodo_academico->nombre }}"
                                required autocomplete="nombre" autofocus>
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="fecha_inicio"
                            class="col-md-4 col-form-label text-md-end">{{ __('Fecha de Inicio') }}</label>
                        <div class="col-md-8">
                            <input type="date" class="form-control" id="fecha_inicio"
                                name="fecha_inicio"
                                value="{{ optional($periodo_academico->fecha_inicio)->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="fecha_fin"
                            class="col-md-4 col-form-label text-md-end">{{ __('Fecha de Fin') }}</label>
                        <div class="col-md-8">
                            <input type="date" class="form-control" id="fecha_fin"
                                name="fecha_fin"
                                value="{{ optional($periodo_academico->fecha_fin)->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="submit"
                                class="btn btn-primary">{{ __('Actualizar') }}</button>
                            <a href="{{ route('periodos_academicos.index') }}"
                                class="btn btn-secondary">{{ __('Cancelar') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>