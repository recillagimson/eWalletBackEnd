<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReaddAdditionalColumnsAsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('id_number')->nullable();
            $table->string('government_id_type')->nullable();
            $table->string('district')->nullable();
            $table->string('region')->nullable();
            $table->string('sex')->nullable();
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
            $table->dropColumn('id_number');
            $table->dropColumn('government_id_type');
            $table->dropColumn('district');
            $table->dropColumn('region');
            $table->dropColumn('sex');
        });
    }
}
