<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrepaidLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepaid_loads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('prepaid_type', 50);
            $table->string('network', 50);
            $table->string('reward_keyword', 50);
            $table->string('amax_keyword', 50);
            $table->decimal('amount', 10, 3);
            $table->boolean('status');
            $table->uuid('user_created');
            $table->uuid('user_updated')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prepaid_loads');
    }
}
