<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankNameToOutSend2banksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_send2banks', function (Blueprint $table) {
            $table->dropColumn('notify_type');
            $table->renameColumn('notify_to', 'send_receipt_to');
            $table->string('bank_code', 20);
            $table->string('bank_name', 150);
            $table->string('purpose', 50);
            $table->string('other_purpose', 50)->nullable();
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
            $table->string('notify_type', 20)->nullable();
            $table->renameColumn('send_receipt_to', 'notify_to');
            $table->dropColumn('bank_code');
            $table->dropColumn('bank_name');
            $table->dropColumn('purpose');
            $table->dropColumn('other_purpose');
        });
    }
}
