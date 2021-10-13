<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDrcrMemosTableAddControlNumberId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drcr_memos', function (Blueprint $table) {
            $table->uuid('control_number_id')->nullable()->references('id')->on('drcr_memo_control_numbers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drcr_memos', function (Blueprint $table) {
            //
        });
    }
}
