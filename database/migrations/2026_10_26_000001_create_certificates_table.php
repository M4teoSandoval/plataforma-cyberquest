<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $t) {
            $t->id();
            // Un certificado por estudiante: la relación es 1-a-1 y se reutiliza
            // en cada descarga para no duplicar documentos.
            $t->foreignId('student_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('code', 24)->unique();   // Identificador público CG-2026-XXXXXXXX
            $t->timestamp('issued_at');          // Fecha de emisión (primera generación)
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
