<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableEmailAndMobileNumberInUserAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('userAccounts', function (Blueprint $table) {
            $table->dropIndex('useraccounts_email_unique');
            $table->dropIndex('useraccounts_mobilenumber_unique');
            $table->string('email', '50')->nullable()->change();
            $table->string('mobileNumber', '20')->nullable()->change();
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
            $table->string('email', '50')->unique()->change();
            $table->string('mobileNumber', '20')->unique()->change();
        });
    }
}
