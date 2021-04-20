<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTierInServiceFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_fees', function (Blueprint $table) {
            $table->renameColumn('tier', 'tier_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_fees', function (Blueprint $table) {
            $table->renameColumn('tier_id', 'tier');
        });
    }
}
