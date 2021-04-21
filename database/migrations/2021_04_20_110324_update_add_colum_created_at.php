<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAddColumCreatedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tiers', function (Blueprint $table) {
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->after('user_updated');

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

        });
    }
}

