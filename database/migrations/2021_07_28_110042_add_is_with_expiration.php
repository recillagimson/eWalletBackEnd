<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsWithExpiration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('id_types', function (Blueprint $table) {
            $table->string('is_with_expiration')->default(0)->after('is_ekyc');
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
            $table->dropColumn('is_with_expiration');
        });
    }
}
