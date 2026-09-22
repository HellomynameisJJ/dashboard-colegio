<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', // Agregado para el CHAR(10) único del alumno
        'name',
        'email',
        'classroom_id',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function competencyResults()
    {
        return $this->hasMany(StudentCompetencyResult::class);
    }
}