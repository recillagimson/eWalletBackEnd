<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ServiceFeeIdNullabelInInAddMoneyBpiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_bpi', function (Blueprint $table) {
            $table->uuid('service_fee_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_add_money_bpi', function (Blueprint $table) {
            $table->uuid('service_fee_id')->change();
        });
    }
}
