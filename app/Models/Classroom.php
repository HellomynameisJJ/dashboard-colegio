<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'grade',
        'section',
        'total_capacity', // Agregado para permitir la asignación masiva de la capacidad
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}