<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\Student;
use App\Models\Submission;
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

        return view('misiones', compact('s', 'missions', 'done'));
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

    public function logout()
    {
        session()->forget('student_uid');

        return redirect()->route('landing');
    }
}
