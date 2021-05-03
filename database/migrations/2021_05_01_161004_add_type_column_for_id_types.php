<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeColumnForIdTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('id_types', function (Blueprint $table) {
            $table->renameColumn('swirecommended', 'is_primary')->default('1 = primary, 0 = secondary');
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
            $table->renameColumn('is_primary', 'swirecommended');
        });
    }
}
