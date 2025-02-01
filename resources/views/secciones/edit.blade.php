@extends('adminlte::page')
@section('title', 'Crear Secciones')
@section('content_header')
    <h1>Editar Seccion</h1>
@stop
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('secciones.update', $seccion->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nombre">Nombre de la sección:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $seccion->nombre }}" required>
                </div>
                <div class="form-group">
                    <label for="maestrias">Maestrías asociadas:</label>
                    <select class="form-control" id="maestrias" name="maestrias[]" multiple required>
                        @foreach ($maestrias as $maestria)
                            <option value="{{ $maestria->id }}" {{ in_array($maestria->id, $seccion->maestrias->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $maestria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
            
        </div>
    </div>
</div>