<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOutSendMoneyForeignKeysUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_send_money', function (Blueprint $table) {
            $table->foreign('user_account_id')->references('id')->on('user_accounts')->after('user_account_id');
            $table->foreign('receiver_id')->references('id')->on('user_accounts')->after('receiver_id');
            // $table->foreign('service_fee_id')->references('id')->on('service_fee')->after('service_fee_id');
            // $table->foreign('purpose_of_transfer_id')->references('id')->on('purposes_of_transfer')->after('purpose_of_transfer_id');
            $table->foreign('transction_category_id')->references('id')->on('transaction_categories')->after('transction_category_id');
            $table->string('message', 50)->nullable()->change();
            $table->string('transaction_remarks', 100)->nullable()->change();
            $table->uuid('user_created', 100)->nullable()->change();
            $table->uuid('user_updated', 100)->nullable()->change();
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

            $table->string('message', 50)->change();
            $table->string('transaction_remarks', 100)->change();
            $table->uuid('user_created', 100)->change();
            $table->uuid('user_updated', 100)->change();
        });
    }
}
