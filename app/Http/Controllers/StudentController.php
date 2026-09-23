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
        return view('registro');
    }

    public function register(Request $r)
    {
        $data = $r->validate([
            'nombre' => ['required', 'string', 'min:3'],
            'correo' => ['required', 'email', 'regex:/@unab\.edu\.co$/i'],
            'uid' => ['required', 'regex:/^U00\d{3,}$/i'],
        ], [], ['correo' => 'correo', 'uid' => 'ID']);

        $uid = strtoupper($data['uid']);
        $student = Student::updateOrCreate(
            ['uid' => $uid],
            ['nombre' => $data['nombre'], 'correo' => strtolower($data['correo']), 'started_at' => now()]
        );
        session(['student_uid' => $uid]);

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
