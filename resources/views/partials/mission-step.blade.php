@php
  $whereMap = [
    'kali' => ['kali', 'Terminal de Kali'],
    'ssh'  => ['ssh',  'Sesión SSH (víctima)'],
    'web'  => ['web',  'Navegador / Web'],
    'local'=> ['local','Equipo local'],
  ];
  [$whereClass, $whereLabel] = $whereMap[$step['where']] ?? ['local', 'Terminal'];
  $n = $i + 1;
@endphp
<div class="cmd">
  <div class="cmd-head">
    <span class="t"><span style="color:var(--green)">{{ sprintf('%02d', $n) }}</span> · {{ $step['title'] }}</span>
    <span class="where {{ $whereClass }}">{{ $whereLabel }}</span>
  </div>
  <div class="cmd-body">
    <pre>{{ $step['cmd'] }}</pre>
    <button type="button" class="copy-btn" data-label="Copiar" data-cmd="{{ $step['cmd'] }}"><span class="lbl">Copiar</span></button>
  </div>
</div>
<div class="cmd-exp">
  <div class="row"><p class="q">¿Qué hace?</p><p class="a">{{ $step['what'] }}</p></div>
  <div class="row"><p class="q">¿Para qué lo necesitamos?</p><p class="a">{{ $step['why'] }}</p></div>
  @if(!empty($step['parts']))
  <div class="row"><p class="q">¿Qué significa cada parte?</p>
    <ul>
      @foreach($step['parts'] as $p)
        <li><code>{{ $p[0] }}</code> — {{ $p[1] }}</li>
      @endforeach
    </ul>
  </div>
  @endif
  <div class="row"><p class="q">¿Dónde debo ejecutarlo?</p><p class="a">{{ $step['where_text'] }}</p></div>
  <div class="row"><p class="q">¿Qué debería pasar?</p><p class="a">{{ $step['expect'] }}</p></div>
  <div class="row"><p class="q">¿Qué hago después?</p><p class="a">{{ $step['next'] }}</p></div>
  @if(!empty($step['fail']))
  <div class="row"><p class="q">¿Qué hago si falla?</p>
    <ul>
      @foreach((array) $step['fail'] as $f)<li class="a m">{{ $f }}</li>@endforeach
    </ul>
  </div>
  @endif
  @if(!empty($step['meaning']))
  <div class="row"><p class="q">¿Qué significa el resultado?</p><p class="a">{{ $step['meaning'] }}</p></div>
  @endif
</div>
