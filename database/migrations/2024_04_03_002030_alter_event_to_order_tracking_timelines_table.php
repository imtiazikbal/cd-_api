<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterEventToOrderTrackingTimelinesTable extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE order_tracking_timelines MODIFY COLUMN event ENUM('advance', 'cancelled', 'confirmed', 'discount', 'pending', 'shipped')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE order_tracking_timelines MODIFY COLUMN event ENUM('advance', 'confirmed', 'discount', 'pending', 'shipped')");
    }
}
