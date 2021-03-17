<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableStatusAndPinCodeInUserAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('userAccounts', function (Blueprint $table) {
            $table->string('status', 20)->nullable()->change();
            $table->string('pinCode', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('userAccounts', function (Blueprint $table) {
            $table->string('status', 50)->change();
            $table->string('pinCode', 50)->change();
        });
    }
}
