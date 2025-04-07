<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFraudsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('frauds', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->enum('courier', ['pathao', 'redx', 'steadfast']);
            $table->integer('orders')->default(0);
            $table->integer('delivered')->default(0);
            $table->integer('cancelled')->default(0);
            $table->double('cancel_percent')->default(0);
            $table->double('success_percent')->default(100);
            $table->unique(['number', 'courier']);
            $table->index('number');
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
        Schema::dropIfExists('frauds');
    }
}
