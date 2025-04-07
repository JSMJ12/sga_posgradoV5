@extends('adminlte::page')

@section('title', 'Mensajería')

@section('content_header')
    <h1 class="text-center"><i class="fas fa-envelope"></i> Mensajería</h1>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-navy text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-list"></i> Lista de Mensajes</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="mensajes">
                                <thead class="thead-dark">
                                    <tr class="text-center">
                                        <th><i class="fas fa-user"></i> De</th>
                                        <th><i class="fas fa-user"></i> Para</th>
                                        <th><i class="fas fa-comment"></i> Mensaje</th>
                                        <th><i class="fas fa-paperclip"></i> Adjunto</th>
                                        <th><i class="fas fa-calendar-alt"></i> Fecha</th>
                                        <th><i class="fas fa-reply"></i> Responder</th>
                                        <th><i class="fas fa-cogs"></i> Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('modales.enviar_mensaje_modal')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Corregir el nombre del ID de la tabla de mensajes
            let table = $('#mensajes').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('messages.index') }}',
                columns: [
                    { data: 'de', name: 'de' },
                    { data: 'para', name: 'para' },
                    { data: 'mensaje', name: 'mensaje' },
                    { data: 'adjunto', name: 'adjunto', orderable: false, searchable: false },
                    { data: 'fecha', name: 'fecha' },
                    { data: 'mensajeria', name: 'mensajeria', orderable: false, searchable: false },
                    { data: 'acciones', name: 'acciones', orderable: false, searchable: false },
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });
            setInterval(function() {
                table.ajax.reload(null, false);
            }, 40000);
        });

        // Al hacer clic en el botón "Responder", llenar los campos del modal
        $(document).on('click', '.btn-message', function() {
            var userId = $(this).data('id');
            var userName = $(this).data('nombre');

            // Actualizar el título del modal con el nombre del destinatario
            $('#sendMessageModalLabel').text('Enviar mensaje a ' + userName);

            // Establecer el ID del receptor en el campo oculto del formulario
            $('#receiver_id').val(userId);
        });

        // Marcar los mensajes como leídos cuando se está en la página de buzón
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.pathname === "/mensajes/buzon") {
                fetch("{{ route('notificaciones.mensajes.leidas') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Accept": "application/json"
                    }
                }).then(response => {
                    if (response.ok) {
                        console.log("✅ Notificaciones de mensajes marcadas como leídas.");
                    } else {
                        console.error("❌ Error al marcar notificaciones.");
                    }
                });
            }
        });
    </script>
@stop
