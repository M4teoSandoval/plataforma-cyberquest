<?php

namespace Tests\Feature;

use App\Models\Mission;
use App\Models\Student;
use App\Models\Submission;
use Database\Seeders\MissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CyberquestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MissionSeeder::class);
    }

    private function registro(array $overrides = [])
    {
        return $this->post('/empezar', array_merge([
            'nombre' => 'Ana Pérez', 'correo' => 'aperez@unab.edu.co', 'uid' => 'U00123456',
        ], $overrides));
    }

    public function test_un_estudiante_nuevo_se_registra(): void
    {
        $this->registro()->assertRedirect('/misiones');
        $this->assertDatabaseHas('students', ['uid' => 'U00123456', 'correo' => 'aperez@unab.edu.co']);
    }

    public function test_no_se_puede_reutilizar_un_id_registrado(): void
    {
        $this->registro();
        $this->flushSession();

        $this->registro(['nombre' => 'Intruso', 'correo' => 'otro@unab.edu.co', 'uid' => 'u00123456'])
            ->assertSessionHasErrors('uid');

        $this->assertSame('Ana Pérez', Student::where('uid', 'U00123456')->value('nombre'));
        $this->assertSame(1, Student::count());
    }

    public function test_no_se_puede_reutilizar_un_correo_con_otro_id(): void
    {
        $this->registro();
        $this->flushSession();

        $this->registro(['uid' => 'U00999999', 'correo' => 'APEREZ@unab.edu.co'])->assertSessionHasErrors('correo');
        $this->assertSame(1, Student::count());
    }

    public function test_el_reingreso_habilitado_conserva_el_progreso_y_se_consume(): void
    {
        $this->registro();
        $student = Student::first();
        Submission::create(['student_id' => $student->id, 'mission_id' => Mission::first()->id, 'correcta' => true]);
        $this->flushSession();

        $this->withSession(['is_teacher' => true])
            ->post("/profesor/estudiantes/{$student->id}/reingreso")->assertRedirect();
        $this->flushSession();

        // Con otro correo no entra aunque esté habilitado.
        $this->registro(['correo' => 'otro@unab.edu.co'])->assertSessionHasErrors('uid');

        $this->registro()->assertRedirect('/misiones');
        $this->assertFalse($student->fresh()->allow_reentry);
        $this->assertSame(1, $student->fresh()->submissions()->count());

        // El permiso es de un solo uso.
        $this->flushSession();
        $this->registro()->assertSessionHasErrors('uid');
    }

    public function test_el_dashboard_requiere_sesion_de_profesor(): void
    {
        $this->get('/profesor/panel')->assertRedirect('/profesor');
        $this->get('/profesor/exportar')->assertRedirect('/profesor');
        $s = Student::create(['uid' => 'U00000009', 'nombre' => 'Dani', 'correo' => 'd@unab.edu.co']);
        $this->post("/profesor/estudiantes/{$s->id}/reingreso")->assertRedirect('/profesor');
        $this->delete("/profesor/estudiantes/{$s->id}")->assertRedirect('/profesor');
        $this->assertFalse($s->fresh()->allow_reentry);
    }

    public function test_el_dashboard_muestra_clasificacion_y_exporta_csv(): void
    {
        $missions = Mission::orderBy('orden')->get();
        $a = Student::create(['uid' => 'U00000001', 'nombre' => 'Ana Uno', 'correo' => 'a@unab.edu.co', 'started_at' => now()->subHour()]);
        $b = Student::create(['uid' => 'U00000002', 'nombre' => 'Beto Dos', 'correo' => 'b@unab.edu.co', 'started_at' => now()->subHour()]);
        foreach ($missions->take(2) as $m) {
            Submission::create(['student_id' => $b->id, 'mission_id' => $m->id, 'correcta' => false]);
            Submission::create(['student_id' => $b->id, 'mission_id' => $m->id, 'correcta' => true]);
        }

        $this->withSession(['is_teacher' => true])->get('/profesor/panel')
            ->assertOk()
            ->assertSeeInOrder(['Beto Dos', 'Ana Uno'])
            ->assertSee('2.000');

        $csv = $this->withSession(['is_teacher' => true])->get('/profesor/exportar')->assertOk()->streamedContent();
        $this->assertStringContainsString('U00000002', $csv);
        $this->assertStringContainsString('1;"Beto Dos";U00000002;b@unab.edu.co;2000;2', $csv);
    }

    public function test_eliminar_estudiante_borra_su_progreso(): void
    {
        $s = Student::create(['uid' => 'U00000003', 'nombre' => 'Caro', 'correo' => 'c@unab.edu.co']);
        Submission::create(['student_id' => $s->id, 'mission_id' => Mission::first()->id, 'correcta' => true]);

        $this->withSession(['is_teacher' => true])->delete("/profesor/estudiantes/{$s->id}")->assertRedirect();
        $this->assertSame(0, Student::count());
        $this->assertSame(0, Submission::count());
    }
}
