<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoologyCredentialsTable extends Migration
{
    public function up()
    {
        Schema::create('schoology_credentials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->text('schoology_credentials'); // Change to json if you prefer structure
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schoology_credentials');
    }
}
