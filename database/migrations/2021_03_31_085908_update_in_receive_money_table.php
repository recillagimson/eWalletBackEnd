<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInReceiveMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_receive_money', function (Blueprint $table) {
     
            $table->string('message', 50)->nullable()->after('amount');
            $table->string('status', 20)->change();
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
            $table->boolean('status')->change();
           
        });
    }
}
