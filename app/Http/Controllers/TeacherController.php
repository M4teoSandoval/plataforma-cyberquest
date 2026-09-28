<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
        $rows = $this->buildRows($missions);

        $totalAttempts = $rows->sum('attempts');
        $totalCorrect = $rows->sum('correct');
        $kpis = [
            'registered' => $rows->count(),
            'active' => $rows->where('status', 'activo')->count(),
            'finished' => $rows->where('finished', true)->count(),
            'avgPoints' => $rows->count() ? (int) round($rows->avg('points')) : 0,
            'maxPoints' => $missions->sum('puntos'),
            'attempts' => $totalAttempts,
            'accuracy' => $totalAttempts ? round($totalCorrect / $totalAttempts * 100) : 0,
            'complete' => $rows->where('solvedCount', $missions->count())->count(),
        ];

        $missionStats = $missions->map(function ($m) use ($rows) {
            $solvers = $rows->filter(fn ($row) => isset($row['solved'][$m->id]))
                ->sortBy(fn ($row) => $row['solved'][$m->id]);
            $attempts = $rows->sum(fn ($row) => $row['perMission'][$m->id]);
            $tried = $rows->filter(fn ($row) => $row['perMission'][$m->id] > 0)->count();
            $first = $solvers->first();

            return [
                'mission' => $m,
                'solved' => $solvers->count(),
                'tried' => $tried,
                'attempts' => $attempts,
                'firstBlood' => $first ? ['nombre' => $first['student']->nombre, 'at' => $first['solved'][$m->id]] : null,
            ];
        });

        // Cuántos estudiantes llevan 0, 1, 2… misiones resueltas.
        $distribution = collect(range(0, $missions->count()))
            ->mapWithKeys(fn ($n) => [$n => $rows->where('solvedCount', $n)->count()]);

        return view('profesor-panel', compact('rows', 'missions', 'kpis', 'missionStats', 'distribution'));
    }

    public function export()
    {
        if (! session('is_teacher')) {
            return redirect()->route('profesor.login');
        }
        $missions = Mission::orderBy('orden')->get();
        $rows = $this->buildRows($missions);

        $filename = 'cyberquest-resultados-'.now()->format('Ymd-Hi').'.csv';

        return response()->streamDownload(function () use ($rows, $missions) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM para que Excel respete las tildes
            $header = ['Posición', 'Nombre', 'ID', 'Correo', 'Puntos', 'Misiones'];
            foreach ($missions as $m) {
                $header[] = 'M'.$m->orden.' resuelta';
                $header[] = 'M'.$m->orden.' intentos';
            }
            array_push($header, 'Intentos totales', 'Precisión %', 'Inicio', 'Última actividad', 'Finalizó');
            fputcsv($out, $header, ';');

            foreach ($rows as $row) {
                $s = $row['student'];
                $line = [$row['rank'], $s->nombre, $s->uid, $s->correo, $row['points'], $row['solvedCount']];
                foreach ($missions as $m) {
                    $line[] = isset($row['solved'][$m->id]) ? $row['solved'][$m->id]->format('d/m/Y H:i') : 'No';
                    $line[] = $row['perMission'][$m->id];
                }
                array_push($line, $row['attempts'], $row['accuracy'],
                    optional($s->started_at)->format('d/m/Y H:i'),
                    optional($row['lastActivity'])->format('d/m/Y H:i'),
                    $s->finished ? 'Sí' : 'No');
                fputcsv($out, $line, ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function allowReentry(Student $student)
    {
        if (! session('is_teacher')) {
            return redirect()->route('profesor.login');
        }
        $student->update(['allow_reentry' => true]);

        return back()->with('notice', "Reingreso habilitado para {$student->uid}. Debe registrarse de nuevo con el mismo ID y correo.");
    }

    public function destroy(Student $student)
    {
        if (! session('is_teacher')) {
            return redirect()->route('profesor.login');
        }
        $student->delete();

        return back()->with('notice', "Se eliminó a {$student->uid} y todo su progreso.");
    }

    public function logout()
    {
        session()->forget('is_teacher');

        return redirect()->route('landing');
    }

    /**
     * Una fila por estudiante con todo lo que necesita el dashboard,
     * ordenada como un marcador de CTF: más puntos primero y, en empate,
     * quien llegó antes a su última misión resuelta.
     */
    private function buildRows(Collection $missions): Collection
    {
        $points = $missions->pluck('puntos', 'id');
        $activeWindow = now()->subMinutes(15);

        $rows = Student::with('submissions')->get()->map(function ($s) use ($missions, $points, $activeWindow) {
            $subs = $s->submissions->sortBy('created_at');
            $solved = $subs->where('correcta', true)->groupBy('mission_id')
                ->map(fn ($g) => $g->first()->created_at);
            $perMission = $missions->mapWithKeys(fn ($m) => [$m->id => $subs->where('mission_id', $m->id)->count()]);
            $lastActivity = $subs->last()?->created_at ?? $s->started_at;
            $lastSolve = $solved->max();

            if ($s->finished) {
                $status = 'finalizo';
            } elseif ($lastActivity && $lastActivity->greaterThan($activeWindow)) {
                $status = 'activo';
            } else {
                $status = 'inactivo';
            }

            return [
                'student' => $s,
                'solved' => $solved->all(),
                'solvedCount' => $solved->count(),
                'perMission' => $perMission->all(),
                'points' => $solved->keys()->sum(fn ($id) => $points[$id] ?? 0),
                'attempts' => $subs->count(),
                'correct' => $subs->where('correcta', true)->count(),
                'accuracy' => $subs->count() ? (int) round($subs->where('correcta', true)->count() / $subs->count() * 100) : 0,
                'lastActivity' => $lastActivity,
                'lastSolve' => $lastSolve,
                'elapsedMinutes' => $s->started_at && $lastSolve ? (int) abs($s->started_at->diffInMinutes($lastSolve)) : null,
                'finished' => $s->finished,
                'status' => $status,
            ];
        });

        return $rows->sort(function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] <=> $a['points'];
            }
            if ($a['lastSolve'] && $b['lastSolve']) {
                return $a['lastSolve'] <=> $b['lastSolve'];
            }

            return strcmp($a['student']->nombre, $b['student']->nombre);
        })->values()->map(function ($row, $i) {
            $row['rank'] = $i + 1;

            return $row;
        });
    }
}
