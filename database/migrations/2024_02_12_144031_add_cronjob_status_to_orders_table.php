<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCronjobStatusToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('cronjob_status', ['n', 'p'])
                ->after('status_update_date')->default('n')
                ->comment('n=normal, p=processing');
            $table->index('updated_at'); // Index for faster query
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('cronjob_status');
            $table->dropIndex(['updated_at']);
        });
    }
}
