@extends('layouts.app')
@push('styles')
<style>
.dash-head{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:14px;margin:26px 0 4px}
.dash-head h2{font-size:clamp(1.6rem,4vw,2.2rem);margin-top:6px}
.dash-head .sub{color:var(--muted);font-size:.85rem;margin-top:4px}
.dash-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.toggle{display:flex;align-items:center;gap:8px;color:var(--muted);font-size:.85rem;cursor:pointer;user-select:none}
.toggle input{accent-color:var(--gold);width:16px;height:16px}
.notice{background:var(--green-soft);color:var(--green);border:1px solid rgba(49,194,124,.35);border-radius:10px;padding:11px 14px;margin:14px 0;font-size:.9rem}
.kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin:18px 0}
.kpi{background:var(--surface);border:1px solid var(--line);border-radius:14px;padding:16px 18px}
.kpi .k-lbl{color:var(--muted);font-size:.76rem;text-transform:uppercase;letter-spacing:.08em;font-weight:600}
.kpi .k-num{font-family:var(--font-d);font-weight:700;font-size:2.1rem;line-height:1.1;margin-top:6px;font-variant-numeric:tabular-nums}
.kpi .k-num small{font-size:1rem;color:var(--muted);font-weight:600}
.kpi .k-sub{color:var(--muted);font-size:.78rem;margin-top:2px}
.kpi.live .k-num{color:var(--green)}
.kpi.gold .k-num{color:var(--gold)}
.grid-2{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(0,1fr);gap:18px}
@media (max-width:900px){.grid-2{grid-template-columns:minmax(0,1fr)}}
.grid-2 .panel{margin:0}
.panel h3.ptitle{font-size:1.25rem;margin-bottom:4px}
.panel .psub{color:var(--muted);font-size:.82rem;margin:0 0 16px}
.mrow{padding:12px 0;border-top:1px solid var(--line)}
.mrow:first-of-type{border-top:0}
.mrow-top{display:flex;justify-content:space-between;gap:10px;align-items:baseline;flex-wrap:wrap}
.mrow-top b{font-family:var(--font-d);font-size:1.05rem}
.mrow-top span{color:var(--muted);font-size:.82rem;font-variant-numeric:tabular-nums}
.track{position:relative;height:10px;background:var(--surface-2);border:1px solid var(--line);border-radius:6px;overflow:hidden;margin:8px 0 6px}
.track i{position:absolute;inset:0 auto 0 0;display:block}
.track .tried{background:rgba(232,184,75,.28)}
.track .solved{background:var(--green)}
.mrow-foot{display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;color:var(--muted);font-size:.78rem}
.mrow-foot .fb{color:var(--gold)}
.legend{display:flex;gap:16px;color:var(--muted);font-size:.78rem;margin-bottom:6px;flex-wrap:wrap}
.legend i{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:6px;vertical-align:-1px}
.dist{display:flex;align-items:flex-end;gap:10px;height:150px;padding-top:18px;border-bottom:1px solid var(--line)}
.dist .col{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;gap:4px}
.dist .col b{font-family:var(--font-d);font-size:.95rem;font-variant-numeric:tabular-nums}
.dist .col i{display:block;width:100%;max-width:44px;background:var(--gold);border-radius:4px 4px 0 0;min-height:2px}
.dist .col.full i{background:var(--green)}
.dist-lbl{display:flex;gap:10px;margin-top:6px}
.dist-lbl span{flex:1;text-align:center;color:var(--muted);font-size:.78rem}
.podium{list-style:none;margin:18px 0 0;padding:0}
.podium li{display:flex;align-items:center;gap:12px;padding:9px 0;border-top:1px solid var(--line)}
.podium .pos{font-family:var(--font-d);font-weight:700;font-size:1.3rem;width:28px;color:var(--muted)}
.podium li:first-child .pos{color:var(--gold)}
.podium .nm{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.podium .pp{font-family:var(--font-d);font-weight:700;color:var(--gold);font-variant-numeric:tabular-nums}
.toolbar{display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:14px}
.toolbar input{flex:1;min-width:220px;padding:10px 13px;border-radius:9px;border:1px solid var(--line);background:var(--surface-2);color:var(--ink);font-family:var(--font-b);font-size:.92rem}
.chips{display:flex;gap:6px;flex-wrap:wrap}
.chip{border:1px solid var(--line);background:transparent;color:var(--muted);border-radius:999px;padding:6px 12px;font-size:.82rem;cursor:pointer;font-family:var(--font-b)}
.chip.on{background:var(--gold);color:#1a1205;border-color:var(--gold);font-weight:600}
table.board{min-width:900px}
table.board th[data-sort]{cursor:pointer;white-space:nowrap}
table.board th[data-sort]:hover{color:var(--ink)}
table.board th .arr{opacity:.35;margin-left:4px}
table.board th.asc .arr,table.board th.desc .arr{opacity:1;color:var(--gold)}
table.board td{vertical-align:middle}
table.board tr.srow{cursor:pointer}
table.board tr.srow:hover td{background:rgba(255,255,255,.02)}
table.board .num{font-variant-numeric:tabular-nums;text-align:right}
table.board th.num{text-align:right}
.rank{font-family:var(--font-d);font-weight:700;font-size:1.1rem;color:var(--muted)}
.st-name{font-weight:600}
.st-meta{color:var(--muted);font-size:.78rem}
.mdots{display:flex;gap:4px}
.mdots span{width:22px;height:22px;border-radius:6px;display:grid;place-items:center;font-family:var(--font-d);font-weight:700;font-size:.8rem;border:1px solid var(--line);color:var(--muted)}
.mdots span.tried{border-color:rgba(232,184,75,.55);color:var(--gold)}
.mdots span.on{background:var(--green);border-color:var(--green);color:#06231a}
.pill{display:inline-block;font-size:.74rem;font-weight:600;padding:3px 9px;border-radius:999px;white-space:nowrap}
.pill.activo{background:var(--green-soft);color:var(--green)}
.pill.inactivo{background:var(--surface-2);color:var(--muted);border:1px solid var(--line)}
.pill.finalizo{background:rgba(232,184,75,.14);color:var(--gold)}
.pill.reentry{background:var(--red-soft);color:var(--red);margin-left:6px}
tr.detail td{background:var(--surface-2);padding:16px 18px}
.dgrid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px}
.dcell{border:1px solid var(--line);border-radius:10px;padding:10px 12px;background:var(--surface)}
.dcell b{font-family:var(--font-d);display:block}
.dcell .ok{color:var(--green);font-size:.82rem}
.dcell .no{color:var(--muted);font-size:.82rem}
.dactions{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;align-items:center}
.btn.danger{background:transparent;color:var(--red);border-color:rgba(226,75,59,.5)}
.empty{color:var(--muted);text-align:center;padding:30px}
</style>
@endpush
@section('content')
@php
  $mCount = $missions->count();
  $reg = max($kpis['registered'], 1);
  $distMax = max($distribution->max(), 1);
  $fmt = fn ($n) => number_format($n, 0, ',', '.');
@endphp
<div class="wrap wide">
  <div class="dash-head">
    <div>
      <span class="eyebrow">Dirección de carrera · Operación Rascasse</span>
      <h2>Centro de control del profesor</h2>
      <div class="sub">Datos a las {{ now()->format('H:i') }} · «Activo» = envió una flag en los últimos 15 minutos</div>
    </div>
    <div class="dash-actions">
      <label class="toggle"><input type="checkbox" id="autorefresh"> Actualizar cada 30 s</label>
      <a class="btn small" href="{{ route('profesor.exportar') }}">Exportar CSV</a>
      <form method="POST" action="{{ route('profesor.salir') }}">@csrf<button class="btn ghost small">Cerrar sesión</button></form>
    </div>
  </div>

  @if(session('notice'))<div class="notice">{{ session('notice') }}</div>@endif

  {{-- Indicadores generales --}}
  <div class="kpis">
    <div class="kpi"><div class="k-lbl">Investigadores</div><div class="k-num">{{ $kpis['registered'] }}</div><div class="k-sub">registrados en la operación</div></div>
    <div class="kpi live"><div class="k-lbl">En pista ahora</div><div class="k-num">{{ $kpis['active'] }}</div><div class="k-sub">con actividad reciente</div></div>
    <div class="kpi gold"><div class="k-lbl">Caso cerrado</div><div class="k-num">{{ $kpis['complete'] }}<small> / {{ $kpis['registered'] }}</small></div><div class="k-sub">resolvieron las {{ $mCount }} misiones</div></div>
    <div class="kpi"><div class="k-lbl">Puntaje promedio</div><div class="k-num">{{ $fmt($kpis['avgPoints']) }}</div><div class="k-sub">de {{ $fmt($kpis['maxPoints']) }} pts posibles</div></div>
    <div class="kpi"><div class="k-lbl">Flags enviadas</div><div class="k-num">{{ $fmt($kpis['attempts']) }}</div><div class="k-sub">{{ $kpis['accuracy'] }}% fueron correctas</div></div>
    <div class="kpi"><div class="k-lbl">Finalizaron</div><div class="k-num">{{ $kpis['finished'] }}</div><div class="k-sub">pulsaron «Finalizar operación»</div></div>
  </div>

  <div class="grid-2">
    {{-- Avance por misión --}}
    <div class="panel">
      <h3 class="ptitle">Avance por misión</h3>
      <p class="psub">Dónde se está atascando el grupo. La barra muestra quién la intentó y quién la resolvió.</p>
      <div class="legend"><span><i style="background:var(--green)"></i>Resolvieron</span><span><i style="background:rgba(232,184,75,.28)"></i>Intentaron sin resolver</span></div>
      @foreach($missionStats as $ms)
        <div class="mrow">
          <div class="mrow-top">
            <b>M{{ $ms['mission']->orden }} · {{ $ms['mission']->titulo }}</b>
            <span>{{ $ms['solved'] }} / {{ $kpis['registered'] }} resolvieron</span>
          </div>
          <div class="track">
            <i class="tried" style="width:{{ $ms['tried'] / $reg * 100 }}%"></i>
            <i class="solved" style="width:{{ $ms['solved'] / $reg * 100 }}%"></i>
          </div>
          <div class="mrow-foot">
            <span>{{ $ms['tried'] }} lo intentaron · {{ $fmt($ms['attempts']) }} envíos · {{ $ms['attempts'] ? round($ms['solved'] / $ms['attempts'] * 100) : 0 }}% de acierto</span>
            @if($ms['firstBlood'])
              <span class="fb">Primero: {{ $ms['firstBlood']['nombre'] }} ({{ $ms['firstBlood']['at']->format('H:i') }})</span>
            @else
              <span>Nadie la ha resuelto aún</span>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    {{-- Distribución y podio --}}
    <div class="panel">
      <h3 class="ptitle">Distribución del grupo</h3>
      <p class="psub">Cuántos estudiantes llevan cada número de misiones resueltas.</p>
      <div class="dist">
        @foreach($distribution as $n => $count)
          <div class="col {{ $n === $mCount ? 'full' : '' }}" title="{{ $count }} estudiante(s) con {{ $n }} misión(es)">
            <b>{{ $count }}</b><i style="height:{{ $count / $distMax * 100 }}%"></i>
          </div>
        @endforeach
      </div>
      <div class="dist-lbl">@foreach($distribution as $n => $count)<span>{{ $n }}</span>@endforeach</div>

      <h3 class="ptitle" style="margin-top:24px">Podio</h3>
      @if($rows->isEmpty())
        <p class="psub">Aún no hay estudiantes.</p>
      @else
        <ol class="podium">
          @foreach($rows->take(3) as $row)
            <li><span class="pos">{{ $row['rank'] }}</span><span class="nm">{{ $row['student']->nombre }}</span><span class="pp">{{ $fmt($row['points']) }}</span></li>
          @endforeach
        </ol>
      @endif
    </div>
  </div>

  {{-- Clasificación completa --}}
  <div class="panel">
    <h3 class="ptitle">Clasificación de investigadores</h3>
    <p class="psub">Ordenada por puntos y, en empate, por quién resolvió antes. Haz clic en una fila para ver el detalle y las acciones.</p>
    <div class="toolbar">
      <input id="q" type="search" placeholder="Buscar por nombre, ID o correo…" autocomplete="off">
      <div class="chips" id="chips">
        <button type="button" class="chip on" data-f="all">Todos</button>
        <button type="button" class="chip" data-f="activo">Activos</button>
        <button type="button" class="chip" data-f="inactivo">Inactivos</button>
        <button type="button" class="chip" data-f="finalizo">Finalizaron</button>
      </div>
    </div>
    <div class="table-scroll">
      <table class="board" id="board">
        <thead><tr>
          <th data-sort="rank" data-type="num" class="asc">#<span class="arr">↕</span></th>
          <th data-sort="name">Estudiante<span class="arr">↕</span></th>
          <th>Misiones</th>
          <th data-sort="points" data-type="num" class="num">Puntos<span class="arr">↕</span></th>
          <th data-sort="attempts" data-type="num" class="num">Intentos<span class="arr">↕</span></th>
          <th data-sort="accuracy" data-type="num" class="num">Precisión<span class="arr">↕</span></th>
          <th data-sort="elapsed" data-type="num" class="num">Tiempo<span class="arr">↕</span></th>
          <th data-sort="last" data-type="num">Última actividad<span class="arr">↕</span></th>
          <th>Estado</th>
        </tr></thead>
        @forelse($rows as $row)
          @php $s = $row['student']; @endphp
          <tbody class="sgroup"
            data-rank="{{ $row['rank'] }}" data-name="{{ mb_strtolower($s->nombre) }}"
            data-points="{{ $row['points'] }}" data-attempts="{{ $row['attempts'] }}" data-accuracy="{{ $row['accuracy'] }}"
            data-elapsed="{{ $row['elapsedMinutes'] ?? 999999 }}" data-last="{{ optional($row['lastActivity'])->timestamp ?? 0 }}"
            data-status="{{ $row['status'] }}" data-search="{{ mb_strtolower($s->nombre.' '.$s->uid.' '.$s->correo) }}">
            <tr class="srow">
              <td class="rank">{{ $row['rank'] }}</td>
              <td><div class="st-name">{{ $s->nombre }}</div><div class="st-meta">{{ $s->uid }} · {{ $s->correo }}</div></td>
              <td><div class="mdots">
                @foreach($missions as $m)
                  @php $ok = isset($row['solved'][$m->id]); $n = $row['perMission'][$m->id]; @endphp
                  <span class="{{ $ok ? 'on' : ($n ? 'tried' : '') }}"
                    title="M{{ $m->orden }} · {{ $ok ? 'resuelta a las '.$row['solved'][$m->id]->format('H:i') : ($n ? 'sin resolver' : 'sin intentos') }} · {{ $n }} intento(s)">{{ $m->orden }}</span>
                @endforeach
              </div></td>
              <td class="num tag">{{ $fmt($row['points']) }}</td>
              <td class="num">{{ $row['attempts'] }}</td>
              <td class="num">{{ $row['attempts'] ? $row['accuracy'].'%' : '—' }}</td>
              <td class="num">
                @if(is_null($row['elapsedMinutes'])) —
                @else {{ intdiv($row['elapsedMinutes'], 60) ? intdiv($row['elapsedMinutes'], 60).' h ' : '' }}{{ $row['elapsedMinutes'] % 60 }} min
                @endif
              </td>
              <td>{{ $row['lastActivity'] ? $row['lastActivity']->locale('es')->diffForHumans() : '—' }}</td>
              <td>
                <span class="pill {{ $row['status'] }}">{{ ['activo' => 'Activo', 'inactivo' => 'Inactivo', 'finalizo' => 'Finalizó'][$row['status']] }}</span>
                @if($s->allow_reentry)<span class="pill reentry" title="Puede volver a registrarse con su ID y correo">Reingreso</span>@endif
              </td>
            </tr>
            <tr class="detail" hidden>
              <td colspan="9">
                <div class="dgrid">
                  @foreach($missions as $m)
                    @php $ok = isset($row['solved'][$m->id]); @endphp
                    <div class="dcell">
                      <b>M{{ $m->orden }} · {{ $m->titulo }}</b>
                      @if($ok)<span class="ok">✔ Resuelta {{ $row['solved'][$m->id]->format('d/m H:i') }}</span>
                      @else<span class="no">Sin resolver</span>@endif
                      <div class="st-meta">{{ $row['perMission'][$m->id] }} intento(s)</div>
                    </div>
                  @endforeach
                </div>
                <div class="dactions">
                  <span class="st-meta">Inicio: {{ optional($s->started_at)->format('d/m/Y H:i') ?? '—' }}
                    @if($s->finished_at) · Finalizó: {{ $s->finished_at->format('d/m/Y H:i') }}@endif</span>
                  <span style="flex:1"></span>
                  @unless($s->allow_reentry)
                    <form method="POST" action="{{ route('profesor.reingreso', $s) }}">@csrf
                      <button class="btn ghost small" title="Para estudiantes que cerraron el navegador o cambiaron de equipo">Habilitar reingreso</button>
                    </form>
                  @endunless
                  <form method="POST" action="{{ route('profesor.eliminar', $s) }}"
                    onsubmit="return confirm('¿Eliminar a {{ $s->uid }} y todo su progreso? No se puede deshacer.')">
                    @csrf @method('DELETE')
                    <button class="btn danger small">Eliminar registro</button>
                  </form>
                </div>
              </td>
            </tr>
          </tbody>
        @empty
          <tbody><tr><td colspan="9" class="empty">Aún no hay estudiantes registrados.</td></tr></tbody>
        @endforelse
        <tbody id="nomatch" hidden><tr><td colspan="9" class="empty">Ningún estudiante coincide con el filtro.</td></tr></tbody>
      </table>
    </div>
  </div>
</div>

<script>
(function () {
  const board = document.getElementById('board');
  const groups = () => Array.from(board.querySelectorAll('tbody.sgroup'));
  const q = document.getElementById('q');
  const chips = document.getElementById('chips');
  const nomatch = document.getElementById('nomatch');
  const store = {
    get(k) { try { return localStorage.getItem('cq_' + k); } catch (e) { return null; } },
    set(k, v) { try { localStorage.setItem('cq_' + k, v); } catch (e) {} },
  };
  let filter = store.get('filter') || 'all';
  q.value = store.get('q') || '';

  function apply() {
    const term = q.value.trim().toLowerCase();
    let visible = 0;
    groups().forEach(g => {
      const show = (filter === 'all' || g.dataset.status === filter) && (!term || g.dataset.search.includes(term));
      g.hidden = !show;
      if (show) visible++;
    });
    nomatch.hidden = visible > 0 || groups().length === 0;
    chips.querySelectorAll('.chip').forEach(c => c.classList.toggle('on', c.dataset.f === filter));
    store.set('filter', filter);
    store.set('q', q.value);
  }
  q.addEventListener('input', apply);
  chips.addEventListener('click', e => {
    const c = e.target.closest('.chip');
    if (c) { filter = c.dataset.f; apply(); }
  });

  // Expandir detalle
  board.addEventListener('click', e => {
    const row = e.target.closest('tr.srow');
    if (row) row.nextElementSibling.hidden = !row.nextElementSibling.hidden;
  });

  // Ordenar columnas
  board.querySelectorAll('th[data-sort]').forEach(th => {
    th.addEventListener('click', () => {
      const key = th.dataset.sort, num = th.dataset.type === 'num';
      const asc = !th.classList.contains('asc');
      board.querySelectorAll('th').forEach(x => x.classList.remove('asc', 'desc'));
      th.classList.add(asc ? 'asc' : 'desc');
      groups().sort((a, b) => {
        const va = a.dataset[key], vb = b.dataset[key];
        const r = num ? Number(va) - Number(vb) : va.localeCompare(vb, 'es');
        return asc ? r : -r;
      }).forEach(g => board.insertBefore(g, nomatch));
    });
  });

  // Auto-actualización
  const auto = document.getElementById('autorefresh');
  auto.checked = store.get('auto') === '1';
  let timer = null;
  function schedule() {
    clearInterval(timer);
    if (auto.checked) timer = setInterval(() => location.reload(), 30000);
  }
  auto.addEventListener('change', () => { store.set('auto', auto.checked ? '1' : '0'); schedule(); });
  schedule();
  apply();
})();
</script>
@endsection
