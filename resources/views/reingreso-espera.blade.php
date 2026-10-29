@extends('layouts.app')
@section('content')
<div class="wrap">
  <div class="panel" style="max-width:560px;margin:44px auto;text-align:center">
    <span class="eyebrow">Control de acceso · Paddock</span>
    <h2 class="mt">Solicitud enviada</h2>
    <p class="lead">Tu solicitud de reingreso quedó registrada. El profesor debe aprobarla; en cuanto lo haga, entrarás automáticamente sin volver a escribir tus datos.</p>

    <p class="hint" style="margin-top:18px">
      Investigador: <b>{{ $student->nombre }}</b><br>
      ID: <span class="tag">{{ $student->uid }}</span> · {{ $student->correo }}
    </p>

    <p id="reentry-state" class="pts" style="margin-top:18px">Esperando la aprobación del profesor…</p>

    <p class="hint">
      <a href="{{ route('reingreso.espera') }}">Actualizar estado</a> ·
      <a href="{{ route('landing') }}">Volver al inicio</a>
    </p>
  </div>
</div>
@endsection
@push('scripts')
<script>
(function () {
  var label = document.getElementById('reentry-state');
  var statusUrl = @json(route('reingreso.estado'));

  function check() {
    fetch(statusUrl, { headers: { 'Accept': 'application/json' }, cache: 'no-store' })
      .then(function (res) { return res.ok ? res.json() : null; })
      .then(function (data) {
        if (!data) { setTimeout(check, 3000); return; }

        if (data.status === 'approved' && data.redirect) {
          label.textContent = '¡Aprobado! Entrando al laboratorio…';
          window.location.href = data.redirect;
          return;
        }

        if (data.status === 'rejected' || data.status === 'expired') {
          label.textContent = data.status === 'rejected'
            ? 'La solicitud no fue aprobada. Redirigiendo…'
            : 'La solicitud expiró. Redirigiendo…';
          if (data.redirect) { setTimeout(function () { window.location.href = data.redirect; }, 2000); }
          return;
        }

        setTimeout(check, 3000);
      })
      .catch(function () { setTimeout(check, 5000); });
  }

  setTimeout(check, 1500);
})();
</script>
@endpush
