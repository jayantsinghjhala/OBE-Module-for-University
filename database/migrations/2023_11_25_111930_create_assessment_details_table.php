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
        Schema::create('assessment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_assessment_id');
            $table->unsignedBigInteger('course_outcome_id');
            $table->integer('question_number')->unsigned(); // Represents the question number
            $table->decimal('marks', 8, 2)->unsigned();
            // Add other necessary columns as needed

            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('course_assessment_id')->references('id')->on('course_assessments')->onDelete('cascade');
            $table->foreign('course_outcome_id')->references('id')->on('course_outcomes')->onDelete('cascade');
            // Add other necessary columns as needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_details');
    }
};
