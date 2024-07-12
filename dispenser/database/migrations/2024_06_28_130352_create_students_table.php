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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->foreignId('course_id')->constrained('course');
            $table->string('birthday');
            $table->boolean('status');
            $table->string('voucher_id')->nullable();
            $table->string('email_id')->nullable();
            $table->timestamps();
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
