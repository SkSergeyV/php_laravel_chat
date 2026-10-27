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
        Schema::create('onlines', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 255)->nullable();
            $table->integer('user_id')->default('0');
            $table->integer('online')->default('0');     
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onlines');
    }
};
