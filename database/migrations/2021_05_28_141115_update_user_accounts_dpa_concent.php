<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserAccountsDpaConcent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            //
            $table->boolean('is_accept_dpa_consent')->default(true)->nullable()->after('is_active');
            $table->timestamp('accept_dpa_consent_date')->default(DB::raw('CURRENT_TIMESTAMP'))->after('is_active');
            $table->boolean('is_accept_tac_consent')->default(true)->nullable()->after('is_active');
            $table->timestamp('accept_tac_consent_date')->default(DB::raw('CURRENT_TIMESTAMP'))->after('is_active');
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
            //
        });
    }
}
