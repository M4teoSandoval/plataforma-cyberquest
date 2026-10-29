@php
    // El tamaño del nombre se ajusta para que siempre quepa completo y centrado,
    // conservando tildes y la letra ñ (fuente DejaVu, con soporte Unicode).
    $len = mb_strlen($nombre);
    // Tamaños pensados para que prácticamente cualquier nombre real quepa en
    // UNA sola línea (los largos se encogen más), evitando que el documento
    // crezca de alto y empuje las firmas fuera de la hoja.
    $nameSize = $len <= 20 ? 42 : ($len <= 30 ? 34 : ($len <= 40 ? 27 : ($len <= 52 ? 21 : 18)));
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 0; }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: "DejaVu Sans", sans-serif;
        color: #16222E;
        background: #ffffff;
    }
    .sheet {
        position: relative;
        width: 100%;
        height: 585pt;             /* A4 apaisado (595pt) menos un pequeño margen */
        background: #ffffff;
        overflow: hidden;
    }
    /* Marco doble con acento dorado */
    .frame-outer {
        position: absolute; top: 14pt; left: 14pt; right: 14pt; bottom: 14pt;
        border: 2pt solid #0E2438;
    }
    .frame-inner {
        position: absolute; top: 20pt; left: 20pt; right: 20pt; bottom: 20pt;
        border: 0.8pt solid #C8A14B;
    }
    /* Bandas decorativas tipo "bandera a cuadros" (una fila de celdas) */
    .checker { position: absolute; left: 21pt; right: 21pt; height: 6pt; }
    .checker.top { top: 22pt; }
    .checker.bottom { bottom: 22pt; }
    .checker table { width: 100%; border-collapse: collapse; }
    .checker td { padding: 0; height: 6pt; line-height: 6pt; font-size: 1pt; }

    .content { padding: 34pt 46pt 0; text-align: center; }

    .logo { height: 46pt; margin-bottom: 6pt; }
    .org {
        font-size: 9pt; letter-spacing: 3pt; color: #4A6075;
        text-transform: uppercase; margin: 2pt 0 0;
    }
    .title {
        font-family: "DejaVu Serif", serif;
        font-size: 30pt; font-weight: bold; letter-spacing: 4pt;
        color: #0E2438; margin: 10pt 0 2pt;
    }
    .rule { width: 90pt; height: 2pt; background: #C8A14B; margin: 6pt auto 12pt; }
    .intro { font-size: 10.5pt; color: #3A4D5E; margin: 0; }

    .name {
        font-family: "DejaVu Serif", serif;
        font-size: {{ $nameSize }}pt; font-weight: bold; color: #0E2438;
        margin: 10pt 0 6pt; line-height: 1.1;
    }
    .name-underline { width: 300pt; height: 0.8pt; background: #C8A14B; margin: 0 auto 12pt; }

    .body { font-size: 10pt; color: #3A4D5E; line-height: 1.5; margin: 0 auto; max-width: 560pt; }
    .workshop {
        font-family: "DejaVu Serif", serif;
        font-size: 13pt; font-weight: bold; color: #16222E; margin: 8pt 0; line-height: 1.3;
    }

    .meta { margin-top: 10pt; width: 100%; }
    .meta td { font-size: 8.8pt; color: #3A4D5E; padding: 1.5pt 10pt; vertical-align: top; }
    .meta .lbl { color: #9A6F1F; font-weight: bold; text-transform: uppercase; letter-spacing: 1pt; font-size: 7.5pt; }

    .facilitators { font-size: 10pt; color: #16222E; margin-top: 12pt; }
    .facilitators b { color: #0E2438; }

    .footer-row { margin: 8pt 46pt 0; }
    .footer-row table { width: 100%; border-collapse: collapse; }
    .footer-row td { width: 50%; text-align: center; vertical-align: top; padding: 0 30pt; }
    .sign .firma { display: block; height: 42pt; margin: 0 auto 1pt; }
    .sign .line { border-top: 0.8pt solid #16222E; margin: 0 0 3pt; }
    .sign .role { font-size: 8pt; color: #4A6075; }
    .sign .nm { font-size: 9pt; font-weight: bold; color: #0E2438; }

    .certid {
        text-align: center; margin-top: 4pt;
        font-family: "DejaVu Sans Mono", monospace; font-size: 7.5pt; color: #6B7C8A;
        line-height: 1.5;
    }
    .certid b { color: #0E2438; }
</style>
</head>
<body>
<div class="sheet">
    <div class="frame-outer"></div>
    <div class="frame-inner"></div>
    @php
        $cells = '';
        for ($i = 0; $i < 40; $i++) {
            $cells .= '<td style="background:'.($i % 2 ? '#0E2438' : '#C8A14B').'"></td>';
        }
    @endphp
    <div class="checker top"><table><tr>{!! $cells !!}</tr></table></div>
    <div class="checker bottom"><table><tr>{!! $cells !!}</tr></table></div>

    <div class="content">
        @if($logo)<img class="logo" src="{{ $logo }}" alt="UNAB">@endif
        <p class="org">Universidad Autónoma de Bucaramanga · Semana de Ingeniería UNAB 2026</p>

        <div class="title">CERTIFICADO DE PARTICIPACIÓN</div>
        <div class="rule"></div>

        <p class="intro">La organización del taller hace constar que</p>

        <div class="name">{{ $nombre }}</div>
        <div class="name-underline"></div>

        <p class="body">
            participó y completó satisfactoriamente las actividades prácticas del
        </p>
        <p class="workshop">Taller Agencia CiberGuardians: Código Rojo<br>Infiltrándose en la Mente del Atacante</p>
        <p class="body">
            Una actividad formativa orientada a la introducción en Red Team, ciberseguridad ofensiva
            y hacking ético mediante escenarios controlados de investigación, reconocimiento,
            análisis de evidencias y comprensión de posibles vectores de ataque, promoviendo
            siempre una práctica ética y responsable.
        </p>

        <table class="meta">
            <tr>
                <td style="text-align:right; width:33%;"><span class="lbl">Horario</span><br>4:00 p.&nbsp;m. a 6:00 p.&nbsp;m.</td>
                <td style="text-align:center; width:34%;"><span class="lbl">Lugar</span><br>Laboratorio de Telecomunicaciones</td>
                <td style="text-align:left; width:33%;"><span class="lbl">Evento</span><br>Semana de Ingeniería UNAB 2026</td>
            </tr>
        </table>

        <div class="certid">
            Certificado N.º <b>{{ $code }}</b> · Emitido el {{ $fechaTexto }}
        </div>
    </div>

    <div class="footer-row">
        <table><tr>
            <td class="sign">
                @if(!empty($firmaYamid))
                    <img class="firma" src="{{ $firmaYamid }}" alt="Firma de Yamid Gabriel Gamba González">
                @endif
                <div class="line"></div>
                <div class="nm">Yamid Gabriel Gamba González</div>
                <div class="role">Facilitador</div>
            </td>
            <td class="sign">
                @if(!empty($firmaFabian))
                    <img class="firma" src="{{ $firmaFabian }}" alt="Firma de Fabián Suárez">
                @endif
                <div class="line"></div>
                <div class="nm">Fabián Suárez</div>
                <div class="role">Facilitador</div>
            </td>
        </tr></table>
    </div>
</div>
</body>
</html>
