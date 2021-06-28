<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateDatetimeFieldsInDrcrMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drcr_memos', function (Blueprint $table) {
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('declined_at')->nullable();
            $table->timestamps();
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
            $table->dropColumn('approved_at');
            $table->dropColumn('declined_at');
            $table->dropTimestamps();
        });
    }
}
