<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminCouriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('courier', ['pathao', 'redx', 'steadfast']);
            $table->string('email');
            $table->string('password');
            $table->text('config')->nullable();
            $table->string('notice')->nullable();
            $table->unique(['courier', 'email']);
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
        Schema::dropIfExists('admin_couriers');
    }
}
