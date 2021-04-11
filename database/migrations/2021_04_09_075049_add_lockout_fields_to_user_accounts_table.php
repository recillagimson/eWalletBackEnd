<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLockoutFieldsToUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->boolean('is_lockout')->default(false);
            $table->integer('login_failed_attempts')->unsigned()->default(0);
            $table->timestamp('last_failed_attempt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->dropColumn('is_lockout');
            $table->dropColumn('login_failed_attempts');
            $table->dropColumn('last_failed_attempt');
        });
    }
}
