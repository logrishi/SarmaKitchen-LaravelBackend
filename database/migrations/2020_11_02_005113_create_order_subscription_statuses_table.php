<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSubscriptionStatusesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('order_subscription_statuses', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_id');
      $table->date('delivery_date')->nullable();
      $table->string('order_status', '100')->nullable();
      $table->integer('order_status_code')->nullable();
      $table->string('product_delivered', '191')->nullable();
      $table->string('delivered_by', '100')->nullable();
      $table->integer('otp')->nullable();
      $table->string('remarks', '255')->nullable();
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
    Schema::dropIfExists('order_subscription_statuses');
  }
}