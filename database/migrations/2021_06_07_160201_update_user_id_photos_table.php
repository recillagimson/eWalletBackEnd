<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserIdPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_id_photos', function (Blueprint $table) {
            $table->uuid('tier_approval_id')->nullable()->after('id');
            $table->string('remarks')->nullable()->after('approval_status');
            $table->dateTime('reviewed_date')->nullable()->after('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_id_photos', function (Blueprint $table) {
            $table->dropColumn('tier_approval_id');
            $table->dropColumn('remarks');
            $table->dropColumn('reviewed_date');
        });
    }
}
