@extends('layouts.app')
@section('content')
@php
  $hechas = $done->count();
  $pts = \App\Models\Mission::whereIn('id', $done)->sum('puntos');
@endphp
<div class="wrap">
  <div class="checker gold" style="margin:24px 0 0;border-radius:6px"></div>
  <div class="panel" style="max-width:640px;margin:0 auto 40px">
    <div class="result-hero">
      <span class="eyebrow">Bandera a cuadros</span>
      <div class="big">{{ number_format($pts, 0, ',', '.') }}</div>
      <div class="lead" style="text-align:center">de 5.000 puntos</div>
    </div>
    <div class="dots">
      @foreach($missions as $m)
        <span class="{{ $done->contains($m->id) ? 'on' : '' }}">{{ $m->orden }}</span>
      @endforeach
    </div>
    <p class="lead center">
      @if($hechas === 5) ¡Caso cerrado! Reuniste toda la evidencia: cómo entró el responsable, qué se llevó y quién firmó el paquete. El expediente llega a los comisarios antes de la clasificación y UNAB Racing sale en Mónaco con la cabeza en alto.
      @elseif($hechas === 0) El expediente sigue vacío. Sin evidencia, UNAB Racing no puede denunciar a nadie. Vuelve al laboratorio y sigue el rastro.
      @else Completaste {{ $hechas }} de 5 misiones. El expediente tiene pistas sólidas, pero todavía no alcanza para demostrar el robo ante los comisarios. Puedes volver e intentar las que faltan.
      @endif
    </p>
    <div class="center mt"><a class="btn ghost" href="{{ route('misiones') }}">Volver a las misiones</a></div>
    <p class="hint center mt">Las respuestas correctas no se muestran. Tu progreso queda registrado para el profesor.</p>
  </div>
</div>
@endsection