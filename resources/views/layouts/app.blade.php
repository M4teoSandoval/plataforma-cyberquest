<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Operación RASCASSE — CYBERQUEST</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<meta name="theme-color" content="#070C0D">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#070C0D;--surface:#0F1719;--surface-2:#0A1113;--panel-head:#0C1315;
  --ink:#E3F1EB;--muted:#7E978E;
  --line:#1B2A28;--line-2:#24413B;
  --green:#28E58C;        /* principal · verde terminal */
  --green-deep:#17B870;   /* completado */
  --green-soft:#0A241A;
  --accent:#36D4E6;       /* cian · datos/resaltado */
  --accent-soft:#082227;
  --amber:#E8B84B;        /* pendiente / advertencia */
  --amber-soft:#272010;
  --red:#FF5E49;--red-soft:#2A1311;
  --gold:var(--green);    /* compatibilidad: el acento principal ahora es verde */
  --font-d:"Saira Condensed",system-ui,sans-serif;
  --font-b:"Inter",system-ui,sans-serif;
  --font-m:"JetBrains Mono",ui-monospace,"Cascadia Code",Consolas,monospace;
  box-sizing:border-box;
}
*{box-sizing:inherit}
html{-webkit-text-size-adjust:100%}
body{margin:0;background:var(--bg);color:var(--ink);font-family:var(--font-b);line-height:1.55;
  background-image:
    radial-gradient(900px 500px at 85% -5%,rgba(40,229,140,.06),transparent 60%),
    radial-gradient(700px 400px at 0% 100%,rgba(54,212,230,.05),transparent 55%),
    linear-gradient(transparent 95%,rgba(40,229,140,.035) 95%),
    linear-gradient(90deg,transparent 95%,rgba(40,229,140,.035) 95%);
  background-size:auto,auto,100% 34px,34px 100%;}
h1,h2,h3{font-family:var(--font-d);font-weight:700;line-height:1.05;margin:0;letter-spacing:.01em}
a{color:var(--green)}
code,kbd,samp{font-family:var(--font-m)}
.wrap{max-width:1040px;margin:0 auto;padding:0 18px}
.wrap.wide{max-width:1320px}

/* Bandera a cuadros / cinta superior */
.checker{height:12px;background:conic-gradient(var(--line-2) 90deg,transparent 90deg 180deg,var(--line-2) 180deg 270deg,transparent 270deg) 0 0/12px 12px;opacity:.9}
.checker.gold{background:conic-gradient(var(--green) 90deg,transparent 90deg 180deg,var(--green) 180deg 270deg,transparent 270deg) 0 0/12px 12px;opacity:.5}

/* Barra superior */
.topbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 18px;border-bottom:1px solid var(--line);background:rgba(10,17,19,.85);backdrop-filter:blur(6px);position:sticky;top:0;z-index:20}
.brand{display:flex;align-items:center;gap:11px;font-family:var(--font-d);font-weight:700;font-size:1.05rem;letter-spacing:.02em}
.brand .dot{width:10px;height:10px;border-radius:50%;background:var(--green);box-shadow:0 0 0 4px rgba(40,229,140,.15),0 0 12px rgba(40,229,140,.6);animation:pulse 2.6s ease-in-out infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.45}}
.brand small{display:block;color:var(--muted);font-family:var(--font-m);font-weight:400;font-size:.68rem;letter-spacing:.02em}

/* Botones */
.btn{border:1px solid transparent;border-radius:9px;padding:12px 20px;font-size:1rem;font-weight:600;background:var(--green);color:#042012;cursor:pointer;font-family:var(--font-b);text-decoration:none;display:inline-block;transition:transform .08s ease,box-shadow .15s ease}
.btn:hover{box-shadow:0 0 0 1px var(--green),0 6px 20px -8px rgba(40,229,140,.7)}
.btn:active{transform:translateY(1px)}
.btn.ghost{background:transparent;color:var(--ink);border-color:var(--line-2)}
.btn.ghost:hover{box-shadow:none;border-color:var(--green);color:var(--green)}
.btn.small{padding:8px 14px;font-size:.88rem}

.eyebrow{color:var(--green);font-family:var(--font-m);font-weight:500;letter-spacing:.14em;font-size:.76rem;text-transform:uppercase}
.eyebrow::before{content:"> ";opacity:.7}

/* Paneles */
.panel{background:var(--surface);border:1px solid var(--line);border-radius:14px;padding:24px;margin:20px 0;position:relative}
.panel h2{font-size:1.6rem;margin-bottom:10px}
.lead{color:var(--muted);max-width:72ch}

/* Formularios */
.field{margin:15px 0}
.field label{display:block;font-weight:600;margin-bottom:6px;font-size:.92rem}
.field input{width:100%;padding:12px 13px;border-radius:9px;border:1px solid var(--line-2);background:var(--surface-2);color:var(--ink);font-size:1rem;font-family:var(--font-m)}
.field input:focus{outline:2px solid var(--green);border-color:var(--green)}
.hint{color:var(--muted);font-size:.82rem;margin-top:5px}
.err{color:var(--red);font-size:.85rem;margin-top:6px}

/* Telemetría / cabecera del participante */
.telemetry{display:flex;flex-wrap:wrap;gap:16px;align-items:center;justify-content:space-between;background:var(--surface-2);border:1px solid var(--line);border-radius:12px;padding:15px 18px;margin:20px 0}
.who{font-family:var(--font-d);font-weight:600;font-size:1.05rem}
.who small{display:block;font-family:var(--font-m);font-weight:400;color:var(--muted);font-size:.74rem}
.stat .num{font-family:var(--font-d);font-weight:700;font-size:1.5rem;color:var(--green)}
.stat .lbl{color:var(--muted);font-size:.76rem}
.bar{height:8px;background:var(--surface);border-radius:6px;overflow:hidden;width:180px;border:1px solid var(--line-2)}
.bar>i{display:block;height:100%;background:linear-gradient(90deg,var(--green-deep),var(--green));box-shadow:0 0 10px rgba(40,229,140,.5)}

/* Tarjetas de misión */
.mission{background:var(--surface);border:1px solid var(--line);border-left:4px solid var(--amber);border-radius:12px;padding:20px;margin:16px 0}
.mission.done{border-left-color:var(--green)}
.mission.locked{opacity:.58}
.mission h3{font-size:1.3rem}
.badge{display:inline-flex;align-items:center;gap:6px;font-size:.74rem;font-weight:600;padding:4px 10px;border-radius:999px;margin-left:8px;font-family:var(--font-m)}
.badge.pend{background:var(--amber-soft);color:var(--amber)}
.badge.ok{background:var(--green-soft);color:var(--green)}
.badge.lock{background:var(--surface-2);color:var(--muted)}
.mission p.n{color:var(--muted);margin:8px 0}
.pista{font-size:.88rem;color:var(--muted);border-left:2px solid var(--line-2);padding-left:12px;margin:10px 0}
.submit-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
.submit-row input{flex:1;min-width:200px;padding:11px 13px;border-radius:9px;border:1px solid var(--line-2);background:var(--surface-2);color:var(--ink);font-family:var(--font-m)}
.submit-row input:focus{outline:2px solid var(--green);border-color:var(--green)}
.feedback{font-size:.88rem;margin-top:8px}
.feedback.good{color:var(--green)}.feedback.bad{color:var(--red)}
.pts{font-family:var(--font-m);color:var(--green);font-weight:500;font-size:.88rem}

/* ===== Bloques de comando con botón copiar ===== */
.cmd{background:var(--surface-2);border:1px solid var(--line-2);border-radius:10px;margin:14px 0;overflow:hidden}
.cmd-head{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:9px 12px;background:var(--panel-head);border-bottom:1px solid var(--line)}
.cmd-head .t{font-family:var(--font-d);font-size:1rem;color:var(--ink)}
.cmd-head .where{font-family:var(--font-m);font-size:.68rem;padding:3px 8px;border-radius:999px;white-space:nowrap}
.where.kali{background:var(--accent-soft);color:var(--accent)}
.where.ssh{background:var(--green-soft);color:var(--green)}
.where.web,.where.local{background:var(--amber-soft);color:var(--amber)}
.cmd-body{display:flex;align-items:stretch;gap:0}
.cmd-body pre{margin:0;flex:1;min-width:0;overflow-x:auto;padding:12px 14px;font-family:var(--font-m);font-size:.86rem;color:var(--green);line-height:1.5;white-space:pre}
.cmd-body pre::selection,.cmd-body pre *::selection{background:rgba(40,229,140,.25)}
.copy-btn{flex:none;align-self:stretch;border:none;border-left:1px solid var(--line);background:var(--panel-head);color:var(--muted);font-family:var(--font-m);font-size:.74rem;font-weight:600;padding:0 14px;cursor:pointer;display:flex;align-items:center;gap:6px;transition:color .15s,background .15s}
.copy-btn:hover{color:var(--green);background:#0b1a16}
.copy-btn.ok{color:var(--green)}
.copy-btn.fail{color:var(--red)}

/* Explicaciones A–H del comando */
.cmd-exp{border:1px solid var(--line);border-top:none;border-radius:0 0 10px 10px;margin:-14px 0 14px;background:rgba(15,23,25,.5)}
.cmd-exp .row{padding:11px 14px;border-top:1px solid var(--line)}
.cmd-exp .row:first-child{border-top:none}
.cmd-exp .q{font-family:var(--font-m);font-size:.72rem;letter-spacing:.04em;text-transform:uppercase;color:var(--accent);margin:0 0 3px}
.cmd-exp .a{margin:0;font-size:.9rem;color:var(--ink)}
.cmd-exp .a.m{color:var(--muted)}
.cmd-exp ul{margin:4px 0 0;padding-left:18px;font-size:.88rem;color:var(--ink)}
.cmd-exp ul li{margin:2px 0}
.cmd-exp code{background:var(--surface-2);border:1px solid var(--line);border-radius:5px;padding:1px 5px;color:var(--accent);font-size:.84rem}
.cmd-exp details{margin-top:4px}
.cmd-exp summary{cursor:pointer;color:var(--amber);font-size:.84rem;font-family:var(--font-m)}
.cmd-exp details[open] summary{margin-bottom:6px}

/* Pistas progresivas */
.hints{margin:14px 0}
.hints>.lbl{font-family:var(--font-m);font-size:.74rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:6px}
.hint-item{border:1px solid var(--line-2);border-radius:9px;margin:7px 0;background:var(--surface-2)}
.hint-item>summary{list-style:none;cursor:pointer;padding:10px 13px;font-weight:600;color:var(--amber);display:flex;align-items:center;gap:8px;font-size:.9rem}
.hint-item>summary::-webkit-details-marker{display:none}
.hint-item>summary .lv{font-family:var(--font-m);font-size:.7rem;background:var(--amber-soft);color:var(--amber);border-radius:999px;padding:2px 8px}
.hint-item[open]>summary{border-bottom:1px solid var(--line)}
.hint-item .body{padding:11px 13px;color:var(--muted);font-size:.9rem}
.hint-item .body code{background:var(--surface);border:1px solid var(--line);border-radius:5px;padding:1px 5px;color:var(--accent);font-family:var(--font-m);font-size:.84rem}

/* Chips de datos técnicos */
.kv{display:inline-flex;align-items:center;gap:6px;font-family:var(--font-m);font-size:.8rem;background:var(--surface-2);border:1px solid var(--line-2);border-radius:7px;padding:3px 9px;margin:3px 4px 3px 0}
.kv b{color:var(--accent);font-weight:600}

/* Resultado / final */
.result-hero{text-align:center;padding:18px 0 6px}
.result-hero .big{font-family:var(--font-d);font-weight:700;font-size:clamp(3rem,12vw,6rem);color:var(--green);line-height:1;text-shadow:0 0 30px rgba(40,229,140,.35)}
.dots{display:flex;gap:10px;justify-content:center;margin:20px 0;flex-wrap:wrap}
.dots span{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;font-family:var(--font-d);font-weight:700;border:2px solid var(--amber);color:var(--amber)}
.dots span.on{border-color:var(--green);color:var(--green);box-shadow:0 0 14px rgba(40,229,140,.4)}

/* Tablas */
table{border-collapse:collapse;width:100%;min-width:640px;font-size:.92rem}
th,td{text-align:left;padding:10px 12px;border-bottom:1px solid var(--line)}
th{font-family:var(--font-d);color:var(--muted);font-size:.85rem}
.table-scroll{overflow-x:auto}
.mdot{display:inline-block;width:12px;height:12px;border-radius:50%;margin-right:3px;background:var(--amber)}
.mdot.on{background:var(--green)}
.tag{font-family:var(--font-m);font-weight:600;color:var(--green)}

.center{text-align:center}.mt{margin-top:18px}
.footer{color:var(--muted);font-size:.8rem;text-align:center;padding:24px 0;font-family:var(--font-m)}

/* Narrativa / dossier */
.story p{margin:0 0 12px}
.story strong{color:var(--ink)}
.dossier{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-top:18px}
.dossier>div{background:var(--surface-2);border:1px solid var(--line);border-radius:11px;padding:14px 16px}
.dossier h4{margin:0 0 6px;font-family:var(--font-m);font-size:.8rem;color:var(--green);letter-spacing:.04em;text-transform:uppercase}
.dossier p{margin:0;color:var(--muted);font-size:.9rem}
.quote{border-left:3px solid var(--green);padding:4px 0 4px 16px;margin:18px 0;color:var(--ink);font-style:italic}
</style>
@stack('styles')
</head>
<body>
<div class="checker gold"></div>
<div class="topbar">
  <div class="brand"><span class="dot"></span><span>CYBERQUEST<small>OPERACIÓN RASCASSE · UNAB Racing</small></span></div>
  @if(!request()->routeIs('landing'))
    <a class="btn ghost small" href="{{ route('landing') }}">Inicio</a>
  @endif
</div>
@yield('content')
<div class="checker"></div>
<p class="footer">// Desarrollado por Mateo Sandoval, Juan David Contreras, Wilson Suárez y Ralph Castellanos · UNAB</p>

<script>
// Copia al portapapeles el comando exacto (data-cmd), con confirmación visual y
// reserva para navegadores sin API de portapapeles.
document.addEventListener('click', function (e) {
  var btn = e.target.closest('.copy-btn');
  if (!btn) return;
  var pre = btn.parentElement.querySelector('pre');
  var text = btn.getAttribute('data-cmd');
  if (text === null && pre) text = pre.innerText;
  if (text === null) return;
  var done = function (ok) {
    var label = btn.querySelector('.lbl') || btn;
    var original = btn.getAttribute('data-label') || 'Copiar';
    btn.classList.add(ok ? 'ok' : 'fail');
    label.textContent = ok ? '✓ Copiado' : '✕ Error';
    setTimeout(function () {
      btn.classList.remove('ok', 'fail');
      label.textContent = original;
    }, 1600);
  };
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(function () { done(true); }, function () { fallback(text, done); });
  } else {
    fallback(text, done);
  }
  function fallback(t, cb) {
    try {
      var ta = document.createElement('textarea');
      ta.value = t; ta.setAttribute('readonly', '');
      ta.style.position = 'absolute'; ta.style.left = '-9999px';
      document.body.appendChild(ta); ta.select();
      var ok = document.execCommand('copy');
      document.body.removeChild(ta); cb(ok);
    } catch (err) { cb(false); }
  }
});
</script>
@stack('scripts')
</body>
</html>
