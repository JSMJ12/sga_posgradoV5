@extends('adminlte::page')

@section('title', 'Detalles del Postulante')

@section('content_header')
    
@stop

@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header bg-success text-white text-center">
                <h1 class="m-0">{{ $postulante->apellidop }} {{ $postulante->apellidom }} {{ $postulante->nombre1 }} {{ $postulante->nombre2 }}</h1>
            </div>
            <div class="card-body text-center">
                <div class="mb-4 text-center">
                    <img src="{{ asset('storage/' . $postulante->imagen) }}" alt="Imagen de {{ $postulante->nombre1 }}" style="width: 200px; height: 200px; border-radius: 5px;">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Maestría</th>
                                <td>{{ $postulante->maestria->nombre }}</td>
                            </tr>
                            <tr>
                                <th>Cédula</th>
                                <td>{{ $postulante->dni }}</td>
                            </tr>
                            <tr>
                                <th>Correo Electrónico</th>
                                <td>{{ $postulante->correo_electronico }}</td>
                            </tr>
                            <tr>
                                <th>Celular</th>
                                <td>{{ $postulante->celular }}</td>
                            </tr>
                            <tr>
                                <th>Título Profesional</th>
                                <td>{{ $postulante->titulo_profesional }}</td>
                            </tr>
                            <tr>
                                <th>Universidad Título</th>
                                <td>{{ $postulante->universidad_titulo }}</td>
                            </tr>
                            <tr>
                                <th>Sexo</th>
                                <td>{{ $postulante->sexo }}</td>
                            </tr>
                            <tr>
                                <th>Fecha de Nacimiento</th>
                                <td>{{ $postulante->fecha_nacimiento }}</td>
                            </tr>
                            <tr>
                                <th>Nacionalidad</th>
                                <td>{{ $postulante->nacionalidad }}</td>
                            </tr>
                            <tr>
                                <th>Discapacidad</th>
                                <td>{{ $postulante->discapacidad }}</td>
                            </tr>
                            <tr>
                                <th>Porcentaje de Discapacidad</th>
                                <td>{{ $postulante->porcentaje_discapacidad }}</td>
                            </tr>
                            <tr>
                                <th>Código CONADIS</th>
                                <td>{{ $postulante->codigo_conadis }}</td>
                            </tr>
                            <tr>
                                <th>Provincia</th>
                                <td>{{ $postulante->provincia }}</td>
                            </tr>
                            <tr>
                                <th>Etnia</th>
                                <td>{{ $postulante->etnia }}</td>
                            </tr>
                            <tr>
                                <th>Nacionalidad Indígena</th>
                                <td>{{ $postulante->nacionalidad_indigena }}</td>
                            </tr>
                            <tr>
                                <th>Cantón</th>
                                <td>{{ $postulante->canton }}</td>
                            </tr>
                            <tr>
                                <th>Dirección</th>
                                <td>{{ $postulante->direccion }}</td>
                            </tr>
                            <tr>
                                <th>Tipo de Colegio</th>
                                <td>{{ $postulante->tipo_colegio }}</td>
                            </tr>
                            <tr>
                                <th>Cantidad de Miembros del Hogar</th>
                                <td>{{ $postulante->cantidad_miembros_hogar }}</td>
                            </tr>
                            <tr>
                                <th>Ingreso Total del Hogar</th>
                                <td>{{ $postulante->ingreso_total_hogar }}</td>
                            </tr>
                            <tr>
                                <th>Nivel de Formación del Padre</th>
                                <td>{{ $postulante->nivel_formacion_padre }}</td>
                            </tr>
                            <tr>
                                <th>Nivel de Formación de la Madre</th>
                                <td>{{ $postulante->nivel_formacion_madre }}</td>
                            </tr>
                            <tr>
                                <th>Origen de Recursos para Estudios</th>
                                <td>{{ $postulante->origen_recursos_estudios }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


