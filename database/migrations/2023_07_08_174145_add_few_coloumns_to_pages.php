<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFewColoumnsToPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('logo')->nullable();
            $table->string('fb')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->longText('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('footer_text_color')->nullable();
            $table->string('footer_link_color')->nullable();
            $table->string('footer_b_color')->nullable();
            $table->string('footer_heading_color')->nullable();
            $table->string('checkout_text_color')->nullable();
            $table->string('checkout_link_color')->nullable();
            $table->string('checkout_b_color')->nullable();
            $table->string('checkout_button_color')->nullable();
            $table->string('checkout_button_text_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            //
        });
    }
}
