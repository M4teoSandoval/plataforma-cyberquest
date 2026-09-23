@extends('layouts.app')
@section('content')
<div class="wrap">
  <div class="mt" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin:26px 0 6px">
    <div><span class="eyebrow">Dirección de carrera</span><h2 class="mt">Resultados por estudiante</h2></div>
    <form method="POST" action="{{ route('profesor.salir') }}">@csrf<button class="btn ghost small">Cerrar sesión</button></form>
  </div>
  <div class="panel">
    <div class="table-scroll">
      <table>
        <thead><tr><th>Estudiante</th><th>ID</th><th>Correo</th><th>Misiones</th><th>Puntos</th><th>Intentos</th><th>Inicio</th></tr></thead>
        <tbody>
        @forelse($students as $s)
          @php $done = $s->completedMissionIds(); @endphp
          <tr>
            <td>{{ $s->nombre }} @if($s->finished)<span class="tag" style="font-size:.72rem">FINALIZÓ</span>@endif</td>
            <td>{{ $s->uid }}</td><td>{{ $s->correo }}</td>
            <td>@foreach($missions as $m)<span class="mdot {{ $done->contains($m->id) ? 'on' : '' }}"></span>@endforeach</td>
            <td class="tag">{{ number_format($s->points(), 0, ',', '.') }}</td>
            <td>{{ $s->attemptsCount() }}</td>
            <td>{{ optional($s->started_at)->format('d/m/Y H:i') }}</td>
          </tr>
        @empty
          <tr><td colspan="7" class="center" style="color:var(--muted);padding:26px">Aún no hay estudiantes registrados.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection