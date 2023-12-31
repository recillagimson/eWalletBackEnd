<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSourceOfFundsNullableUserUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('source_of_funds', function (Blueprint $table) {
     
            $table->uuid('user_updated')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('source_of_funds', function (Blueprint $table) {
            $table->uuid('user_updated')->change();
        });
    }
}
