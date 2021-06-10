<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tier_approvals', function (Blueprint $table) {
            //
            $table->uuid('verified_by')->nullable()->after('remarks');
            $table->uuid('approved_by')->nullable()->after('remarks');
            $table->uuid('declined_by')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tier_approvals', function (Blueprint $table) {
            //
        });
    }
}
