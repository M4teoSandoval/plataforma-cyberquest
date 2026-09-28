<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Operación RASCASSE — CYBERQUEST</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--bg:#0A1622;--surface:#13273B;--surface-2:#0F1E2E;--ink:#EAF2F8;--muted:#90A6BA;
--line:#233A52;--gold:#E8B84B;--green:#31C27C;--green-soft:#10322A;--red:#E24B3B;--red-soft:#33202A;
--font-d:"Saira Condensed",system-ui,sans-serif;--font-b:"Inter",system-ui,sans-serif;box-sizing:border-box;}
*{box-sizing:inherit}
body{margin:0;background:var(--bg);color:var(--ink);font-family:var(--font-b);line-height:1.55}
h1,h2,h3{font-family:var(--font-d);font-weight:700;line-height:1.05;margin:0}
a{color:var(--gold)}
.wrap{max-width:1040px;margin:0 auto;padding:0 22px}
.checker{height:14px;background:conic-gradient(var(--ink) 90deg,transparent 90deg 180deg,var(--ink) 180deg 270deg,transparent 270deg) 0 0/14px 14px;opacity:.9}
.checker.gold{background:conic-gradient(var(--gold) 90deg,transparent 90deg 180deg,var(--gold) 180deg 270deg,transparent 270deg) 0 0/12px 12px}
.topbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 22px;border-bottom:1px solid var(--line);background:var(--surface-2)}
.brand{display:flex;align-items:center;gap:12px;font-family:var(--font-d);font-weight:700;font-size:1.05rem}
.brand .dot{width:12px;height:12px;border-radius:50%;background:var(--gold);box-shadow:0 0 0 4px rgba(232,184,75,.15)}
.brand small{display:block;color:var(--muted);font-family:var(--font-b);font-weight:500;font-size:.72rem;letter-spacing:.04em}
.btn{border:1px solid transparent;border-radius:10px;padding:13px 22px;font-size:1rem;font-weight:600;background:var(--gold);color:#1a1205;cursor:pointer;font-family:var(--font-b);text-decoration:none;display:inline-block}
.btn.ghost{background:transparent;color:var(--ink);border-color:var(--line)}
.btn.small{padding:9px 15px;font-size:.9rem}
.eyebrow{color:var(--gold);font-family:var(--font-d);font-weight:600;letter-spacing:.16em;font-size:.86rem;text-transform:uppercase}
.panel{background:var(--surface);border:1px solid var(--line);border-radius:16px;padding:26px;margin:22px 0}
.panel h2{font-size:1.6rem;margin-bottom:10px}
.lead{color:var(--muted);max-width:70ch}
.field{margin:16px 0}
.field label{display:block;font-weight:600;margin-bottom:6px}
.field input{width:100%;padding:13px 14px;border-radius:10px;border:1px solid var(--line);background:var(--surface-2);color:var(--ink);font-size:1rem;font-family:var(--font-b)}
.field input:focus{outline:2px solid var(--gold);border-color:var(--gold)}
.hint{color:var(--muted);font-size:.82rem;margin-top:5px}
.err{color:var(--red);font-size:.85rem;margin-top:6px}
.telemetry{display:flex;flex-wrap:wrap;gap:18px;align-items:center;justify-content:space-between;background:var(--surface-2);border:1px solid var(--line);border-radius:14px;padding:16px 20px;margin:22px 0}
.who{font-family:var(--font-d);font-weight:600;font-size:1.05rem}
.who small{display:block;font-family:var(--font-b);font-weight:400;color:var(--muted);font-size:.8rem}
.stat .num{font-family:var(--font-d);font-weight:700;font-size:1.5rem;color:var(--gold)}
.stat .lbl{color:var(--muted);font-size:.78rem}
.bar{height:8px;background:var(--surface);border-radius:6px;overflow:hidden;width:180px;border:1px solid var(--line)}
.bar>i{display:block;height:100%;background:var(--gold)}
.mission{background:var(--surface);border:1px solid var(--line);border-left:5px solid var(--red);border-radius:14px;padding:20px;margin:16px 0}
.mission.done{border-left-color:var(--green)}
.mission h3{font-size:1.3rem}
.badge{display:inline-flex;align-items:center;gap:7px;font-size:.78rem;font-weight:600;padding:4px 10px;border-radius:999px;margin-left:8px}
.badge.pend{background:var(--red-soft);color:var(--red)}
.badge.ok{background:var(--green-soft);color:var(--green)}
.mission p.n{color:var(--muted);margin:8px 0}
.pista{font-size:.88rem;color:var(--muted);border-left:2px solid var(--line);padding-left:12px;margin:10px 0}
.submit-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
.submit-row input{flex:1;min-width:220px;padding:11px 13px;border-radius:9px;border:1px solid var(--line);background:var(--surface-2);color:var(--ink);font-family:var(--font-b)}
.feedback{font-size:.88rem;margin-top:8px}
.feedback.good{color:var(--green)}.feedback.bad{color:var(--red)}
.pts{font-family:var(--font-d);color:var(--gold);font-weight:600;font-size:.95rem}
.result-hero{text-align:center;padding:20px 0 6px}
.result-hero .big{font-family:var(--font-d);font-weight:700;font-size:clamp(3rem,12vw,6rem);color:var(--gold);line-height:1}
.dots{display:flex;gap:10px;justify-content:center;margin:22px 0}
.dots span{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;font-family:var(--font-d);font-weight:700;border:2px solid var(--red);color:var(--red)}
.dots span.on{border-color:var(--green);color:var(--green)}
table{border-collapse:collapse;width:100%;min-width:640px;font-size:.92rem}
th,td{text-align:left;padding:11px 12px;border-bottom:1px solid var(--line)}
th{font-family:var(--font-d);color:var(--muted);font-size:.85rem}
.mdot{display:inline-block;width:12px;height:12px;border-radius:50%;margin-right:3px;background:var(--red)}
.mdot.on{background:var(--green)}
.tag{font-family:var(--font-d);font-weight:700;color:var(--gold)}
.table-scroll{overflow-x:auto}
.center{text-align:center}.mt{margin-top:18px}
.footer{color:var(--muted);font-size:.82rem;text-align:center;padding:26px 0}
.wrap.wide{max-width:1320px}
.story p{margin:0 0 12px}
.story strong{color:var(--ink)}
.dossier{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-top:18px}
.dossier>div{background:var(--surface-2);border:1px solid var(--line);border-radius:12px;padding:14px 16px}
.dossier h4{margin:0 0 6px;font-family:var(--font-d);font-size:1rem;color:var(--gold);letter-spacing:.04em;text-transform:uppercase}
.dossier p{margin:0;color:var(--muted);font-size:.9rem}
.quote{border-left:3px solid var(--gold);padding:4px 0 4px 16px;margin:18px 0;color:var(--ink);font-style:italic}
</style>
@stack('styles')
</head>
<body>
<div class="checker gold"></div>
<div class="topbar">
  <div class="brand"><span class="dot"></span><span>OPERACIÓN RASCASSE<small>Hackathon CYBERQUEST · UNAB Racing</small></span></div>
  @if(!request()->routeIs('landing'))
    <a class="btn ghost small" href="{{ route('landing') }}">Inicio</a>
  @endif
</div>
@yield('content')
<div class="checker"></div>
<p class="footer">Entorno académico y aislado · Todos los equipos, personas y datos son ficticios.</p>
</body>
</html>