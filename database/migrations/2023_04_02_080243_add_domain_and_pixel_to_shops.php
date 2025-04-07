<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainAndPixelToShops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('domain_verify')->nullable();
            $table->string('domain_request')->nullable();
            $table->enum('domain_status', ['pending', 'connected', 'rejected'])->default(null)->nullable();
            $table->string('fb_pixel')->nullable();
            $table->string('c_api')->nullable();
            $table->boolean('c_status')->default(false);
            $table->string('test_event')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('domain_verify');
            $table->dropColumn('domain_request');
            $table->dropColumn('domain_status');
            $table->dropColumn('fb_pixel');
            $table->dropColumn('c_api');
            $table->dropColumn('c_status');
            $table->dropColumn('test_event');
        });
    }
}
