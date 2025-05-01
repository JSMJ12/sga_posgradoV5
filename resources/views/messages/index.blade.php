@extends('adminlte::page')

@section('title', 'Mensajería')

@section('content_header')
    <h1 class="text-center"><i class="fas fa-envelope"></i> Mensajería</h1>
@stop

@section('content')
    <div class="container">
        <div class="row">
            {{-- Columna izquierda: Contactos --}}
            <div class="col-md-5 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Contactos</h5>
                    </div>
                    <div class="card-body p-2">
                        <input type="text" class="form-control filter-input mb-3" placeholder="Buscar contacto..."
                            data-target="listaContactos">

                        <div id="listaContactos" style="max-height: 400px; overflow-y: auto;">
                            @foreach ($contactos as $index => $contacto)
                                <div class="card mb-2 list-group-item">
                                    <div class="card-header p-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $contacto['nombre'] }} {{ $contacto['apellido'] }}</strong>
                                            @foreach ($contacto['roles'] as $rol)
                                                <span class="badge badge-secondary ml-1">{{ $rol }}</span>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-message"
                                            data-id="{{ $contacto['id'] }}"
                                            data-nombre="{{ $contacto['nombre'] }} {{ $contacto['apellido'] }}"
                                            data-toggle="modal" data-target="#sendMessageModal" title="Enviar mensaje">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna derecha: Tabla de mensajes --}}
            <div class="col-md-7">
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
                columns: [{
                        data: 'de',
                        name: 'de'
                    },
                    {
                        data: 'para',
                        name: 'para'
                    },
                    {
                        data: 'mensaje',
                        name: 'mensaje'
                    },
                    {
                        data: 'adjunto',
                        name: 'adjunto',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'fecha',
                        name: 'fecha'
                    },
                    {
                        data: 'mensajeria',
                        name: 'mensajeria',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    },
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
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
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
    <script>
        document.querySelectorAll('.filter-input').forEach(input => {
            input.addEventListener('input', function() {
                const listId = this.dataset.target;
                const query = this.value.toLowerCase();
                const listItems = document.querySelectorAll(`#${listId} .list-group-item`);
                listItems.forEach(item => {
                    const name = item.querySelector('strong')?.textContent.toLowerCase() || '';
                    const roles = Array.from(item.querySelectorAll('.badge')).map(b => b.textContent
                        .toLowerCase()).join(' ');
                    const fullText = name + ' ' + roles;
                    item.style.display = fullText.includes(query) ? '' : 'none';
                });
            });
        });
    </script>

@stop

@section('css')
    <style>
        .accordion-modern .accordion-item {
            border-left: 5px solid #0d6efd;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1rem;
        }

        .accordion-modern .accordion-button {
            background-color: #fff;
            font-weight: 600;
            color: #0d6efd;
        }

        .accordion-modern .accordion-button::after {
            color: #0d6efd;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .accordion-body {
            background-color: #fdfdfd;
        }

        .filter-input {
            border-radius: 0.375rem;
        }
    </style>
@stop
