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
        Schema::dropIfExists('students'); // Ensure the students table does not already exist

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->unsignedBigInteger('course_id');
            $table->date('birthday'); // Add the birthday column
            $table->string('status');
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('course')->onDelete('cascade');
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
