<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePasswordAndPinCodeToKeyInPasswordHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('password_histories', function (Blueprint $table) {
            $table->renameColumn('password', 'key');
        });

        Schema::table('pin_code_histories', function (Blueprint $table) {
            $table->renameColumn('pin_code', 'key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_histories', function (Blueprint $table) {
            $table->renameColumn('key', 'password');
        });

        Schema::table('pin_code_histories', function (Blueprint $table) {
            $table->renameColumn('key', 'pin_code');
        });
    }
}
