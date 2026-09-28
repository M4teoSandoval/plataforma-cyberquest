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

  <div class="panel story">
    <span class="eyebrow">Briefing de la operación · Confidencial</span>
    <h2 class="mt">El robo del Proyecto Rascasse</h2>
    <p class="lead">Te damos la bienvenida al caso, {{ \Illuminate\Support\Str::before($s->nombre, ' ') }}. Esto es lo que la escudería sabe hasta ahora:</p>
    <div class="dossier">
      <div><h4>El activo robado</h4><p>El <strong>Proyecto Rascasse</strong>: el paquete aerodinámico de UNAB Racing para Mónaco, fruto de toda una temporada de simulaciones y túnel de viento.</p></div>
      <div><h4>El sospechoso</h4><p><strong>UIS GP</strong> estrenó en los entrenamientos un alerón delantero idéntico. Alguien de dentro tuvo que entregárselo.</p></div>
      <div><h4>La escena</h4><p>Un servidor del laboratorio con señales de intrusión, ya aislado de la red de la fábrica. Ahí están los rastros del responsable.</p></div>
      <div><h4>El reloj</h4><p>Faltan 72 horas para la salida. Las pruebas tienen que llegar a los comisarios antes de la carrera.</p></div>
    </div>
    <p class="lead mt">Cada misión es un paso de la investigación y deja la pista que necesitas para la siguiente, así que resuélvelas <strong>en orden</strong>. Cuando encuentres una flag <span class="tag">RASCASSE{...}</span>, súbela aquí como evidencia.</p>
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