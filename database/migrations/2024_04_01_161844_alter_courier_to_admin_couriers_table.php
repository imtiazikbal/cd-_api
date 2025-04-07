<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterCourierToAdminCouriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE admin_couriers MODIFY COLUMN courier ENUM('pathao', 'redx', 'steadfast', 'steadfastcourier')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE admin_couriers MODIFY COLUMN courier ENUM('pathao', 'redx', 'steadfast')");
    }
}
