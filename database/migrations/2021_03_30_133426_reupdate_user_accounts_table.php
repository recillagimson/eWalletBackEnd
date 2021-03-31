<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReupdateUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->string('entity_id')->nullable()->change();
            $table->string('merchant_id')->nullable()->change();
            $table->string('username')->nullable()->change();
            $table->boolean('ismerchant')->default(false)->change();
            $table->uuid('merchant_type_id')->nullable()->change();
            $table->datetime('old_creation_date_time_from_v3_DB')->nullable()->change();
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
            $table->string('entity_id', 50);
            $table->string('merchant_id', 50);
            $table->string('username', 100);
            $table->boolean('ismerchant')->nullable();
            $table->uuid('merchant_type_id');
            $table->datetime('old_creation_date_time_from_v3_DB');
        });
    }
}
