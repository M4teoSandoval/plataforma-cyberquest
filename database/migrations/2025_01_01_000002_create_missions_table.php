<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $t) {
            $t->id();
            $t->unsignedTinyInteger('orden')->unique();
            $t->string('titulo');
            $t->text('narrativa');
            $t->text('objetivo');
            $t->text('pista');
            $t->string('insignia');
            $t->unsignedInteger('puntos');
            $t->string('flag_hash', 64);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
