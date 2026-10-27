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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->integer('att_type')->default('0');
            $table->string('img_original', 255)->nullable();
            $table->string('img_middle', 255)->nullable();
            $table->string('img_small', 255)->nullable();
            $table->string('mov', 255)->nullable();
            $table->string('doc', 255)->nullable();
            $table->integer('user_id')->default('0');
            $table->string('channel', 255)->nullable();                 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('attachments');
    }
};
