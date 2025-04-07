<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckoutFormIdToActiveThemes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('active_themes', function (Blueprint $table) {
            $table->bigInteger('checkout_form_id')->nullable()->after('theme_id');
            $table->bigInteger('footer_id')->nullable()->after('checkout_form_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('active_themes', function (Blueprint $table) {
            $table->bigInteger('checkout_form_id')->nullable()->after('theme_id');
            $table->bigInteger('footer_id')->nullable()->after('checkout_form_id');
        });
    }
}
