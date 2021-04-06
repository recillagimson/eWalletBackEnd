<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInAddMoneyWebBanks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_add_money_web_banks', function (Blueprint $table) {
            $table->renameColumn('user_saved_bank_id', 'online_bank_or_over_the_counter_list_id');
            $table->string('dragonpay_channel_reference_number', 20)->after('dragonpay_reference')->nullable();
            $table->string('status', 20)->after('transaction_remarks');
            $table->dropColumn('expires_at');
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
            $table->renameColumn('online_bank_or_over_the_counter_list_id', 'user_saved_bank_id');
            $table->dropColumn('dragonpay_channel_reference_number');
            $table->dropColumn('status');
            $table->timestamp('expires_at');
        });
    }
}
