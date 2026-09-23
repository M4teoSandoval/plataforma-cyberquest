@extends('layouts.app')
@section('content')
<div class="wrap">
  <div class="panel" style="max-width:560px;margin:44px auto">
    <span class="eyebrow">Control de acceso · Paddock</span>
    <h2 class="mt">Registra tu pase</h2>
    <p class="lead">Necesitamos identificarte para guardar tu progreso y tu puntuación. No se crea contraseña.</p>
    <form method="POST" action="{{ route('registro.store') }}">
      @csrf
      <div class="field"><label>Nombres y apellidos</label>
        <input name="nombre" value="{{ old('nombre') }}" placeholder="Ej. Pablo Méndez"></div>
      <div class="field"><label>Correo institucional</label>
        <input name="correo" type="email" value="{{ old('correo') }}" placeholder="usuario@unab.edu.co">
        <p class="hint">Debe terminar en @unab.edu.co</p></div>
      <div class="field"><label>ID institucional</label>
        <input name="uid" value="{{ old('uid') }}" placeholder="U00XXXXXX">
        <p class="hint">Formato U00 seguido de números.</p></div>
      @foreach($errors->all() as $e)<p class="err">{{ $e }}</p>@endforeach
      <button class="btn" style="width:100%">Entrar a la operación</button>
    </form>
  </div>
</div>
@endsection