@extends('adminlte::page')

@section('title', 'Gestión de Tutorías')

@section('content_header')
    <h1 class="text-center">Tesis Asignadas al Tutor: {{ $docente->nombre1 }} {{ $docente->apellidop }}</h1>
@stop

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tesis-table" class="table table-hover table-bordered align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>Tema de la Tesis</th>
                                <th>Descripción</th>
                                <th>Alumno</th>
                                <th>Contacto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            $('#tesis-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('tutorias.index') }}",
                columns: [{
                        data: 'tema',
                        name: 'tema'
                    },
                    {
                        data: 'descripcion',
                        name: 'descripcion'
                    },
                    {
                        data: 'alumno',
                        name: 'alumno'
                    },
                    {
                        data: 'contacto',
                        name: 'contacto',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                colReorder: true,
                keys: true,
                autoFill: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                }
            });

            // Delegar eventos para botones generados dinámicamente
            $('#tesis-table').on('click', '.certificar-alumno', function() {
                const alumnoDni = $(this).data('dni');

                fetch('{{ route('certificar.alumno') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            alumno_dni: alumnoDni
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('Error:', text);
                                throw new Error('Error en la respuesta del servidor: ' +
                                    response.statusText);
                            });
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download = 'certificacion_titulacion.pdf';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(error => {
                        console.error('Error:', error.message);
                        alert('Ocurrió un error al generar la certificación.');
                    });
            });
        });
    </script>
@endpush
