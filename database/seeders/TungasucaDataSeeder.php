<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Competency;
use App\Models\Student;
use App\Models\StudentCompetencyResult;
use Faker\Factory as Faker;

class TungasucaDataSeeder extends Seeder
{
    public function run(): void
    {
        // Instancia de Faker configurado para español de Perú / Latinoamérica
        $faker = Faker::create('es_PE');

        // 1. Crear Salones con su Capacidad/Margen Total
        $classroomA = Classroom::create([
            'name' => '3° A Secundaria',
            'grade' => '3°',
            'section' => 'A',
            'total_capacity' => 28,
        ]);

        $classroomB = Classroom::create([
            'name' => '4° B Secundaria',
            'grade' => '4°',
            'section' => 'B',
            'total_capacity' => 30,
        ]);

        // 2. Crear Cursos y Competencias (incluyendo el campo 'code' en ambos)
        $coursesData = [
            'MAT-101' => [
                'name' => 'Matemática',
                'competencies' => [
                    'CMP-001' => 'Resuelve problemas de cantidad',
                    'CMP-002' => 'Resuelve problemas de regularidad, equivalencia y cambio',
                    'CMP-003' => 'Resuelve problemas de forma, movimiento y localización',
                ]
            ],
            'COM-101' => [
                'name' => 'Comunicación',
                'competencies' => [
                    'CMP-004' => 'Se comunica oralmente en su lengua materna',
                    'CMP-005' => 'Lee diversos tipos de textos escritos',
                    'CMP-006' => 'Escribe diversos tipos de textos',
                ]
            ],
            'CTA-101' => [
                'name' => 'Ciencia y Tecnología',
                'competencies' => [
                    'CMP-007' => 'Indaga mediante métodos científicos para construir conocimientos',
                    'CMP-008' => 'Explica el mundo físico basándose en conocimientos científicos',
                ]
            ],
        ];

        $competenciesList = [];

        foreach ($coursesData as $courseCode => $courseInfo) {
            $course = Course::create([
                'code' => $courseCode,
                'name' => $courseInfo['name'],
            ]);

            foreach ($courseInfo['competencies'] as $compCode => $compName) {
                $competenciesList[] = Competency::create([
                    'code' => $compCode,
                    'course_id' => $course->id,
                    'name' => $compName,
                ]);
            }
        }

        // 3. Generar los 28 Estudiantes de 3° A con nombres reales
        for ($i = 1; $i <= 28; $i++) {
            $num = str_pad($i, 3, '0', STR_PAD_LEFT);

            // Generar Nombre y Apellidos reales automatizados
            $firstName = $faker->firstName();
            $lastName1 = $faker->lastName();
            $lastName2 = $faker->lastName();
            $fullName = "{$firstName} {$lastName1} {$lastName2}";

            // Correo dinámico basado en su nombre real
            $cleanName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $firstName));
            $cleanLastName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $lastName1));
            $email = "{$cleanName}.{$cleanLastName}@tungasuca.edu.pe";

            $student = Student::create([
                'code' => "EST3A{$num}",
                'name' => $fullName,
                'email' => $email,
                'classroom_id' => $classroomA->id,
            ]);

            // Asignar puntajes aleatorios a cada competencia
            foreach ($competenciesList as $competency) {
                StudentCompetencyResult::create([
                    'student_id' => $student->id,
                    'competency_id' => $competency->id,
                    'score' => rand(4500, 10000) / 100,
                ]);
            }
        }

        // 4. Generar los 30 Estudiantes de 4° B con nombres reales
        for ($i = 1; $i <= 30; $i++) {
            $num = str_pad($i, 3, '0', STR_PAD_LEFT);

            // Generar Nombre y Apellidos reales automatizados
            $firstName = $faker->firstName();
            $lastName1 = $faker->lastName();
            $lastName2 = $faker->lastName();
            $fullName = "{$firstName} {$lastName1} {$lastName2}";

            // Correo dinámico
            $cleanName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $firstName));
            $cleanLastName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $lastName1));
            $email = "{$cleanName}.{$cleanLastName}@tungasuca.edu.pe";

            $student = Student::create([
                'code' => "EST4B{$num}",
                'name' => $fullName,
                'email' => $email,
                'classroom_id' => $classroomB->id,
            ]);

            // Asignar puntajes aleatorios a cada competencia
            foreach ($competenciesList as $competency) {
                StudentCompetencyResult::create([
                    'student_id' => $student->id,
                    'competency_id' => $competency->id,
                    'score' => rand(4500, 10000) / 100,
                ]);
            }
        }
    }
}