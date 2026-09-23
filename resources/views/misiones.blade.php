@extends('layouts.app')
@section('content')
@php
  $hechas = $done->count();
  $pts = \App\Models\Mission::whereIn('id', $done)->sum('puntos');
@endphp
<div class="wrap">
  <div class="telemetry">
    <div class="who">{{ $s->nombre }}<small>{{ $s->uid }} · {{ $s->correo }}</small></div>
    <div style="display:flex;gap:22px;align-items:center">
      <div><div class="bar"><i style="width:{{ $hechas / 5 * 100 }}%"></i></div>
        <div class="lbl" style="color:var(--muted);font-size:.78rem;margin-top:5px">{{ $hechas }} de 5 misiones completadas</div></div>
      <div class="stat"><div class="num">{{ number_format($pts, 0, ',', '.') }}</div><div class="lbl">de 5.000 pts</div></div>
    </div>
  </div>

  <div class="panel">
    <span class="eyebrow">Briefing de la operación</span>
    <h2 class="mt">El robo del Proyecto Rascasse</h2>
    <p class="lead">A tres días del Gran Premio de Mónaco, UNAB Racing detecta que su paquete aerodinámico confidencial —el <strong>Proyecto Rascasse</strong>— aparece en el coche de UIS GP. Investiga la máquina del laboratorio, sigue el rastro y descubre quién filtró los datos.</p>
    <p class="lead mt">Resuelve las misiones <strong>en orden</strong>. Cuando encuentres una flag <span class="tag">RASCASSE{...}</span>, súbela aquí.</p>
  </div>

  @foreach($missions as $m)
    @php $isDone = $done->contains($m->id); @endphp
    <div class="mission {{ $isDone ? 'done' : '' }}">
      <h3>Misión {{ $m->orden }}: {{ $m->titulo }}
        <span class="badge {{ $isDone ? 'ok' : 'pend' }}">{{ $isDone ? '✔ Completada' : '● Pendiente' }}</span>
      </h3>
      <p class="n">{{ $m->narrativa }}</p>
      <p><strong>Objetivo:</strong> {{ $m->objetivo }}</p>
      <p class="pista">Pista: {{ $m->pista }}</p>
      <div class="pts">Insignia: {{ $m->insignia }} · {{ $m->puntos }} pts</div>
      @if($isDone)
        <p class="feedback good">Flag validada. Insignia «{{ $m->insignia }}» conseguida.</p>
      @else
        <form method="POST" action="{{ route('flag.submit', $m) }}" class="submit-row">
          @csrf
          <input name="flag" placeholder="RASCASSE{...}" autocomplete="off">
          <button class="btn small">Enviar flag</button>
        </form>
        @if(session('bad_' . $m->id))<p class="feedback bad">Flag incorrecta. Revisa tu procedimiento y vuelve a intentarlo.</p>@endif
      @endif
    </div>
  @endforeach

  <div class="center mt" style="margin:26px 0 40px">
    <form method="POST" action="{{ route('finalizar') }}">@csrf
      <button class="btn">Finalizar operación y ver puntuación</button>
    </form>
  </div>
</div>
@endsection