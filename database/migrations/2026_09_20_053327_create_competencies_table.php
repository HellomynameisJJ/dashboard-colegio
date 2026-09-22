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
        Schema::create('competencies', function (Blueprint $table) {
            $table->integer('id', true); // INT con Auto Increment
            $table->char('code', 8)->unique(); // CHAR(8) -> Código único de la competencia (ej: "COMP-01")
            $table->integer('course_id'); // INT -> Foránea para hacer match con courses.id
            $table->string('name', 150); // VARCHAR(150) -> Ej: "Resuelve problemas de cantidad"
            $table->timestamps();

            // Definición explícita de llave foránea
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competencies');
    }
};