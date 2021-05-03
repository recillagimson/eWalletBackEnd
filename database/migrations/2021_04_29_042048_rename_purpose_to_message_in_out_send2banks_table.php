<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePurposeToMessageInOutSend2banksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('out_send2banks', function (Blueprint $table) {
            $table->renameColumn('purpose', 'message');
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
            $table->renameColumn('message', 'purpose');
        });
    }
}
