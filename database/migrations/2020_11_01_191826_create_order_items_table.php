<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('order_items', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_id');
      $table->string('product_id');
      $table->string('product_name', '100');
      $table->double('price');
      $table->integer('quantity');
      $table->string('size', '50')->nullable();
      $table->string('note', '100')->nullable();
      $table->boolean('is_veg')->nullable();
      $table->double('rating')->nullable();
      $table->string('meal_type', '100');
      $table->integer('subscription_duration');
      $table->integer('num_persons')->nullable();
      $table->date('start_date')->nullable();
      $table->date('end_date')->nullable();
      $table->json('subscription_menu')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('order_items');
  }
}