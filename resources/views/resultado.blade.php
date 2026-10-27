@extends('layouts.app')
@section('content')
@php
  $hechas = $done->count();
  $total = $missions->count();
  $pts = \App\Models\Mission::whereIn('id', $done)->sum('puntos');
  $completo = $hechas === $total && $total > 0;
  $habilidades = ['Reconocimiento y escaneo de red', 'Esteganografía y verificación con hashes',
    'Análisis de tráfico (PCAP)', 'Criptografía simétrica y asimétrica', 'Escalada de privilegios en Linux'];
@endphp
@push('styles')
<style>
  .cert-cta{background:linear-gradient(180deg,var(--surface),var(--surface-2));border:1px solid var(--gold);
    border-radius:16px;padding:26px;margin:22px 0;text-align:center}
  .cert-cta h3{font-size:1.4rem;color:var(--gold)}
  .btn.cert{background:var(--gold);color:#1a1205;font-size:1.05rem;padding:15px 28px;margin-top:8px}
  .skills{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin:14px 0 0}
  .skills span{background:var(--green-soft);color:var(--green);border:1px solid rgba(49,194,124,.3);
    border-radius:999px;padding:6px 13px;font-size:.84rem}
  .badges{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px;margin:18px 0}
  .bdg{background:var(--surface-2);border:1px solid var(--line);border-radius:12px;padding:14px 10px;text-align:center}
  .bdg.on{border-color:var(--green)}
  .bdg .ic{font-size:1.6rem}
  .bdg.on .ic{filter:none}.bdg .ic{filter:grayscale(1) opacity(.4)}
  .bdg small{display:block;color:var(--muted);font-size:.74rem;margin-top:4px}
  .bdg.on small{color:var(--green)}
  @keyframes pop{0%{transform:scale(.8);opacity:0}60%{transform:scale(1.06)}100%{transform:scale(1);opacity:1}}
  .pop{animation:pop .5s ease both}
  .flash-err{background:var(--red-soft);color:var(--red);border:1px solid rgba(226,75,59,.35);
    border-radius:10px;padding:11px 14px;margin:14px 0;font-size:.9rem;text-align:center}
</style>
@endpush
@php
  $insignias = [1=>['🟢','Bandera Verde'],2=>['⛑️','Casco del Ingeniero'],3=>['📻','Radio del Box'],
    4=>['🔐','Caja Fuerte'],5=>['🏁','Bandera a Cuadros']];
@endphp
<div class="wrap">
  <div class="checker gold" style="margin:24px 0 0;border-radius:6px"></div>
  <div class="panel {{ $completo ? 'pop' : '' }}" style="max-width:720px;margin:0 auto 40px">
    <div class="result-hero">
      <span class="eyebrow">{{ $completo ? 'Operación RASCASSE completada' : 'Bandera a cuadros' }}</span>
      <div class="big">{{ number_format($pts, 0, ',', '.') }}</div>
      <div class="lead" style="text-align:center">de {{ number_format($total * 1000, 0, ',', '.') }} puntos</div>
    </div>

    <div class="dots">
      @foreach($missions as $m)
        <span class="{{ $done->contains($m->id) ? 'on' : '' }}">{{ $m->orden }}</span>
      @endforeach
    </div>

    @if(session('cert_error'))<p class="flash-err">{{ session('cert_error') }}</p>@endif

    <p class="lead center">
      @if($completo) ¡Caso cerrado! Reuniste toda la evidencia: cómo entró el responsable, qué se llevó y quién firmó el paquete. El expediente llega a los comisarios antes de la clasificación y UNAB Racing sale en Mónaco con la cabeza en alto.
      @elseif($hechas === 0) El expediente sigue vacío. Sin evidencia, UNAB Racing no puede denunciar a nadie. Vuelve al laboratorio y sigue el rastro.
      @else Completaste {{ $hechas }} de {{ $total }} misiones. El expediente tiene pistas sólidas, pero todavía no alcanza para demostrar el robo ante los comisarios. Puedes volver e intentar las que faltan.
      @endif
    </p>

    {{-- Insignias conseguidas --}}
    <div class="badges">
      @foreach($missions as $m)
        @php [$ic,$nm] = $insignias[$m->orden] ?? ['🎖️',$m->insignia]; $on = $done->contains($m->id); @endphp
        <div class="bdg {{ $on ? 'on' : '' }}">
          <div class="ic">{{ $ic }}</div>
          <small>{{ $nm }}</small>
        </div>
      @endforeach
    </div>

    @if($completo)
      {{-- Resumen de habilidades aprendidas --}}
      <div class="center" style="margin-top:22px">
        <span class="eyebrow">Habilidades que practicaste</span>
        <div class="skills">
          @foreach($habilidades as $h)<span>{{ $h }}</span>@endforeach
        </div>
      </div>

      {{-- Llamado a descargar el certificado --}}
      <div class="cert-cta">
        <span class="eyebrow">Reconocimiento oficial</span>
        <h3 class="mt">Tu certificado está listo</h3>
        <p class="lead center" style="margin:6px auto 0">Taller Agencia CiberGuardians: Código Rojo — Infiltrándose en la Mente del Atacante. Documento a nombre de <strong>{{ $s->nombre }}</strong>.</p>
        <a class="btn cert" href="{{ route('certificado') }}">⬇ DESCARGAR MI CERTIFICADO PDF</a>
        <p class="hint center mt">Puedes volver a descargarlo cuando quieras desde esta pantalla.</p>
      </div>
    @endif

    <div class="center mt"><a class="btn ghost" href="{{ route('misiones') }}">Volver a las misiones</a></div>
    <p class="hint center mt">Las respuestas correctas no se muestran. Tu progreso queda registrado para el profesor.</p>
  </div>
</div>
@endsection
