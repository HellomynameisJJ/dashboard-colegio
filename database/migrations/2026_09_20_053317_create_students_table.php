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
        Schema::create('students', function (Blueprint $table) {
            $table->integer('id', true); // INT con Auto Increment
            $table->char('code', 10)->unique(); // CHAR(10) -> Código único del estudiante (ej: "EST2026001")
            $table->string('name', 100); // VARCHAR(100) -> Nombre completo
            $table->string('email', 100)->nullable(); // VARCHAR(100) -> Correo institucional
            $table->integer('classroom_id'); // INT -> Foránea para hacer match con classrooms.id
            $table->timestamps();

            // Definición explícita de llave foránea
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};