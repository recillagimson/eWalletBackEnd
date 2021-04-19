<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInReceiveMoneyChangeAmountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_receive_money', function (Blueprint $table) {
            $table->decimal('amount', $precision = 19, $scale = 6)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_receive_money', function (Blueprint $table) {
            $table->decimal('amount')->change();
        });
    }
}
