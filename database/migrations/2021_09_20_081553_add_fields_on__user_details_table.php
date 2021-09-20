<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsOnUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('name_of_da_personel', 50)->nullable();
            $table->string('da_remarks', 50)->nullable();
            $table->boolean('is_da_update')->default(1)->nullable();
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
            $table->dropColumn('name_of_da_personel');
            $table->dropColumn('da_remarks');
            $table->dropColumn('is_da_update');
        });
    }
}
