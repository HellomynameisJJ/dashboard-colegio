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
        Schema::create('courses', function (Blueprint $table) {
            $table->integer('id', true); // INT con Auto Increment
            $table->char('code', 8)->unique(); // CHAR(8) -> Código/sigla del curso (ej: "MAT-101", "COM-202")
            $table->string('name', 80); // VARCHAR(80) -> Ej: "Matemática", "Comunicación"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};