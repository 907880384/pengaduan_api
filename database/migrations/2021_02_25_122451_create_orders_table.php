<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->unsignedBigInteger('qty')->nullable()->default(null);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status')->nullable()->default(null);
            $table->date('order_date')->nullable()->default(null);
            $table->unsignedBigInteger('complaint_id')->nullable();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
