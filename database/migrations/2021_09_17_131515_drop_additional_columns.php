<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAdditionalColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('id_number');
            $table->dropColumn('government_id_tpe');
            $table->dropColumn('district');
            $table->dropColumn('region');
            $table->dropColumn('sex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('id_number');
            $table->string('government_id_tpe');
            $table->string('district');
            $table->string('region');
            $table->string('sex');
        });
    }
}
