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
        Schema::create('course_outcomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id'); // Foreign key referencing courses table
            $table->string('name');
            $table->text('outcome_description');
            $table->boolean('status')->default(1);
            $table->decimal('max_marks_CIA', 8, 2)->unsigned()->nullable();
            $table->decimal('max_marks_ETA', 8, 2)->unsigned()->nullable();
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            // Add other necessary columns as needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_outcomes');
    }
};
