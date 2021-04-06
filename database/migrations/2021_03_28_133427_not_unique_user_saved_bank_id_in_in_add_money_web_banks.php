<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotUniqueUserSavedBankIdInInAddMoneyWebBanks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_web_banks', function (Blueprint $table) {
            $table->dropUnique('add_money_web_banks_user_saved_bank_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_add_money_web_banks', function (Blueprint $table) {
            $table->uuid('user_saved_bank_id')->unique()->change();
        });
    }
}
