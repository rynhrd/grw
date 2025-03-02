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
    Schema::create('price_list', function (Blueprint $table) {
      $table->id();
      $table->string('deskripsi');
      $table->string('product');
      $table->string('cc_motor');
      $table->integer('price');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('price_list');
  }
};