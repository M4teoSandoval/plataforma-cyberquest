<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = ['student_id', 'code', 'issued_at'];

    protected $casts = ['issued_at' => 'datetime'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Genera un código público único con el formato CG-2026-XXXXXXXX.
     * No deriva de datos privados del estudiante y se comprueba contra
     * la tabla para garantizar que no se repita.
     */
    public static function generateCode(): string
    {
        do {
            $code = 'CG-2026-'.strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
