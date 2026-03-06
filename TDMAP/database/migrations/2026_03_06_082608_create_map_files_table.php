<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('map_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id');

            $table->string('dc_moi')->nullable();
            $table->string('dc_cu')->nullable();
            $table->string('quy_hoach')->nullable();

            $table->string('tinh')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_files');
    }
};
