<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', // Agregado CHAR(8) para la sigla del curso
        'name',
    ];

    public function competencies()
    {
        return $this->hasMany(Competency::class);
    }
}