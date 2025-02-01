@extends('adminlte::page')
@section('title', 'Asignaturas-Docentes')
@section('content_header')
<h1>Asignación de asignaturas a docentes</h1>
@stop
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <table class="table" id='docentes'>
                        <thead>
                            <tr>
                                <th scope="col">Foto</th>
                                <th scope="col">Docente</th>
                                <th scope="col">Asignaturas asignadas</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($docentes as $docente)
                                <tr>
                                    <td class="text-center">
                                        <img src="{{ asset('storage/' . $docente->image) }}" alt="Imagen de {{ $docente->name }}" style="max-width: 60px; border-radius: 50%;">
                                    </td>
                                    <td>{{ $docente->nombre1 }} {{ $docente->nombre2 }} {{ $docente->apellidop }} {{ $docente->apellidom }}</td>
                                    <td>
                                        <ul>
                                            @foreach ($docente->asignaturas as $asignatura)
                                                <li>{{ $asignatura->nombre }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('js')
<script>
    $('#docentes').DataTable({
        lengthMenu: [5, 10, 15, 20, 40, 45, 50, 100], 
        pageLength: {{ $perPage }},
        responsive: true, 
        colReorder: true,
        keys: true,
        autoFill: true, 
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });
</script>
@stop