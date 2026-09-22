<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_competency_results', function (Blueprint $table) {
            $table->integer('id', true); // INT con Auto Increment
            $table->integer('student_id'); // INT -> Foránea para hacer match con students.id
            $table->integer('competency_id'); // INT -> Foránea para hacer match with competencies.id
            $table->decimal('score', 5, 2)->default(0.00); // DECIMAL(5,2) -> Porcentaje de 0.00 a 100.00
            $table->timestamps();

            // Definición explícita de llaves foráneas
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('competency_id')->references('id')->on('competencies')->onDelete('cascade');

            // Evita registros duplicados de un alumno para la misma competencia
            $table->unique(['student_id', 'competency_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_competency_results');
    }
};