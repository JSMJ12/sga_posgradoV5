@extends('adminlte::page')
@section('title', 'Calificar')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Editar Notas del Alumno</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('calificaciones.update', $nota->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Actividades</th>
                                        <th>Prácticas</th>
                                        <th>Autónomo</th>
                                        <th>Examen Final</th>
                                        <th>Recuperación</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="number" step="0.01" class="form-control nota-input" name="nota_actividades" value="{{ $nota->nota_actividades }}" max="3.0" oninput="calcularTotal(this)"><i class="fas fa-pencil-alt nota-icon"></i></td>
                                        <td><input type="number" step="0.01" class="form-control nota-input" name="nota_practicas" value="{{ $nota->nota_practicas }}" max="3.0" oninput="calcularTotal(this)"><i class="fas fa-pencil-alt nota-icon"></i></td>
                                        <td><input type="number" step="0.01" class="form-control nota-input" name="nota_autonomo" value="{{ $nota->nota_autonomo }}" max="3.0" oninput="calcularTotal(this)"><i class="fas fa-pencil-alt nota-icon"></i></td>
                                        <td><input type="number" step="0.01" class="form-control nota-input" name="examen_final" value="{{ $nota->examen_final }}" max="3.0" oninput="calcularTotal(this)"><i class="fas fa-pencil-alt nota-icon"></i></td>
                                        <td><input type="number" step="0.01" class="form-control nota-input" name="recuperacion" value="{{ $nota->recuperacion }}" max="3.0" oninput="calcularTotal(this)"><i class="fas fa-pencil-alt nota-icon"></i></td>
                                        <td><input type="number" step="0.01" class="form-control total-input" name="total" value="{{ $nota->total }}" readonly><i class="fas fa-calculator total-icon"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Actualizar Notas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        function calcularTotal(input) {
            var fila = input.closest('tr');
            var notas = fila.querySelectorAll('.nota-input');
            var total = 0;
            
            notas.forEach(function(nota) {
                total += parseFloat(nota.value) || 0;
            });

            fila.querySelector('.total-input').value = total.toFixed(2);
        }
    </script>
@stop
