<!-- Modal -->
<div class="modal fade" id="verificarDocumentosModal_{{ $postulante->dni }}" tabindex="-1" role="dialog"
    aria-labelledby="verificarDocumentosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #003366; color: white;">
                <h5 class="modal-title" id="verificarDocumentosLabel">Verificar Documentos - {{ $postulante->apellidop }} {{ $postulante->apellidom }} {{ $postulante->nombre1 }} {{ $postulante->nombre2 }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('documentos.verificar', $postulante->dni) }}">
                    @csrf
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Acción</th>
                                <th>Verificado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $documentos = [
                                    [
                                        'archivo' => $postulante->pdf_cedula,
                                        'titulo' => 'Cédula',
                                        'color' => 'primary',
                                        'verificado' => $postulante->cedula_verificado,
                                    ],
                                    [
                                        'archivo' => $postulante->pdf_papelvotacion,
                                        'titulo' => 'Papel Votación',
                                        'color' => 'success',
                                        'verificado' => $postulante->papel_votacion_verificado,
                                    ],
                                    [
                                        'archivo' => $postulante->pdf_titulouniversidad,
                                        'titulo' => 'Título Universidad',
                                        'color' => 'warning',
                                        'verificado' => $postulante->titulo_verificado,
                                    ],
                                    [
                                        'archivo' => $postulante->pdf_hojavida,
                                        'titulo' => 'Hoja de Vida',
                                        'color' => 'info',
                                        'verificado' => $postulante->hoja_vida_verificado,
                                    ],
                                    [
                                        'archivo' => $postulante->pdf_conadis,
                                        'titulo' => 'CONADIS',
                                        'color' => 'secondary',
                                        'verificado' => $postulante->conadis_verificado,
                                    ],
                                    [
                                        'archivo' => $postulante->carta_aceptacion,
                                        'titulo' => 'Carta de Aceptación',
                                        'color' => 'secondary',
                                        'verificado' => $postulante->carta_aceptacion_verificado,
                                    ],
                                    [
                                        'archivo' => $postulante->pago_matricula,
                                        'titulo' => 'Comprobante de Pago',
                                        'color' => 'danger',
                                        'verificado' => $postulante->pago_matricula_verificado,
                                    ],
                                ];
                            @endphp

                            @php
                                $documentosVerificados = $postulante->documentos_verificados ?? collect(); // Asegurar que sea una colección
                            @endphp

                            @foreach ($documentos as $documento)
                                @if ($documento['archivo'])
                                    @php
                                        $tituloDocumento = $documento['titulo'];
                                        $docVerificado = $documentosVerificados->firstWhere(
                                            'tipo_documento',
                                            $tituloDocumento,
                                        );
                                    @endphp
                                    <tr>
                                        <td>{{ $documento['titulo'] }}</td>
                                        <td>
                                            <a href="{{ asset('storage/' . $documento['archivo']) }}" target="_blank"
                                                class="btn btn-outline-{{ $documento['color'] }} btn-sm"
                                                title="Ver {{ $documento['titulo'] }}">
                                                <i class="fas fa-file-pdf"></i> Abrir
                                            </a>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    name="documentos_verificados[{{ $documento['titulo'] }}]"
                                                    value="1"
                                                    {{ $docVerificado && $docVerificado->verificado ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                        </tbody>
                    </table>
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
