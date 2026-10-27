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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->Text('message')->nullable();
            $table->integer('read_message')->default('0');
            $table->integer('from_user_id')->default('0');
            $table->integer('to_user_id')->default('0');
            $table->dateTime('send_at')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->string('channel', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('messages');
    }
};
