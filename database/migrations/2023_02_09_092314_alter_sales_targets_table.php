<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSalesTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_targets', function (Blueprint $table) {
            $table->integer('custom')->default(0)->after('monthly')->nullable();
            $table->timestamp('from_date')->nullable()->default(null)->after('custom');
            $table->timestamp('to_date')->nullable()->default(null)->after('from_date');
        });

        if (Schema::hasColumn('sales_targets', 'weekly')) {
            Schema::table('sales_targets', function (Blueprint $table) {
                $table->dropColumn('weekly');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_targets', function (Blueprint $table) {
            $table->dropColumn('custom');
            $table->dropColumn('from_date');
            $table->dropColumn('to_date');
        });
    }
}
