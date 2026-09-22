<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        // Configura Faker para generar nombres y apellidos en español
        $faker = \Faker\Factory::create('es_ES');
        
        $firstName = $faker->firstName();
        $lastName = $faker->lastName() . ' ' . $faker->lastName();
        $fullName = $firstName . ' ' . $lastName;
        
        // Genera un correo único a partir de las iniciales o nombre
        $emailName = strtolower(substr($firstName, 0, 1) . str_replace(' ', '', $faker->lastName()));

        return [
            'name' => $fullName,
            'email' => $emailName . rand(10, 99) . '@tungasuca.edu.pe',
            // Agrega otros campos de tu tabla 'students' si aplica
        ];
    }
}