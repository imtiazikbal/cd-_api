<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFraudNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fraud_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fraud_id')
                ->constrained('frauds')
                ->onDelete('cascade');
            $table->bigInteger('courier_uid')->nullable();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('mark_at')->nullable();
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
        Schema::dropIfExists('fraud_notes');
    }
}
