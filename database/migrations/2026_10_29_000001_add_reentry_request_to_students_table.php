<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $t) {
            // Marca cuándo el estudiante pidió reingreso desde el formulario.
            // El profesor lo aprueba activando allow_reentry; al entrar se limpia.
            $t->timestamp('reentry_requested_at')->nullable()->after('allow_reentry');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $t) {
            $t->dropColumn('reentry_requested_at');
        });
    }
};
