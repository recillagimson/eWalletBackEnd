<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingBalanceColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_balance_infos', function (Blueprint $table) {
            $table->decimal('pending_balance', $precision = 19, $scale = 6)->after('available_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_balance_infos', function (Blueprint $table) {
            $table->dropColumn('pending_balance');
        });
    }
}
