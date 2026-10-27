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
                    'uid' => 'Este ID ya está registrado en la operación. Si eres tú y perdiste la sesión, pídele al profesor que habilite tu reingreso.',
                ]);
            }
            $existing->update(['allow_reentry' => false]);
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
        session(['student_uid' => $student->uid]);

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

        $meses = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $fecha = $certificate->issued_at;
        $fechaTexto = $fecha->day.' de '.$meses[(int) $fecha->month].' de '.$fecha->year;

        $pdf = Pdf::loadView('certificado', [
            'nombre' => $s->nombre,
            'code' => $certificate->code,
            'fechaTexto' => $fechaTexto,
            'logo' => $logo,
        ])->setPaper('a4', 'landscape');

        $slug = \Illuminate\Support\Str::slug($s->nombre) ?: 'participante';

        return $pdf->download("certificado-ciberguardians-{$slug}.pdf");
    }

    public function logout()
    {
        session()->forget('student_uid');

        return redirect()->route('landing');
    }
}
