<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInReceiveMoneyForeignKeysUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_receive_money', function (Blueprint $table) {
            $table->foreign('user_account_id')->references('id')->on('user_accounts')->after('user_account_id');
            $table->foreign('sender_id')->references('id')->on('user_accounts')->after('sender_id');
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
        Schema::table('in_receive_money', function (Blueprint $table) {
            $table->string('message', 50)->change();
            $table->string('transaction_remarks', 100)->change();
            $table->uuid('user_created', 100)->change();
            $table->uuid('user_updated', 100)->change();
        });
    }
}
