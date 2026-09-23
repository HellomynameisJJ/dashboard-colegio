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
            // Se usa integer para coincidir exactamente con el id de classrooms
            $table->integer('id', true); 
            $table->char('code', 10)->unique();
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            
            // INT para hacer match perfecto con classrooms.id
            $table->integer('classroom_id'); 
            $table->timestamps();

            // Llave foránea explícita
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