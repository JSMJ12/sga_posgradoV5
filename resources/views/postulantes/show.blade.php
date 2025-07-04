{{-- filepath: resources/views/postulantes/show.blade.php --}}
@extends('adminlte::page')

@section('title', 'Detalles del Postulante')

@section('content_header')
@stop

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="container">
        <div class="card mt-4 shadow" style="border: 1px solid #222831; background: #f8f9fa;">
            <div class="card-header text-white text-center" style="background: #1f2937;">
                <h2 class="m-0" style="font-family: 'Segoe UI', sans-serif; letter-spacing: 1px;">
                    <i class="fas fa-user-shield"></i>
                    {{ $postulante->apellidop }} {{ $postulante->apellidom }} {{ $postulante->nombre1 }}
                    {{ $postulante->nombre2 }}
                </h2>
            </div>
            <div class="card-body" style="background: #fff;">
                <div class="row mb-4">
                    <div class="col-md-4 text-center">
                        <img src="{{ $postulante->imagen ? asset('storage/' . $postulante->imagen) : asset('images/default-avatar.jpg') }}"
                            alt="Imagen de {{ $postulante->nombre1 }}"
                            style="width: 180px; height: 180px; border-radius: 10px; border: 3px solid #3d4a3d; background: #e5e5e5;">
                    </div>
                    <div class="col-md-8">
                        <div class="section-title mb-2" style="background: #22313f;">
                            <h5 class="mb-0" style="color:#fff;"><i class="fas fa-graduation-cap"></i> Maestría a Postular
                            </h5>
                        </div>
                        <div class="p-2" style="background: #f4f4f4; border-radius: 6px; color: #222;">
                            <strong>Maestría:</strong> {{ $postulante->maestria->nombre ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- DATOS PERSONALES --}}
                <div class="section-title mt-3" style="background: #3d4a3d;">
                    <h5 class="mb-0" style="color:#fff;"><i class="fas fa-user"></i> Datos Personales</h5>
                </div>
                <div class="row p-2" style="background: #f8f9fa; border-radius: 6px; color: #222;">
                    <div class="col-md-4"><strong><i class="fas fa-id-card"></i> Cédula/Pasaporte:</strong>
                        {{ $postulante->dni }}</div>
                    <div class="col-md-4"><strong><i class="fas fa-envelope"></i> Correo:</strong>
                        {{ $postulante->correo_electronico }}</div>
                    <div class="col-md-4"><strong><i class="fas fa-mobile-alt"></i> Celular:</strong>
                        {{ $postulante->celular }}</div>
                    <div class="col-md-4"><strong><i class="fas fa-phone"></i> Teléfono Convencional:</strong>
                        {{ $postulante->telefono_convencional }}</div>
                    <div class="col-md-4"><strong><i class="fas fa-calendar"></i> Fecha de Nacimiento:</strong>
                        {{ $postulante->fecha_nacimiento }}</div>
                    <div class="col-md-2"><strong><i class="fas fa-hourglass-half"></i> Edad:</strong>
                        {{ $postulante->edad }}</div>
                    <div class="col-md-2"><strong><i class="fas fa-venus-mars"></i> Sexo:</strong> {{ $postulante->sexo }}
                    </div>
                    <div class="col-md-2"><strong><i class="fas fa-tint"></i> Sangre:</strong>
                        {{ $postulante->tipo_sangre }}</div>
                    <div class="col-md-4"><strong>Nacionalidad:</strong> {{ $postulante->nacionalidad }}</div>
                    <div class="col-md-4"><strong>Libreta Militar:</strong> {{ $postulante->libreta_militar }}</div>
                    <div class="col-md-4"><strong>¿Discapacidad?:</strong> {{ $postulante->discapacidad }}</div>
                    <div class="col-md-4"><strong>Porcentaje Discapacidad:</strong>
                        {{ $postulante->porcentaje_discapacidad }}</div>
                    <div class="col-md-4"><strong>Código CONADIS:</strong> {{ $postulante->codigo_conadis }}</div>
                    <div class="col-md-4"><strong>Tipo Discapacidad:</strong> {{ $postulante->tipo_discapacidad }}</div>
                </div>

                {{-- RESIDENCIA --}}
                <div class="section-title mt-4" style="background: #222831;">
                    <h5 class="mb-0" style="color:#fff;"><i class="fas fa-home"></i> Residencia</h5>
                </div>
                <div class="row p-2" style="background: #f4f4f4; border-radius: 6px; color: #222;">
                    <div class="col-md-3"><strong>País:</strong> {{ $postulante->pais_residencia }}</div>
                    <div class="col-md-2"><strong>Años Residencia:</strong> {{ $postulante->anios_residencia }}</div>
                    <div class="col-md-3"><strong>Provincia:</strong> {{ $postulante->provincia }}</div>
                    <div class="col-md-2"><strong>Cantón:</strong> {{ $postulante->canton }}</div>
                    <div class="col-md-2"><strong>Parroquia:</strong> {{ $postulante->parroquia }}</div>
                    <div class="col-md-3"><strong>Calle Principal:</strong> {{ $postulante->calle_principal }}</div>
                    <div class="col-md-2"><strong>Número:</strong> {{ $postulante->numero_direccion }}</div>
                    <div class="col-md-3"><strong>Calle Secundaria:</strong> {{ $postulante->calle_secundaria }}</div>
                    <div class="col-md-4"><strong>Referencia:</strong> {{ $postulante->referencia_direccion }}</div>
                    <div class="col-md-3"><strong>Teléfono Domicilio:</strong> {{ $postulante->telefono_domicilio }}</div>
                    <div class="col-md-3"><strong>Celular Residencia:</strong> {{ $postulante->celular_residencia }}</div>
                </div>

                {{-- ACADÉMICA --}}
                <div class="section-title mt-4" style="background: #1f2937;">
                    <h5 class="mb-0" style="color:#fff;"><i class="fas fa-book-open"></i> Información Académica</h5>
                </div>
                <div class="row p-2" style="background: #f8f9fa; border-radius: 6px; color: #222;">
                    <div class="col-md-4"><strong>Especialidad Bachillerato:</strong>
                        {{ $postulante->especialidad_bachillerato }}</div>
                    <div class="col-md-4"><strong>Colegio Bachillerato:</strong> {{ $postulante->colegio_bachillerato }}
                    </div>
                    <div class="col-md-4"><strong>Ciudad Bachillerato:</strong> {{ $postulante->ciudad_bachillerato }}
                    </div>
                    <div class="col-md-4"><strong>Título Profesional:</strong> {{ $postulante->titulo_profesional }}</div>
                    <div class="col-md-4"><strong>Especialidad/Mención:</strong> {{ $postulante->especialidad_mencion }}
                    </div>
                    <div class="col-md-4"><strong>Universidad Tercer Nivel:</strong> {{ $postulante->universidad_titulo }}
                    </div>
                    <div class="col-md-4"><strong>Ciudad Universidad:</strong> {{ $postulante->ciudad_universidad }}</div>
                    <div class="col-md-4"><strong>País Universidad:</strong> {{ $postulante->pais_universidad }}</div>
                    <div class="col-md-4"><strong>Registro SENESCYT:</strong> {{ $postulante->registro_senescyt }}</div>
                    <div class="col-md-4"><strong>Título Posgrado:</strong> {{ $postulante->titulo_posgrado }}</div>
                    <div class="col-md-4"><strong>Denominación Posgrado:</strong> {{ $postulante->denominacion_posgrado }}
                    </div>
                    <div class="col-md-4"><strong>Universidad Posgrado:</strong> {{ $postulante->universidad_posgrado }}
                    </div>
                    <div class="col-md-4"><strong>Ciudad Posgrado:</strong> {{ $postulante->ciudad_posgrado }}</div>
                    <div class="col-md-4"><strong>País Posgrado:</strong> {{ $postulante->pais_posgrado }}</div>
                </div>

                {{-- LABORAL --}}
                <div class="section-title mt-4" style="background: #3d4a3d;">
                    <h5 class="mb-0" style="color:#fff;"><i class="fas fa-briefcase"></i> Información Laboral</h5>
                </div>
                <div class="row p-2" style="background: #f8f9fa; border-radius: 6px; color: #222;">
                    <div class="col-md-4"><strong>Lugar de Trabajo:</strong> {{ $postulante->lugar_trabajo }}</div>
                    <div class="col-md-4"><strong>Función Laboral:</strong> {{ $postulante->funcion_laboral }}</div>
                    <div class="col-md-4"><strong>Ciudad Trabajo:</strong> {{ $postulante->ciudad_trabajo }}</div>
                    <div class="col-md-6"><strong>Dirección Trabajo:</strong> {{ $postulante->direccion_trabajo }}</div>
                    <div class="col-md-3"><strong>Teléfono Trabajo:</strong> {{ $postulante->telefono_trabajo }}</div>
                </div>

                {{-- SOCIOECONÓMICOS --}}
                <div class="section-title mt-4" style="background: #222831;">
                    <h5 class="mb-0" style="color:#fff;"><i class="fas fa-money-bill-wave"></i> Datos Socioeconómicos
                    </h5>
                </div>
                <div class="row p-2" style="background: #f4f4f4; border-radius: 6px; color: #222;">
                    <div class="col-md-3"><strong>Etnia:</strong> {{ $postulante->etnia }}</div>
                    <div class="col-md-3"><strong>Nacionalidad Indígena:</strong> {{ $postulante->nacionalidad_indigena }}
                    </div>
                    <div class="col-md-3"><strong>Tipo de Colegio:</strong> {{ $postulante->tipo_colegio }}</div>
                    <div class="col-md-3"><strong>Miembros en el Hogar:</strong>
                        {{ $postulante->cantidad_miembros_hogar }}</div>
                    <div class="col-md-3"><strong>Ingreso total del Hogar:</strong> {{ $postulante->ingreso_total_hogar }}
                    </div>
                    <div class="col-md-3"><strong>Nivel Formación Padre:</strong> {{ $postulante->nivel_formacion_padre }}
                    </div>
                    <div class="col-md-3"><strong>Nivel Formación Madre:</strong> {{ $postulante->nivel_formacion_madre }}
                    </div>
                    <div class="col-md-3"><strong>Origen Recursos Estudios:</strong>
                        {{ $postulante->origen_recursos_estudios }}</div>
                </div>

                {{-- CONTACTO DE EMERGENCIA --}}

                <div class="section-title mt-4" style="background: #3d4a3d; padding: 10px 15px; border-radius: 8px;">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-phone-alt"></i> Contacto de Emergencia
                    </h5>
                </div>

                <div class="p-3 mt-2" style="background: #f8f9fa; border-radius: 8px; color: #222; font-size: 15px;">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Apellidos:</strong> {{ $postulante->contacto_apellidos }}
                        </div>
                        <div class="col-md-6">
                            <strong>Nombres:</strong> {{ $postulante->contacto_nombres }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Parentesco:</strong> {{ $postulante->contacto_parentesco }}
                        </div>
                        <div class="col-md-4">
                            <strong>Teléfono:</strong> {{ $postulante->contacto_telefono }}
                        </div>
                        <div class="col-md-4">
                            <strong>Celular:</strong> {{ $postulante->contacto_celular }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .section-title {
            padding: 8px 16px;
            border-radius: 6px 6px 0 0;
            margin-bottom: 0;
            background: #22313f;
        }

        h5 {
            border: none;
            padding-left: 0;
            background: none;
            border-radius: 0;
            margin-bottom: 0;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 24px 0 rgba(34, 40, 49, 0.10);
        }

        .card-body {
            border-radius: 0 0 12px 12px;
            background: #fff;
        }

        .row>div {
            margin-bottom: 8px;
        }

        .card-header {
            background: #1f2937 !important;
            color: #fff !important;
            border-radius: 12px 12px 0 0 !important;
            border-bottom: 2px solid #3d4a3d !important;
        }

        strong {
            color: #22313f;
        }
    </style>

@endsection

