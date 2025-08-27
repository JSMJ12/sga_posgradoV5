@extends('adminlte::page')

@section('title', 'Gestión de Alumnos')

@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Alumnos</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header text-white" style="background-color: #446f5f;">
                <h3 class="card-title">Listado de Alumnos</h3>
                <div class="card-tools">
                    <a href="{{ route('alumnos.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Agregar nuevo
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="alumnos">
                        <thead style="background-color: #315d50; color: white;">
                            <tr>
                                <th>Cédula / Pasaporte</th>
                                <th>Foto</th>
                                <th>Nombre Completo</th>
                                <th>Maestría</th>
                                <th>Email Institucional</th>
                                <th>Sexo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- El contenido se cargará dinámicamente mediante DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('modales.mostrar_matricula_modal')
    @include('modales.botones_alumnos_modal')
    @include('modales.matricular_alumno_modal')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let alumnosTable = $('#alumnos').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('alumnos.index') }}",
                columns: [{
                        data: 'dni',
                        name: 'dni'
                    },
                    {
                        data: 'foto',
                        name: 'foto',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nombre_completo',
                        name: 'nombre_completo'
                    },
                    {
                        data: 'maestria_nombre',
                        name: 'maestria_nombre'
                    },
                    {
                        data: 'email_institucional',
                        name: 'email_institucional'
                    },
                    {
                        data: 'sexo',
                        name: 'sexo'
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: true, // Habilitar el diseño responsivo

                columnDefs: [{
                        targets: [1, 6], // Aplica estilo especial a las columnas de foto y acciones
                        className: 'text-center'
                    },
                    {
                        targets: '_all',
                        className: 'align-middle' // Alinear verticalmente las columnas
                    }
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
            });

            setInterval(function() {
                alumnosTable.ajax.reload(null, false);
            }, 30000);
        });
    </script>
    <script>
        document.addEventListener("click", function(e) {
            // ============================
            //  Abrir modal de reportes
            // ============================
            if (e.target.closest(".open-reportes")) {
                let btn = e.target.closest(".open-reportes");
                let dni = btn.dataset.dni;
                let nombre = btn.dataset.nombre;
                let maestrias = JSON.parse(btn.dataset.maestrias);

                document.getElementById("modalAlumnoNombre").textContent = nombre;

                let tbody = document.getElementById("tablaReportes");
                tbody.innerHTML = "";

                maestrias.forEach(m => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${m.nombre}</td>
                            <td>
                                <a href="/certificado/${dni}/${m.id}" target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-file-pdf"></i> Certificado
                                </a>
                                <a href="/certificado-matricula/${dni}/${m.id}" target="_blank" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-file-pdf"></i> Matrícula
                                </a>
                                <a href="/record_academico/${dni}/${m.id}" target="_blank" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-file-alt"></i> Record
                                </a>
                                <a href="/certificado_culminacion/${dni}/${m.id}" target="_blank" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-pdf"></i> Culminación
                                </a>
                            </td>
                        </tr>
                    `;
                });

                let modal = new bootstrap.Modal(document.getElementById("modalReportes"));
                modal.show();
            }
        });

        // ============================
        //  Abrir modal de matrícula
        // ============================
        $(document).on('click', '.open-matricula-modal', function() {
            let dni = $(this).data('dni');
            let maestrias = $(this).data('maestrias');

            let $list = $('#modalMaestriasList');
            $list.empty();

            maestrias.forEach(function(m) {
                let btn = $(`
                    <a href="/matriculas/create/${dni}/${m.id}" class="btn btn-primary btn-lg btn-block mb-3 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-graduation-cap mr-2"></i> ${m.nombre}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                `);
                $list.append(btn);
            });

            $('#matriculaModal').modal('show');
        });

        // ============================
        //  Ver matrículas agrupadas
        // ============================
        $(document).on('click', '.view-matriculas', function() {
            let matriculas = $(this).data('matriculas');

            if (!matriculas || matriculas.length === 0) {
                $('#matriculasModalBody').html('<div class="alert alert-info">El estudiante no tiene matrículas registradas.</div>');
                $('#matriculasModal').modal('show');
                return;
            }

            // Agrupar primero por maestría y luego por cohorte
            let groupedByMaestria = {};
            matriculas.forEach(m => {
                let maestriaNombre = m.maestria || 'Sin Maestría';
                if (!groupedByMaestria[maestriaNombre]) groupedByMaestria[maestriaNombre] = {};

                let cohorteNombre = m.cohorte || 'Sin Cohorte';
                if (!groupedByMaestria[maestriaNombre][cohorteNombre]) {
                    groupedByMaestria[maestriaNombre][cohorteNombre] = [];
                }
                groupedByMaestria[maestriaNombre][cohorteNombre].push(m);
            });

            // Construir HTML
            let content = '';
            Object.keys(groupedByMaestria)
                .sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase())) // ordenar maestrías
                .forEach(maestria => {
                    content += `
                        <div class="mb-4 p-3 border rounded shadow-sm">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-university"></i> ${maestria}
                            </h5>
                    `;

                    Object.keys(groupedByMaestria[maestria])
                        .sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase())) // ordenar cohortes
                        .forEach(cohorte => {
                            content += `
                                <h6 class="text-secondary mb-2">
                                    <i class="fas fa-users"></i> ${cohorte}
                                </h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered table-striped mb-0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Asignatura</th>
                                                <th>Docente</th>
                                                <th>Aula</th>
                                                <th>Paralelo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${groupedByMaestria[maestria][cohorte]
                                                .sort((a, b) => a.asignatura.trim().toLowerCase().localeCompare(b.asignatura.trim().toLowerCase())) // ordenar asignaturas
                                                .map(m => `
                                                    <tr>
                                                        <td>${m.asignatura}</td>
                                                        <td>${m.docente}</td>
                                                        <td>${m.aula}</td>
                                                        <td>${m.paralelo}</td>
                                                    </tr>`).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            `;
                        });

                    content += `</div>`;
                });

            $('#matriculasModalBody').html(content);
            $('#matriculasModal').modal('show');
        });
    </script>


@stop
