@extends('layouts.app')  {{-- Esto es opcional, si estás utilizando un layout --}}

@section('content')
<div class="container">
    <h1>Enviar Correo</h1>
    <form action="{{ route('enviar-correo') }}" method="POST">
        @csrf
        <input type="hidden" name="remitente" value="{{ auth()->user()->email }}">
        <div class="form-group">
            <label for="destinatario">Correo del Destinatario:</label>
            <input type="email" name="destinatario" id="destinatario" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="remitente">Tu Correo (Remitente):</label>
            <input type="email" name="remitente" id="remitente" class="form-control" value="{{ auth()->user()->email }}" required readonly>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Enviar</button>
            <a href="{{ route('cancelar-envio') }}" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>
@endsection
