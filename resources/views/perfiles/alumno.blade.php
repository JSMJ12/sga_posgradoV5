@extends('adminlte::page')
@section('title', 'Datos Personales')

@section('content_header')
    <h1>Actualizar Datos Personales</h1>
@stop

@section('content')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/postulacion.css') }}">


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg">
                    <div class="header text-center py-3 rounded-top">
                        <img src="{{ asset('images/logo_unesum_certificado.png') }}" alt="University Logo" class="logo">
                        <img src="{{ asset('images/posgrado-25.png') }}" alt="University Seal" class="seal">
                        <div>
                            <span class="university-name">UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ</span><br>
                            <span class="institute">INSTITUTO DE POSGRADO</span>
                        </div>
                    </div>
                    <div class="divider"></div>

                    <div class="card-body bg-light">
                        <form id="formEdicionAlumno" action="{{ route('update_datosAlumnos') }}" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('POST')
                            {{-- Nav Tabs --}}
                            <ul class="nav nav-tabs mb-3" id="postulacionTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="tab-datos" data-toggle="tab" href="#datos_personales"
                                        role="tab" aria-controls="datos_personales" aria-selected="true">Datos
                                        Personales</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-residencia" data-toggle="tab" href="#residencia"
                                        role="tab" aria-controls="residencia" aria-selected="false">Residencia</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-contacto" data-toggle="tab" href="#contacto" role="tab"
                                        aria-controls="contacto" aria-selected="false">Emergencia</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-academica" data-toggle="tab" href="#academica"
                                        role="tab" aria-controls="academica" aria-selected="false">Académica</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-laboral" data-toggle="tab" href="#laboral" role="tab"
                                        aria-controls="laboral" aria-selected="false">Laboral</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-socio" data-toggle="tab" href="#socioeconomico"
                                        role="tab" aria-controls="socioeconomico"
                                        aria-selected="false">Socioeconómico</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="postulacionTabsContent">
                                <div class="tab-pane fade show active" id="datos_personales" role="tabpanel"
                                    aria-labelledby="tab-datos">
                                    @include('alumnos.partials.datos_personales_form')
                                </div>
                                <div class="tab-pane fade" id="residencia" role="tabpanel" aria-labelledby="tab-residencia">
                                    @include('alumnos.partials.residencia_form')
                                </div>
                                <div class="tab-pane fade" id="contacto" role="tabpanel" aria-labelledby="tab-contacto">
                                    @include('alumnos.partials.contacto_emergencia_form')
                                </div>
                                <div class="tab-pane fade" id="academica" role="tabpanel" aria-labelledby="tab-academica">
                                    @include('alumnos.partials.academica_form')
                                </div>
                                <div class="tab-pane fade" id="laboral" role="tabpanel" aria-labelledby="tab-laboral">
                                    @include('alumnos.partials.laboral_form')
                                </div>
                                <div class="tab-pane fade" id="socioeconomico" role="tabpanel" aria-labelledby="tab-socio">
                                    @include('alumnos.partials.socioeconomico_form')
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


<script>
    $(function() {
        $('#alumnoTabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $('#formEdicionAlumno').on('submit', function(e) {
            e.preventDefault();

            const form = this;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: '¿Deseas actualizar los datos?',
                text: 'Esta acción actualizará tu información de alumno.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
