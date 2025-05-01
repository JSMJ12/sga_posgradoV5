@extends('adminlte::page')

@section('title', 'Gestión de Descuentos')

@section('content_header')
    <h1>Descuentos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header text-white" style="background-color: #3007b8;">
            <h3 class="card-title">Gestion de Descuentos</h3>
            <div class="card-tools">
                <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#crearDescuentoModal">
                    <i class="fas fa-plus"></i> Nuevo Descuento
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped" id="descuentos-table">
                    <thead style="background-color: #28a745; color: white;">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Porcentaje</th>
                            <th>Activo</th>
                            <th>Requisitos</th> <!-- Nueva columna -->
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($descuentos as $descuento)
                            <tr>
                                <td>{{ $descuento->id }}</td>
                                <td>{{ $descuento->nombre }}</td>
                                <td>{{ $descuento->porcentaje }}%</td>
                                <td>
                                    @if ($descuento->activo)
                                        <span class="badge badge-success">Sí</span>
                                    @else
                                        <span class="badge badge-danger">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($descuento->requisitos)
                                        <ul class="mb-0 pl-3">
                                            @foreach (json_decode($descuento->requisitos, true) as $requisito)
                                                <li>{{ $requisito }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <em>Sin requisitos</em>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#editarDescuentoModal{{ $descuento->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="form-eliminar-{{ $descuento->id }}"
                                        action="{{ route('descuentos.destroy', $descuento->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmarEliminacion({{ $descuento->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal Editar --}}
                            @include('modales.editar_descuento_modal', ['descuento' => $descuento])
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>
    </div>

    {{-- Modal Crear --}}
    @include('modales.crear_descuento_modal')


@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#descuentos-table').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });
        });

        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-eliminar-' + id).submit();
                }
            });
        }
    </script>
@stop
