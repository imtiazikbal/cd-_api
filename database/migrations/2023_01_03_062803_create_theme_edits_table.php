<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThemeEditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme_edits', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('menu');
            $table->text('content');
            $table->string('logo')->nullable();
            $table->string('page');
            $table->string('theme');
            $table->string('type');
            $table->unsignedInteger('shop_id');
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
        Schema::dropIfExists('theme_edits');
    }
}
