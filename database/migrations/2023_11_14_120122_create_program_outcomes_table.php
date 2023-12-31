<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('program_outcomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->string('name'); // Allowing non-unique names per se, but uniqueness with program_id
            $table->text('outcome_description');
            $table->timestamps();
    
            // Creating a unique index for combination of program_id and name
            $table->unique(['program_id', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('program_outcomes');
    }
};
