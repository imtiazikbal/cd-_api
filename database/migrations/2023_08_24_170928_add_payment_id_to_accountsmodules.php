<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentIdToAccountsmodules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accountsmodules', function (Blueprint $table) {
            $table->foreignId('payment_id')->default(1)->constrained('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accountsmodules', function (Blueprint $table) {
            $table->dropColumn('payment_id');
        });
    }
}
