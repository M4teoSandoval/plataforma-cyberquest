<?php

namespace App\Console\Commands;

use App\Models\Mission;
use App\Models\Student;
use App\Models\Submission;
use Illuminate\Console\Command;

/**
 * Utilidad SOLO para pruebas locales: marca las 5 misiones como resueltas
 * para un estudiante, de modo que puedas comprobar la pantalla final y la
 * descarga del certificado sin tener que resolver todo el CTF.
 *
 * No afecta a producción: aborta si el entorno no es "local". No altera flags,
 * hashes ni la lógica de validación; solo crea las submissions correctas.
 */
class CertTestComplete extends Command
{
    protected $signature = 'cert:test-complete {uid : ID del estudiante, ej. U00123456}';

    protected $description = '[local] Completa las 5 misiones de un estudiante para probar el certificado';

    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->error('Este comando solo puede ejecutarse en entorno local.');

            return self::FAILURE;
        }

        $uid = strtoupper(trim($this->argument('uid')));
        $student = Student::where('uid', $uid)->first();

        if (! $student) {
            $this->error("No existe un estudiante con ID {$uid}. Regístrate primero en la plataforma.");

            return self::FAILURE;
        }

        foreach (Mission::orderBy('orden')->get() as $m) {
            $yaResuelta = $student->submissions()
                ->where('mission_id', $m->id)->where('correcta', true)->exists();
            if (! $yaResuelta) {
                Submission::create([
                    'student_id' => $student->id, 'mission_id' => $m->id, 'correcta' => true,
                ]);
            }
        }

        $this->info("✓ {$student->nombre} ({$uid}) tiene las 5 misiones completadas.");
        $this->line('Abre /resultado en la plataforma y usa "DESCARGAR MI CERTIFICADO PDF".');

        return self::SUCCESS;
    }
}
