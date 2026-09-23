<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->string('uid')->unique();
            $t->string('nombre');
            $t->string('correo');
            $t->timestamp('started_at')->nullable();
            $t->boolean('finished')->default(false);
            $t->timestamp('finished_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
