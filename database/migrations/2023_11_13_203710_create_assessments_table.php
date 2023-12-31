<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('assessment_name', 100);
            $table->enum('assessment_type', ['CIA', 'ETA']);
            $table->unsignedBigInteger('user_id'); // Foreign key column

            // Define the foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Drop the foreign key constraint before dropping the table
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('assessments');
    }
};
