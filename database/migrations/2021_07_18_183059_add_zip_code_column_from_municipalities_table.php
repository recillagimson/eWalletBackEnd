<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZipCodeColumnFromMunicipalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('municipalities', function (Blueprint $table) {
            $table->string('zip_code')->after('municipality_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('municipalities', function (Blueprint $table) {
            $table->dropColumn('zip_code');
        });
    }
}
