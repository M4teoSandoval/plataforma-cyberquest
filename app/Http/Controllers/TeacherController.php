<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\Student;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function showLogin()
    {
        return view('profesor-login');
    }

    public function login(Request $r)
    {
        $u = strtolower(trim($r->input('user')));
        $p = $r->input('pass');
        if ($u === strtolower(config('cyberquest.teacher_user')) && $p === config('cyberquest.teacher_pass')) {
            session(['is_teacher' => true]);

            return redirect()->route('profesor.panel');
        }

        return back()->withErrors(['login' => 'Usuario o contraseña incorrectos.']);
    }

    public function panel()
    {
        if (! session('is_teacher')) {
            return redirect()->route('profesor.login');
        }
        $missions = Mission::orderBy('orden')->get();
        $students = Student::with('submissions')->orderBy('nombre')->get();

        return view('profesor-panel', compact('students', 'missions'));
    }

    public function logout()
    {
        session()->forget('is_teacher');

        return redirect()->route('landing');
    }
}
