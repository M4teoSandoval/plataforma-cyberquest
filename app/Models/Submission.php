<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['student_id', 'mission_id', 'correcta'];

    protected $casts = ['correcta' => 'boolean'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
}
