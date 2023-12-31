<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('enrollment_number')->unique();
            $table->string('student_name');
            $table->string('session'); // e.g., 2020-2024
            $table->unsignedBigInteger('program_id'); // Foreign key to the programs table
            $table->tinyInteger('semester'); // Use a small integer to represent semesters (1 to 8)
            // Add other fields as needed

            $table->foreign('program_id')->references('id')->on('programs');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};