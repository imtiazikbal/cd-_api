<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shop_id');
            $table->text('title');
            $table->string('order_title')->default('তাই আর দেরি না করে আজই অর্ডার করুন');
            $table->string('slug');
            $table->string('video_link')->nullable();
            $table->longText('page_content')->nullable();
            $table->longText('descriptions')->nullable();
            $table->unsignedBigInteger('theme')->default(1);
            $table->unsignedBigInteger('status')->default(0);
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
        Schema::dropIfExists('pages');
    }
}
