<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOutSend2banksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_send2banks', function (Blueprint $table) {
            $table->string('account_name', 100)->change();
            $table->string('purpose', 100)->change();

            $table->renameColumn('transction_category_id', 'transaction_category_id');

            $table->dropColumn('pesonet_reference');
            $table->dropColumn('instapay_reference');


            $table->string('provider', '20');
            $table->string('provider_reference', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('out_send2banks', function (Blueprint $table) {
            $table->string('account_name', 50)->change();
            $table->string('purpose', 50)->change();

            $table->renameColumn('transaction_category_id', 'transction_category_id');

            $table->dropColumn('provider');
            $table->dropColumn('provider_reference');

            $table->string('pesonet_reference', 50)->nullable();
            $table->string('instapay_reference', 50)->nullable();
        });
    }
}
