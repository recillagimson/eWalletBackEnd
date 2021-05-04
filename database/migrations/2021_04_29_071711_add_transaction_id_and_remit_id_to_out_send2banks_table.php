<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionIdAndRemitIdToOutSend2banksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_send2banks', function (Blueprint $table) {
            $table->renameColumn('provider_reference', 'provider_transaction_id');
            $table->string('provider_remittance_id', 50)->nullable();
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
            $table->renameColumn('provider_transaction_id', 'provider_reference');
            $table->dropColumn('provider_remittance_id');
        });
    }
}
