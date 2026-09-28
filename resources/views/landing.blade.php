@extends('layouts.app')
@section('content')
<div class="wrap">
  <div class="panel story" style="margin-top:40px">
    <span class="eyebrow">Gran Premio de Mónaco 2025 · 72 horas para la salida</span>
    <h1 style="font-size:clamp(2.4rem,7vw,4.5rem);margin:14px 0 16px">Operación Rascasse</h1>

    <p class="lead">En la Fórmula 1 lo más valioso no es el motor ni el piloto: es la <strong>información</strong>. La telemetría, las simulaciones de aerodinámica y las horas de túnel de viento pueden valer décimas por vuelta y millones de dólares. Por eso los equipos protegen sus diseños como secretos de Estado. En 2007, un equipo recibió una multa de 100 millones de dólares y fue excluido del campeonato de constructores por tener documentos técnicos de su rival.</p>

    <p class="lead">A tres días del Gran Premio de Mónaco, <strong>UNAB Racing</strong> revisa las fotos de los entrenamientos de <strong>UIS GP</strong> y se queda helado: su nuevo alerón delantero es idéntico al <strong>Proyecto Rascasse</strong>, el paquete aerodinámico secreto en el que la escudería trabajó toda la temporada. Nadie fuera de la fábrica debería haberlo visto.</p>

    <p class="lead">El equipo técnico aisló un servidor del laboratorio que muestra señales de intrusión. Alguien sacó la información desde dentro… y dejó rastros. La escudería te <strong>contrató como analista forense</strong> de su equipo de ciberseguridad para seguir esas pistas.</p>

    <p class="quote">«No basta con saber qué se llevaron. Necesito saber quién, cómo, y poder demostrarlo antes de que se apague el semáforo del domingo.»<br><small style="color:var(--muted);font-style:normal">— Directora de equipo, UNAB Racing</small></p>

    <div class="dossier">
      <div><h4>Tu rol</h4><p>Analista forense de UNAB Racing. Tienes acceso a la máquina recuperada del laboratorio y a todo lo que el responsable dejó en ella.</p></div>
      <div><h4>Qué está en juego</h4><p>Con pruebas, la escudería puede denunciar a UIS GP ante los comisarios. Sin ellas, corre en Mónaco con su diseño en manos del rival.</p></div>
      <div><h4>Cómo funciona</h4><p>5 misiones encadenadas. Cada una termina en una flag <span class="tag">RASCASSE{...}</span> que prueba que encontraste la evidencia.</p></div>
      <div><h4>Reglas del paddock</h4><p>Ataca solo la máquina del laboratorio asignada. Las flags son personales: cada envío queda registrado con tu ID.</p></div>
    </div>

    <p class="pts mt">5 misiones · 5.000 puntos · una sola investigación</p>
    <div class="mt" style="display:flex;gap:14px;flex-wrap:wrap">
      <a class="btn" href="{{ route('registro') }}">Aceptar el contrato</a>
      <a class="btn ghost" href="{{ route('profesor.login') }}">Acceso profesor</a>
    </div>
  </div>
</div>
@endsection
