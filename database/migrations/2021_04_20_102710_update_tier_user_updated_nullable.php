<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTierUserUpdatedNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tiers', function (Blueprint $table) {
            $table->uuid('user_updated')->nullable()->change();
            $table->decimal('daily_limit', 18,6)->change();
            $table->decimal('daily_threshold', 18,6)->change();
            $table->decimal('monthly_limit', 18,6)->change();
            $table->decimal('monthly_threshold', 18,6)->change();
            $table->dropColumn('created_at');
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tiers', function (Blueprint $table) {
            $table->uuid('user_updated')->change();
        });
    }
}
