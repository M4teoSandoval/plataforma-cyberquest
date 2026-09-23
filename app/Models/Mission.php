<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    protected $fillable = ['orden', 'titulo', 'narrativa', 'objetivo', 'pista', 'insignia', 'puntos', 'flag_hash'];

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
