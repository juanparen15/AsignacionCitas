@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Agendar Cita</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('citas.store') }}">
        @csrf

        <div class="form-group">
            <label for="tramite_id">Trámite</label>
            <select name="tramite_id" class="form-control" required>
                <option value="">Seleccione un trámite</option>
                @foreach($tramites as $tramite)
                    <option value="{{ $tramite->id }}">{{ $tramite->nombre }} ({{ $tramite->area->secretaria->nombre }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="tipo_documento">Tipo de Documento</label>
            <select name="tipo_documento" class="form-control" required>
                @foreach(\App\Models\Cita::TIPOS_DOCUMENTO as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="numero_documento">Número de Documento</label>
            <input type="text" name="numero_documento" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="nombres">Nombres</label>
            <input type="text" name="nombres" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="apellidos">Apellidos</label>
            <input type="text" name="apellidos" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" class="form-control">
        </div>

        <div class="form-group">
            <label for="fecha_cita">Fecha</label>
            <input type="date" name="fecha_cita" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="hora_cita">Hora</label>
            <input type="time" name="hora_cita" class="form-control" required>
        </div>

        <div class="form-check my-2">
            <input type="checkbox" name="acepta_tratamiento_datos" class="form-check-input" required>
            <label class="form-check-label">Acepto el tratamiento de datos personales</label>
        </div>

        <button type="submit" class="btn btn-primary">Agendar Cita</button>
    </form>
</div>
@endsection
