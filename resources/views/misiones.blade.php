@extends('layouts.app')
@section('content')
@php
  $hechas = $done->count();
  $total = $missions->count();
  $pts = \App\Models\Mission::whereIn('id', $done)->sum('puntos');
  $insignias = [1=>'🟢',2=>'⛑️',3=>'📻',4=>'🔐',5=>'🏁'];
@endphp
@push('styles')
<style>
  .walk{border:1px solid var(--line-2);border-radius:10px;margin:14px 0;background:var(--surface-2)}
  .walk>summary{list-style:none;cursor:pointer;padding:13px 16px;font-family:var(--font-d);font-size:1.05rem;
    color:var(--green);display:flex;align-items:center;justify-content:space-between;gap:10px}
  .walk>summary::-webkit-details-marker{display:none}
  .walk>summary .chev{transition:transform .2s;color:var(--muted)}
  .walk[open]>summary{border-bottom:1px solid var(--line)}
  .walk[open]>summary .chev{transform:rotate(90deg)}
  .walk .inner{padding:4px 16px 16px}
  .walk .intro{color:var(--muted);font-size:.92rem;margin:14px 0 6px;border-left:2px solid var(--line-2);padding-left:12px}
  .objetivo{background:var(--green-soft);border:1px solid rgba(40,229,140,.25);border-radius:9px;padding:11px 14px;margin:10px 0;font-size:.93rem}
  .objetivo b{color:var(--green)}
  .labtag{font-family:var(--font-m);font-size:.72rem;color:var(--muted)}
</style>
@endpush
<div class="wrap">
  <div class="telemetry">
    <div class="who">{{ $s->nombre }}<small>{{ $s->uid }} · {{ $s->correo }}</small></div>
    <div style="display:flex;gap:22px;align-items:center;flex-wrap:wrap">
      <div><div class="bar"><i style="width:{{ $total ? $hechas / $total * 100 : 0 }}%"></i></div>
        <div class="lbl" style="color:var(--muted);font-size:.78rem;margin-top:5px">{{ $hechas }} de {{ $total }} misiones · {{ $hechas }} {{ $hechas === 1 ? 'insignia' : 'insignias' }}</div></div>
      <div class="stat"><div class="num">{{ number_format($pts, 0, ',', '.') }}</div><div class="lbl">de {{ number_format($total*1000,0,',','.') }} pts</div></div>
    </div>
  </div>

  <div class="panel story">
    <span class="eyebrow">Briefing de la operación · Confidencial</span>
    <h2 class="mt">El robo del Proyecto Rascasse</h2>
    <p class="lead">Te damos la bienvenida al caso, {{ \Illuminate\Support\Str::before($s->nombre, ' ') }}. CYBERQUEST es tu panel de investigación: aquí tienes la historia, la guía de cada comando y el envío de evidencias. Los comandos se ejecutan en tu <strong>entorno de laboratorio</strong> (Kali + la máquina víctima); la plataforma te guía y valida las flags.</p>
    <div class="dossier">
      <div><h4>El activo robado</h4><p>El <strong>Proyecto Rascasse</strong>: el paquete aerodinámico de UNAB Racing para Mónaco.</p></div>
      <div><h4>El sospechoso</h4><p><strong>UIS GP</strong> estrenó un alerón idéntico. Alguien de dentro lo entregó.</p></div>
      <div><h4>La escena</h4><p>Un servidor del laboratorio, ya aislado, con los rastros del responsable.</p></div>
      <div><h4>El reloj</h4><p>Taller de ~2 horas. Resuelve las 5 misiones <strong>en orden</strong>.</p></div>
    </div>
    <div class="mt">
      <span class="kv">Red <b>192.168.56.0/24</b></span>
      <span class="kv">Víctima (ej.) <b>192.168.56.103</b></span>
      <span class="kv">SSH <b>:22</b></span>
      <span class="kv">Web Apache <b>:2027</b></span>
    </div>
    <p class="hint mt">Las direcciones son las documentadas y pueden variar según tu instalación local. Cada comando indica <b>dónde</b> ejecutarlo y qué hacer <b>si falla</b>.</p>
  </div>

  @foreach($missions as $m)
    @php
      $isDone = $done->contains($m->id);
      $g = $guide[$m->orden] ?? null;
      $ic = $insignias[$m->orden] ?? '🎖️';
    @endphp
    <div class="mission {{ $isDone ? 'done' : '' }}" id="m{{ $m->orden }}">
      <h3>{{ $ic }} Misión {{ $m->orden }}: {{ $m->titulo }}
        <span class="badge {{ $isDone ? 'ok' : 'pend' }}">{{ $isDone ? '✔ Completada' : '● Pendiente' }}</span>
      </h3>
      <p class="n">{{ $m->narrativa }}</p>
      <div class="objetivo"><b>Objetivo:</b> {{ $m->objetivo }}</div>

      @if($g)
      <details class="walk" {{ $isDone ? '' : '' }}>
        <summary><span>📋 Guía paso a paso — {{ count($g['steps']) }} comandos explicados</span><span class="chev">▸</span></summary>
        <div class="inner">
          @if(!empty($g['intro']))<p class="intro">{{ $g['intro'] }}</p>@endif
          @foreach($g['steps'] as $i => $step)
            @include('partials.mission-step', ['step' => $step, 'i' => $i])
          @endforeach

          @if(!empty($g['hints']))
          <div class="hints">
            <div class="lbl">¿Atascado? Pistas progresivas (ábrelas solo si las necesitas)</div>
            @foreach($g['hints'] as $hi => $hint)
              <details class="hint-item">
                <summary><span class="lv">Pista {{ $hi + 1 }}</span> {{ ['Orientación general','Herramienta o concepto','Paso técnico concreto'][$hi] ?? 'Pista' }}</summary>
                <div class="body">{{ $hint }}</div>
              </details>
            @endforeach
          </div>
          @endif
        </div>
      </details>
      @endif

      <div class="pts mt">Insignia: {{ $m->insignia }} · {{ $m->puntos }} pts</div>

      @if($isDone)
        <p class="feedback good">✓ Flag validada. Insignia «{{ $m->insignia }}» conseguida.</p>
      @else
        <form method="POST" action="{{ route('flag.submit', $m) }}" class="submit-row">
          @csrf
          <input name="flag" placeholder="RASCASSE{...}" autocomplete="off" spellcheck="false">
          <button class="btn small">Enviar flag</button>
        </form>
        @if(session('bad_' . $m->id))<p class="feedback bad">✕ Flag incorrecta. Revisa tu procedimiento y vuelve a intentarlo.</p>@endif
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
