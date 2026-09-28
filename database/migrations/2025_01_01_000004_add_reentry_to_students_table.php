<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $t) {
            $t->unique('correo');
            $t->boolean('allow_reentry')->default(false)->after('finished_at');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $t) {
            $t->dropUnique(['correo']);
            $t->dropColumn('allow_reentry');
        });
    }
};
