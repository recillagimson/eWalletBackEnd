<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyServiceFeeInOutPayMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_pay_merchants', function (Blueprint $table) {
            $table->decimal('service_fee')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('out_pay_merchants', function (Blueprint $table) {
            $table->decimal('service_fee')->change();
        });
    }
}
