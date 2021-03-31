<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->string('entity_id', 50)->after('id');
            $table->string('merchant_id', 50)->after('entity_id');
            $table->string('username', 100)->after('merchant_id');
            $table->boolean('ismerchant')->nullable()->after('password');
            $table->uuid('merchant_type_id')->after('ismerchant');
            $table->datetime('old_creation_date_time_from_v3_DB')->after('status');

            $table->dropColumn('swiVerificationStatus');
            $table->dropColumn('swiUserAccountStatus');
            $table->dropColumn('swiUserLoginStatus');
            $table->dropColumn('swiReportExceptionStatus');

            $table->renameColumn('pinCode', 'pin_code');
            $table->renameColumn('isAdmin', 'isAdmin');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_accounts', function($table) {

            $table->renameColumn('pin_code', 'pinCode');
            $table->renameColumn('isadmin', 'isAdmin');


        });
    }
}
