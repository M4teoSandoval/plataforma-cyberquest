<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['uid', 'nombre', 'correo', 'started_at', 'finished', 'finished_at', 'allow_reentry', 'reentry_requested_at'];

    protected $casts = [
        'started_at' => 'datetime', 'finished_at' => 'datetime',
        'finished' => 'boolean', 'allow_reentry' => 'boolean',
        'reentry_requested_at' => 'datetime',
    ];

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function completedMissionIds()
    {
        return $this->submissions()->where('correcta', true)->pluck('mission_id')->unique();
    }

    /**
     * Número de misiones realmente resueltas (flags validadas por el backend).
     */
    public function solvedCount(): int
    {
        return $this->completedMissionIds()->count();
    }

    /**
     * ¿Completó la operación completa? Se compara contra el total real de
     * misiones registradas, no contra una constante.
     */
    public function hasCompletedOperation(): bool
    {
        return $this->solvedCount() >= Mission::count() && Mission::count() > 0;
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
