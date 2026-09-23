@extends('layouts.app')
@section('content')
<div class="wrap">
  <div class="panel" style="margin-top:40px">
    <span class="eyebrow">Gran Premio de Mónaco 2025</span>
    <h1 style="font-size:clamp(2.4rem,7vw,4.5rem);margin:14px 0 6px">Operación Rascasse</h1>
    <p class="lead">UNAB Racing sospecha que su paquete aerodinámico fue filtrado a UIS GP. Te unes al equipo de seguridad para investigar el servidor recuperado.</p>
    <p class="pts mt">5 misiones · 5.000 puntos · una sola operación</p>
    <div class="mt" style="display:flex;gap:14px;flex-wrap:wrap">
      <a class="btn" href="{{ route('registro') }}">Empezar el reto</a>
      <a class="btn ghost" href="{{ route('profesor.login') }}">Acceso profesor</a>
    </div>
  </div>
</div>
@endsection