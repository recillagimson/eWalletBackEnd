<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsForIdTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('id_types', function (Blueprint $table) {
            $table->integer('is_ekyc')->after('status')->default('0');
            $table->integer('is_full_name')->after('is_ekyc')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('id_types', function (Blueprint $table) {
            $table->dropColumn('is_ekyc');
            $table->dropColumn('is_full_name');
        });
    }
}
