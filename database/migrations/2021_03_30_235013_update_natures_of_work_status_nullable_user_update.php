<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNaturesOfWorkStatusNullableUserUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('natures_of_work', function (Blueprint $table) {
     
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
        Schema::table('natures_of_work', function (Blueprint $table) {
            $table->uuid('user_updated')->change();
        });
    }
}
