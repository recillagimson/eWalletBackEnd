<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingBalanceColumnToUserBalanceInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_balance_infos', function (Blueprint $table) {
            $table->decimal('available_balance', 19, 6)->default(0)->change();
            $table->decimal('pending_balance', 19, 6)->default(0);
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
            $table->decimal('available_balance', 19, 6)->change();
            $table->dropColumn('pending_balance');
        });
    }
}
