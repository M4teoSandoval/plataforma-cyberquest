@extends('layouts.app')
@section('content')
<div class="wrap">
  <div class="panel" style="max-width:560px;margin:44px auto">
    <span class="eyebrow">Control de acceso · Paddock</span>
    <h2 class="mt">Firma tu contrato de investigador</h2>
    <p class="lead">Antes de darte acceso al servidor, el departamento legal de UNAB Racing necesita identificarte. Toda evidencia que encuentres quedará a tu nombre en el expediente del caso.</p>
    <p class="hint">Cada ID se registra una sola vez. No cierres el navegador durante la operación: si pierdes la sesión, el profesor tendrá que habilitar tu reingreso.</p>
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
      <button class="btn" style="width:100%">Firmar y entrar al laboratorio</button>
    </form>
  </div>
</div>
@endsection