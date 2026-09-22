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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->integer('id', true); // INT con Auto Increment
            $table->string('name', 50); // VARCHAR(50) -> Ej: "3° A Secundaria"
            $table->char('grade', 2)->nullable(); // CHAR(2) -> Ej: "3°"
            $table->char('section', 1)->nullable(); // CHAR(1) -> Ej: "A"
            $table->integer('total_capacity')->default(30); // INT -> Límite/margen de estudiantes del aula
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};