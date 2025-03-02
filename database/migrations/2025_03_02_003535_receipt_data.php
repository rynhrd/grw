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
    Schema::create('receipt_tbl', function (Blueprint $table) {
      $table->id();
      $table->string('uuid');
      $table->string('nopol');
      $table->string('merek_motor');
      $table->string('price_list_deskripsi');
      $table->string('price_list_product');
      $table->string('cc_motor');
      $table->string('payment');
      $table->integer('price');
      $table->timestamps();
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