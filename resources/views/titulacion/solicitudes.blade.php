@extends('adminlte::page')

@section('title', 'Proceso de Titulación')

@section('content_header')
    <h1 class="text-center text-success">Solicitud de Aprobación de Tema</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Listado de Solicitudes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive"> {{-- envoltorio responsivo --}}
                    <table id="solicitudesTable" class="table table-hover align-middle w-100">
                        <thead class="table-success text-center">
                            <tr>
                                <th>Foto</th>
                                <th>Cédula / Pasaporte</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Tema</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Tipo</th>
                                <th>Tutor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de asignación de tutor -->
<div class="modal fade" id="asignarTutorModal" tabindex="-1" role="dialog" aria-labelledby="asignarTutorModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> {{-- modal más ancho y centrado --}}
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="asignarTutorModalLabel">Asignar Tutor</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="asignarTutorForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="docente">Seleccionar Tutor</label>
                        <select class="form-control" id="docente" name="dni">
                            @foreach ($docentes as $docente)
                                <option value="{{ $docente->dni }}">
                                    {{ $docente->nombre1 }} {{ $docente->nombre2 }}
                                    {{ $docente->apellidop }} {{ $docente->apellidom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" id="tesisId" name="tesisId">
                    <div class="text-right">
                        <button type="submit" class="btn btn-success">Asignar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
    $(function() {
        const maxChars = 40;

        function renderShortText(data) {
            if (!data) return '<span class="text-muted">Sin información</span>';
            if (data.length > maxChars) {
                const short = data.slice(0, maxChars) + '...';
                return `<span class="toggle-text" data-full="${data}" data-short="${short}" onclick="toggleText(this)" style="cursor:pointer;" title="Click para expandir/contraer">${short}</span>`;
            }
            return data;
        }

        $('#solicitudesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('tesis.index') }}",
            responsive: true, // 🔹 hace la tabla adaptable
            scrollX: true,    // 🔹 evita desbordes horizontales
            columns: [
                {
                    data: 'alumno_image',
                    render: data => data
                        ? `<img src="${data}" alt="Foto" class="rounded-circle img-fluid" style="max-width:50px; max-height:50px;">`
                        : '<span class="text-muted">Sin foto</span>',
                    className: 'text-center'
                },
                { data: 'alumno.dni', className: 'text-center' },
                { data: 'nombre_completo' },
                { data: 'alumno.email_institucional' },
                { data: 'alumno.celular' },
                { data: 'tema', render: renderShortText },
                { data: 'descripcion', render: renderShortText },
                { data: 'estado', className: 'text-center' },
                {
                    data: 'tipo',
                    className: 'text-center',
                    render: data => data
                        ? data.replace(/\w\S*/g, w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
                        : ''
                },
                { data: 'tutor', className: 'text-center' },
                {
                    data: 'acciones',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (data, type, row) => row.tipo === 'examen complexivo' ? '' : data
                }
            ],
            colReorder: true,
            keys: true,
            autoFill: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            }
        });
    });

    function mostrarModalAsignarTutor(id) {
        $('#tesisId').val(id);
        $('#asignarTutorForm').attr('action', '/tesis/asignar-tutor/' + id);
        $('#asignarTutorModal').modal('show');
    }

    function verSolicitud(url) {
        window.open(url, '_blank');
    }

    function aceptarTema(id) {
        if (confirm('¿Estás seguro de que quieres aceptar este tema?')) {
            $.post('/tesis/aceptar/' + id, { _token: '{{ csrf_token() }}' })
                .done(response => {
                    alert(response.success);
                    location.reload();
                })
                .fail(xhr => {
                    console.error('Error:', xhr.responseText);
                    alert('Ocurrió un error al aceptar el tema.');
                });
        }
    }

    function rechazarTema(id) {
        if (confirm('¿Estás seguro de que quieres rechazar este tema?')) {
            $.post('/tesis/rechazar/' + id, { _token: '{{ csrf_token() }}' })
                .done(response => {
                    alert(response.success);
                    location.reload();
                })
                .fail(xhr => {
                    console.error('Error:', xhr.responseText);
                    alert('Ocurrió un error al rechazar el tema.');
                });
        }
    }

    function toggleText(element) {
        element.innerText = element.innerText.endsWith('...') ? element.dataset.full : element.dataset.short;
    }
</script>
@stop
