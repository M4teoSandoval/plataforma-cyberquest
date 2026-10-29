@extends('layouts.app')
@section('content')
<div class="wrap">
  @if($errors->any())
    <div class="panel" style="max-width:560px;margin:44px auto 0;border-color:rgba(255,94,73,.5)">
      @foreach($errors->all() as $e)<p class="err">{{ $e }}</p>@endforeach
    </div>
  @endif

  <div class="panel" style="max-width:560px;margin:44px auto">
    <span class="eyebrow">Control de acceso · Paddock</span>
    <h2 class="mt">Firma tu contrato de investigador</h2>
    <p class="lead">Antes de darte acceso al servidor, el departamento legal de UNAB Racing necesita identificarte. Toda evidencia que encuentres quedará a tu nombre en el expediente del caso.</p>
    <p class="hint">Cada ID se registra una sola vez. No cierres el navegador durante la operación: si pierdes la sesión, solicita reingreso aquí abajo.</p>
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
      <button class="btn" style="width:100%">Firmar y entrar al laboratorio</button>
    </form>
  </div>

  <div class="panel" style="max-width:560px;margin:0 auto 44px">
    <span class="eyebrow">¿Perdiste la sesión?</span>
    <h2 class="mt">Solicitar reingreso</h2>
    <p class="lead">Si ya estabas registrado y cerraste el navegador o cambiaste de equipo, pide volver a entrar con tu mismo ID y correo. El profesor debe aprobar la solicitud y entrarás automáticamente.</p>
    <form method="POST" action="{{ route('reingreso.solicitar') }}">
      @csrf
      <div class="field"><label>ID institucional</label>
        <input name="uid" value="{{ old('uid') }}" placeholder="U00XXXXXX"></div>
      <div class="field"><label>Correo institucional</label>
        <input name="correo" type="email" value="{{ old('correo') }}" placeholder="usuario@unab.edu.co"></div>
      <button class="btn ghost" style="width:100%">Solicitar reingreso</button>
    </form>
  </div>
</div>
@endsection
