<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOutSendMoneyChangeAmountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_send_money', function (Blueprint $table) {
            $table->decimal('amount', $precision = 19, $scale = 6)->change();
            $table->decimal('total_amount', $precision = 19, $scale = 6)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('out_send_money', function (Blueprint $table) {
            $table->decimal('amount')->change();
            $table->decimal('total_amount')->change();
        });
    }
}
