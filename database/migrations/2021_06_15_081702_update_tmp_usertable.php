<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTmpUsertable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_user_details', function (Blueprint $table) {
            $table->string('name_extension', 255)->nullable()->change();
            $table->renameColumn('provice_state', 'province_state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_user_details', function (Blueprint $table) {
            $table->string('name_extension', 255)->change();
            $table->renameColumn('provice_state', 'province_state');
        });
    }
}
