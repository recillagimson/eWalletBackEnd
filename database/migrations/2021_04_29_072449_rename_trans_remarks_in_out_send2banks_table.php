<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTransRemarksInOutSend2banksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_send2banks', function (Blueprint $table) {
            $table->dropColumn('transaction_remarks');
            $table->json('transaction_response')->nullable();
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
            $table->string('transaction_remarks', 100);
            $table->dropColumn('transaction_response');
        });
    }
}
