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
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_detail_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('obtained_marks', 8, 2)->unsigned();
            // Add other necessary columns as needed

            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('assessment_detail_id')->references('id')->on('assessment_details')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            // Add other necessary columns as needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_marks');
    }
};
