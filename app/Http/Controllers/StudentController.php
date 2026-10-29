<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Mission;
use App\Models\Student;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

    public function showRegister()
    {
        if ($this->current()) {
            return redirect()->route('misiones');
        }

        // Si ya hay una solicitud de reingreso en curso, retoma la pantalla de espera.
        if (session('reentry_uid')) {
            return redirect()->route('reingreso.espera');
        }

        return view('registro');
    }

    public function register(Request $r)
    {
        // Normalizar antes de validar para que la comparación de duplicados no dependa de mayúsculas.
        $r->merge([
            'uid' => strtoupper(trim((string) $r->input('uid'))),
            'correo' => strtolower(trim((string) $r->input('correo'))),
        ]);

        $data = $r->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:120'],
            'correo' => ['required', 'email', 'max:120', 'regex:/@unab\.edu\.co$/i'],
            'uid' => ['required', 'regex:/^U00\d{3,}$/'],
        ], [], ['correo' => 'correo', 'uid' => 'ID']);

        $existing = Student::where('uid', $data['uid'])->first();

        if ($existing) {
            // Un ID ya registrado solo puede volver a entrar si el profesor lo habilitó
            // y el correo coincide con el del registro original.
            if (! $existing->allow_reentry || $existing->correo !== $data['correo']) {
                return back()->withInput()->withErrors([
                    'uid' => 'Este ID ya está registrado en la operación. Si eres tú y perdiste la sesión, usa la opción «Solicitar reingreso» aquí abajo.',
                ]);
            }
            $existing->update(['allow_reentry' => false, 'reentry_requested_at' => null]);
            $student = $existing;
        } else {
            if (Student::where('correo', $data['correo'])->exists()) {
                return back()->withInput()->withErrors([
                    'correo' => 'Este correo ya está asociado a otro ID.',
                ]);
            }
            $student = Student::create([
                'uid' => $data['uid'], 'nombre' => $data['nombre'],
                'correo' => $data['correo'], 'started_at' => now(),
            ]);
        }

        $r->session()->regenerate();
        $r->session()->forget('reentry_uid');
        session(['student_uid' => $student->uid]);

        return redirect()->route('misiones');
    }

    /**
     * El estudiante pide reingreso cuando perdió su sesión. La solicitud queda
     * visible en el panel del profesor; solo se entra cuando él la aprueba.
     */
    public function requestReentry(Request $r)
    {
        if ($this->current()) {
            return redirect()->route('misiones');
        }

        $r->merge([
            'uid' => strtoupper(trim((string) $r->input('uid'))),
            'correo' => strtolower(trim((string) $r->input('correo'))),
        ]);

        $data = $r->validate([
            'uid' => ['required', 'regex:/^U00\d{3,}$/'],
            'correo' => ['required', 'email', 'max:120'],
        ], [], ['correo' => 'correo', 'uid' => 'ID']);

        $student = Student::where('uid', $data['uid'])->first();

        // La identidad se valida solo con datos del backend: el ID debe existir
        // y el correo debe coincidir con el del registro original.
        if (! $student || $student->correo !== $data['correo']) {
            return back()->withInput()->withErrors([
                'uid' => 'No encontramos un registro con ese ID y correo. Verifica los datos.',
            ]);
        }

        // Si el profesor ya habilitó el reingreso, se entra de una vez.
        if ($student->allow_reentry) {
            return $this->completeReentry($r, $student);
        }

        $student->update(['reentry_requested_at' => now()]);
        $r->session()->put('reentry_uid', $student->uid);

        return redirect()->route('reingreso.espera');
    }

    /**
     * Pantalla de espera mientras el profesor aprueba la solicitud.
     */
    public function reentryWait(Request $r)
    {
        if ($this->current()) {
            return redirect()->route('misiones');
        }

        $student = $this->pendingReentryStudent();

        if (! $student) {
            session()->forget('reentry_uid');

            return redirect()->route('registro');
        }

        // Aprobado mientras estaba en esta pantalla: entra sin volver a escribir nada.
        if ($student->allow_reentry) {
            return $this->completeReentry($r, $student);
        }

        if (! $student->reentry_requested_at) {
            session()->forget('reentry_uid');

            return redirect()->route('registro')->withErrors([
                'uid' => 'La solicitud de reingreso no fue aprobada. Puedes volver a solicitarla.',
            ]);
        }

        return view('reingreso-espera', compact('student'));
    }

    /**
     * Consulta de estado usada por la pantalla de espera (polling).
     */
    public function reentryStatus(Request $r)
    {
        if ($this->current()) {
            return response()->json(['status' => 'approved', 'redirect' => route('misiones')]);
        }

        $student = $this->pendingReentryStudent();

        if (! $student) {
            $r->session()->forget('reentry_uid');

            return response()->json(['status' => 'expired', 'redirect' => route('registro')]);
        }

        if ($student->allow_reentry) {
            $this->completeReentry($r, $student);

            return response()->json(['status' => 'approved', 'redirect' => route('misiones')]);
        }

        if ($student->reentry_requested_at) {
            return response()->json(['status' => 'pending']);
        }

        $r->session()->forget('reentry_uid');

        return response()->json(['status' => 'rejected', 'redirect' => route('registro')]);
    }

    private function pendingReentryStudent(): ?Student
    {
        $uid = session('reentry_uid');

        return $uid ? Student::where('uid', $uid)->first() : null;
    }

    /**
     * Consume la aprobación y abre la sesión del estudiante.
     */
    private function completeReentry(Request $r, Student $student)
    {
        $student->update(['allow_reentry' => false, 'reentry_requested_at' => null]);

        $r->session()->forget('reentry_uid');
        $r->session()->regenerate();
        $r->session()->put('student_uid', $student->uid);

        return redirect()->route('misiones');
    }

    private function current()
    {
        $uid = session('student_uid');

        return $uid ? Student::where('uid', $uid)->first() : null;
    }

    public function missions()
    {
        $s = $this->current();
        if (! $s) {
            return redirect()->route('registro');
        }
        $missions = Mission::orderBy('orden')->get();
        $done = $s->completedMissionIds();
        $guide = require resource_path('missions/walkthrough.php');

        return view('misiones', compact('s', 'missions', 'done', 'guide'));
    }

    public function submitFlag(Request $r, Mission $mission)
    {
        $s = $this->current();
        if (! $s) {
            return redirect()->route('registro');
        }
        $r->validate(['flag' => ['required', 'string']]);

        $hash = hash('sha256', trim($r->input('flag')));
        $ok = hash_equals($mission->flag_hash, $hash);

        Submission::create([
            'student_id' => $s->id, 'mission_id' => $mission->id, 'correcta' => $ok,
        ]);

        return back()->with($ok ? 'ok_'.$mission->id : 'bad_'.$mission->id, true);
    }

    public function finish()
    {
        $s = $this->current();
        if (! $s) {
            return redirect()->route('registro');
        }
        $s->update(['finished' => true, 'finished_at' => now()]);

        return redirect()->route('resultado');
    }

    public function results()
    {
        $s = $this->current();
        if (! $s) {
            return redirect()->route('registro');
        }
        $missions = Mission::orderBy('orden')->get();
        $done = $s->completedMissionIds();

        return view('resultado', compact('s', 'missions', 'done'));
    }

    /**
     * Envía al formulario de satisfacción configurado en SURVEY_URL.
     * La URL vive en config/cyberquest.php para poder cambiarla sin tocar
     * las vistas; si no está configurada, se vuelve al resultado.
     */
    public function survey()
    {
        $url = config('cyberquest.survey_url');

        if (! $url) {
            return redirect()->route('resultado');
        }

        return redirect()->away($url);
    }

    /**
     * Genera y descarga el certificado PDF personalizado.
     *
     * Seguridad: la autorización se decide SOLO con datos del backend.
     *  - Debe existir una sesión de estudiante válida.
     *  - El estudiante debe tener las 5 misiones realmente resueltas
     *    (flags validadas por hash en submitFlag), no un estado del navegador.
     *  - El nombre sale de $student->nombre (fuente confiable), nunca del request.
     *  - El registro del certificado es idempotente: una descarga repetida
     *    reutiliza el mismo código y no altera la puntuación ni el progreso.
     */
    public function certificate()
    {
        $s = $this->current();
        if (! $s) {
            return redirect()->route('registro');
        }

        if (! $s->hasCompletedOperation()) {
            return redirect()->route('resultado')
                ->with('cert_error', 'El certificado estará disponible cuando completes las cinco misiones.');
        }

        // Reutiliza el certificado existente o lo crea una sola vez.
        $certificate = Certificate::firstOrCreate(
            ['student_id' => $s->id],
            ['code' => Certificate::generateCode(), 'issued_at' => now()],
        );

        $logoPath = public_path('images/logo-universidad-unab.png');
        $logo = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        // Firmas de los facilitadores (PNG con fondo transparente). Se incrustan
        // en el PDF igual que el logo para que el documento sea autocontenido.
        $firmaYamidPath = public_path('images/firmas/firma_yamid-removebg-preview.png');
        $firmaYamid = is_file($firmaYamidPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($firmaYamidPath))
            : null;

        $firmaFabianPath = public_path('images/firmas/firma_fabian-removebg-preview.png');
        $firmaFabian = is_file($firmaFabianPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($firmaFabianPath))
            : null;

        $meses = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $fecha = $certificate->issued_at;
        $fechaTexto = $fecha->day.' de '.$meses[(int) $fecha->month].' de '.$fecha->year;

        $pdf = Pdf::loadView('certificado', [
            'nombre' => $s->nombre,
            'code' => $certificate->code,
            'fechaTexto' => $fechaTexto,
            'logo' => $logo,
            'firmaYamid' => $firmaYamid,
            'firmaFabian' => $firmaFabian,
        ])->setPaper('a4', 'landscape');

        $slug = \Illuminate\Support\Str::slug($s->nombre) ?: 'participante';

        return $pdf->download("certificado-ciberguardians-{$slug}.pdf");
    }

    public function logout()
    {
        session()->forget(['student_uid', 'reentry_uid']);

        return redirect()->route('landing');
    }
}
