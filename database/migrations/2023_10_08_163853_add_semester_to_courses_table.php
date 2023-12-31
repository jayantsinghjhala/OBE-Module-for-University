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
        // Define the enum values for the "semester" column
        $enumValues = ['1', '2', '3', '4', '5', '6', '7', '8'];

        // Add the "semester" column as an enum
        Schema::table('courses', function (Blueprint $table) use ($enumValues) {
            $table->enum('semester', $enumValues)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
