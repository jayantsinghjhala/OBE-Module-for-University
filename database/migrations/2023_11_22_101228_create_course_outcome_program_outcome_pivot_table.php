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
        Schema::create('course_outcome_program_outcome', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_outcome_id');
            $table->unsignedBigInteger('program_outcome_id');
            $table->unsignedTinyInteger('strength')->nullable()->default(1); // Adding 'strength' column
            $table->timestamps();

            $table->foreign('course_outcome_id')->references('id')->on('course_outcomes')->onDelete('cascade');
            $table->foreign('program_outcome_id')->references('id')->on('program_outcomes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_outcome_program_outcome_pivot');
    }
};
