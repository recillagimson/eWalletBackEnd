<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecipientNumberInDrcrMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drcr_memos', function (Blueprint $table) {
            $table->string('recipient_number', 30)->nullable();
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
            $table->dropColumn('recipient_number', 30);
        });
    }
}
