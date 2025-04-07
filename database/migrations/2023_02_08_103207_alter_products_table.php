<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('delivery_charge', ['free', 'paid'])->default('free');
            $table->integer('inside_dhaka')->default(0);
            $table->integer('outside_dhaka')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('delivery_charge');
            $table->dropColumn('inside_dhaka');
            $table->dropColumn('outside_dhaka');
        });
    }
}
