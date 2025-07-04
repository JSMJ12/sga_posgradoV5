@extends('adminlte::page')

@section('title', 'Generador de Reporte')

@section('content_header')
    <h1 class="font-weight-bold mb-4"><i class="fas fa-file-alt mr-2"></i>Generador de Reporte EPSU</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-cogs mr-2"></i>Parámetros del Reporte</h4>
                </div>
                <div class="card-body">
                    <form id="formGenerador" method="POST" action="{{ route('reporte.generar.pdf.epsu') }}" target="_blank">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="maestria" class="font-weight-bold">Selecciona una Maestría:</label>
                            <select class="form-control" id="maestria" name="maestria_id" required>
                                <option value="">-- Selecciona --</option>
                                @foreach ($maestrias as $maestria)
                                    <option value="{{ $maestria->id }}" data-cohortes='@json($maestria->cohortes)'>
                                        {{ $maestria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label for="cohorte" class="font-weight-bold">Selecciona una Cohorte:</label>
                            <select class="form-control" id="cohorte" name="cohorte_id" required disabled>
                                <option value="">-- Selecciona una maestría primero --</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5" disabled id="btnGenerar">
                                <i class="fas fa-file-pdf"></i> Generar PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectMaestria = document.getElementById('maestria');
            const selectCohorte = document.getElementById('cohorte');
            const btnGenerar = document.getElementById('btnGenerar');

            selectMaestria.addEventListener('change', function() {
                const cohortes = this.options[this.selectedIndex].getAttribute('data-cohortes');
                const cohortesData = cohortes ? JSON.parse(cohortes) : [];

                selectCohorte.innerHTML = '<option value="">-- Selecciona una cohorte --</option>';

                if (cohortesData.length > 0) {
                    cohortesData.forEach(cohorte => {
                        const option = document.createElement('option');
                        option.value = cohorte.id;
                        option.text = cohorte.nombre ?? ('Cohorte ' + cohorte.id);
                        selectCohorte.appendChild(option);
                    });
                    selectCohorte.disabled = false;
                } else {
                    selectCohorte.innerHTML = '<option value="">No hay cohortes disponibles</option>';
                    selectCohorte.disabled = true;
                }

                btnGenerar.disabled = true;
            });

            selectCohorte.addEventListener('change', function() {
                btnGenerar.disabled = !this.value;
            });
        });
    </script>
@stop
