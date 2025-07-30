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
       Schema::create('credential_display_settings', function (Blueprint $table) {
    $table->id();
    $table->string('section')->unique(); // 'voucher', 'satp', 'schoology', 'kumosoft', 'email'
    $table->boolean('is_enabled')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credential_display_settings');
    }
};
