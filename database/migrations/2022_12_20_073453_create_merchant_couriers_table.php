<?php

use App\Models\MerchantCourier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantCouriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_couriers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('provider', ['steadfast', 'redx', 'pathao', 'paperfly', 'ecourier']);
            $table->string('status')->default(MerchantCourier::STATUS_ACTIVE);
            $table->text('config');
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
        Schema::dropIfExists('merchant_couriers');
    }
}
