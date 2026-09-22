<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCompetencyResult extends Model
{
    use HasFactory;

    // Se especifica el nombre exacto de la tabla en la BD
    protected $table = 'student_competency_results';

    protected $fillable = [
        'student_id',
        'competency_id',
        'score',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }
}