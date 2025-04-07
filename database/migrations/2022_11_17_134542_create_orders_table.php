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
            $table->unsignedBigInteger('shop_id');
            $table->string('order_no');
            $table->string('customer_name');
            $table->string('phone');
            $table->text('address');
            $table->string('order_status')
                ->default('pending')
                ->comment('pending,confirmed,follow_up,shipped,delivery,returned,cancel');
            $table->boolean('cod')
                ->default(true);
            $table->string('delivery_location')
                ->nullable();
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
        Schema::dropIfExists('orders');
    }
}
