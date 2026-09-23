<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['uid', 'nombre', 'correo', 'started_at', 'finished', 'finished_at'];

    protected $casts = ['started_at' => 'datetime', 'finished_at' => 'datetime', 'finished' => 'boolean'];

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function completedMissionIds()
    {
        return $this->submissions()->where('correcta', true)->pluck('mission_id')->unique();
    }

    public function points()
    {
        return Mission::whereIn('id', $this->completedMissionIds())->sum('puntos');
    }

    public function attemptsCount()
    {
        return $this->submissions()->count();
    }
}
