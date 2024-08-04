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
      Schema::create('stops', function (Blueprint $table) {
          $table->id();
          $table->foreignId('day_id')->constrained()->onDelete('cascade');
          $table->string('name');
          $table->text('description')->nullable();
          $table->time('start_time')->nullable();
          $table->time('end_time')->nullable();
          $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stops');
    }
};
